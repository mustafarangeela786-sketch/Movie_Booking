<?php
/* ==========================================================
   api_search.php
   Live instant search endpoint for the floating search bar.
   ========================================================== */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['results' => []]);
    exit;
}

$like = '%' . $q . '%';
$stmt = $conn->prepare("
    SELECT movie_id, title, genre, poster, duration_minutes, release_date
    FROM movies
    WHERE title LIKE ? OR genre LIKE ? OR industry LIKE ?
    ORDER BY (title LIKE ?) DESC, (title = ?) DESC, created_at DESC
    LIMIT 6
");
$exact = $q;
$start_like = $q . '%';
$stmt->bind_param("sssss", $like, $like, $like, $start_like, $exact);
$stmt->execute();
$res = $stmt->get_result();

$results = [];
while ($m = $res->fetch_assoc()) {
    $results[] = [
        'id'          => (int)$m['movie_id'],
        'title'       => $m['title'],
        'genre'       => $m['genre'] ?: 'General',
        'poster'      => posterUrl($m),
        'duration'    => (int)$m['duration_minutes'],
        'is_coming'   => !empty($m['release_date']) && $m['release_date'] > date('Y-m-d'),
        'url'         => 'movie_details.php?id=' . (int)$m['movie_id']
    ];
}

echo json_encode(['results' => $results]);
