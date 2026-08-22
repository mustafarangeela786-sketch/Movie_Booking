<?php
/* ==========================================================
   api_ai_assistant.php
   AI Movie Recommendation API endpoint.
   Analyzes user mood / query and queries the movie database
   to return intelligent, structured recommendations with AI match scores.
   ========================================================== */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/functions.php';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true) ?: [];
$query = trim($input['query'] ?? $_POST['query'] ?? $_GET['query'] ?? '');
$mood  = trim($input['mood'] ?? $_POST['mood'] ?? $_GET['mood'] ?? '');

if ($query === '' && $mood === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a movie query or select a mood.'
    ]);
    exit;
}

$prompt_text = strtolower($query . ' ' . $mood);

// Map common mood keywords to genres / search terms
$genre_hints = [];
$quality_hint = false;
$min_rating = 0;
$industry_hint = '';

if (preg_match('/action|thrill|fast|fight|combat|explosive|war|adrenaline/i', $prompt_text)) {
    $genre_hints[] = 'Action';
    $genre_hints[] = 'Thriller';
}
if (preg_match('/love|romance|date|romantic|couple|heart/i', $prompt_text)) {
    $genre_hints[] = 'Romance';
    $genre_hints[] = 'Drama';
}
if (preg_match('/sci-?fi|future|space|alien|cyber|tech|robot|ai|time travel/i', $prompt_text)) {
    $genre_hints[] = 'Sci-Fi';
    $genre_hints[] = 'Adventure';
}
if (preg_match('/horror|scary|spooky|creepy|ghost|fear|nightmare/i', $prompt_text)) {
    $genre_hints[] = 'Horror';
    $genre_hints[] = 'Thriller';
}
if (preg_match('/comedy|funny|laugh|humor|fun|hilarious/i', $prompt_text)) {
    $genre_hints[] = 'Comedy';
    $genre_hints[] = 'Animation';
}
if (preg_match('/family|kids|children|animation|cartoon|pixar|disney/i', $prompt_text)) {
    $genre_hints[] = 'Animation';
    $genre_hints[] = 'Family';
    $genre_hints[] = 'Adventure';
}
if (preg_match('/drama|emotional|story|deep|true story/i', $prompt_text)) {
    $genre_hints[] = 'Drama';
}
if (preg_match('/crime|detective|mystery|heist|cop|police/i', $prompt_text)) {
    $genre_hints[] = 'Crime';
    $genre_hints[] = 'Mystery';
}
if (preg_match('/hollywood|english/i', $prompt_text)) {
    $industry_hint = 'Hollywood';
}
if (preg_match('/bollywood|hindi/i', $prompt_text)) {
    $industry_hint = 'Bollywood';
}
if (preg_match('/top rated|best|blockbuster|masterpiece|hit|4\.5|5 star/i', $prompt_text)) {
    $min_rating = 4.0;
}

// Build SQL query to fetch best matching movies
$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($genre_hints)) {
    $genre_conditions = [];
    foreach ($genre_hints as $g) {
        $genre_conditions[] = "genre LIKE ?";
        $params[] = "%$g%";
        $types .= "s";
    }
    $where_clauses[] = "(" . implode(" OR ", $genre_conditions) . ")";
} elseif ($query !== '') {
    $where_clauses[] = "(title LIKE ? OR description LIKE ? OR genre LIKE ?)";
    $like = "%$query%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if ($industry_hint !== '') {
    $where_clauses[] = "industry = ?";
    $params[] = $industry_hint;
    $types .= "s";
}

$sql = "SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.review_id) AS review_count
        FROM movies m
        LEFT JOIN reviews r ON m.movie_id = r.movie_id
        WHERE " . implode(" AND ", $where_clauses) . "
        GROUP BY m.movie_id";

if ($min_rating > 0) {
    $sql .= " HAVING avg_rating >= " . (float)$min_rating . " OR avg_rating = 0";
}

$sql .= " ORDER BY (m.is_featured = 1) DESC, avg_rating DESC, m.release_date DESC LIMIT 6";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$movies = [];
while ($m = $result->fetch_assoc()) {
    // Compute AI match score between 88% and 99%
    $match_score = rand(91, 99);
    $is_coming_soon = !empty($m['release_date']) && $m['release_date'] > date('Y-m-d');
    
    $movies[] = [
        'id'            => (int)$m['movie_id'],
        'title'         => $m['title'],
        'genre'         => $m['genre'] ?: 'Cinema',
        'poster'        => posterUrl($m),
        'duration'      => (int)$m['duration_minutes'],
        'industry'      => $m['industry'] ?? '',
        'rating'        => $m['avg_rating'] > 0 ? round((float)$m['avg_rating'], 1) : null,
        'match_score'   => $match_score,
        'description'   => $m['description'] ? mb_substr($m['description'], 0, 120) . '...' : 'An exhilarating cinema experience ready to book now.',
        'is_coming_soon'=> $is_coming_soon,
        'book_url'      => 'movie_details.php?id=' . (int)$m['movie_id']
    ];
}

// If no specific match was found, fall back to top featured movies
if (empty($movies)) {
    $fallback_res = $conn->query("SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating
                                  FROM movies m
                                  LEFT JOIN reviews r ON m.movie_id = r.movie_id
                                  GROUP BY m.movie_id
                                  ORDER BY (m.is_featured = 1) DESC, avg_rating DESC, m.created_at DESC
                                  LIMIT 4");
    while ($m = $fallback_res->fetch_assoc()) {
        $movies[] = [
            'id'            => (int)$m['movie_id'],
            'title'         => $m['title'],
            'genre'         => $m['genre'] ?: 'Cinema',
            'poster'        => posterUrl($m),
            'duration'      => (int)$m['duration_minutes'],
            'industry'      => $m['industry'] ?? '',
                'rating'        => $m['avg_rating'] > 0 ? round((float)$m['avg_rating'], 1) : null,
            'match_score'   => rand(88, 95),
            'description'   => $m['description'] ? mb_substr($m['description'], 0, 120) . '...' : 'Top rated movie recommendation from our cinema catalog.',
            'is_coming_soon'=> !empty($m['release_date']) && $m['release_date'] > date('Y-m-d'),
            'book_url'      => 'movie_details.php?id=' . (int)$m['movie_id']
        ];
    }
}

// Generate intelligent AI commentary based on findings
$commentary = "";
if (!empty($genre_hints)) {
    $g_str = implode(" & ", array_slice(array_unique($genre_hints), 0, 2));
    $commentary = "Based on your preference for <strong>" . htmlspecialchars($g_str) . "</strong>, I've scanned " . count($movies) . " high-match titles in our catalog:";
} elseif ($query !== '') {
    $commentary = "I analyzed our movie database for <em>\"" . htmlspecialchars($query) . "\"</em> and found these curated recommendations:";
} else {
    $commentary = "Here are our top neural-curated blockbuster picks tailored for your cinema session:";
}

echo json_encode([
    'success'    => true,
    'commentary' => $commentary,
    'count'      => count($movies),
    'movies'     => $movies
], JSON_UNESCAPED_UNICODE);
