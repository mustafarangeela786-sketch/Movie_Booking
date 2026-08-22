<?php
/* ==========================================================
   admin/movies.php - Add, edit and delete movies, including
   poster image upload and YouTube trailer link.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$edit_movie = null;
$error = "";

// Delete a movie
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM movies WHERE movie_id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    redirect('movies.php');
}

// Load a movie to edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_movie = $stmt->get_result()->fetch_assoc();
}

// Add or update a movie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = clean($_POST['title']);
    $genre       = trim(clean($_POST['genre']));
    $language    = clean($_POST['language']);
    $industry    = clean($_POST['industry']);
    $quality_tag = clean($_POST['quality_tag']);
    $duration    = (int)$_POST['duration_minutes'];
    $description = clean($_POST['description']);
    $trailer_url = youtubeEmbedUrl(clean($_POST['trailer_url']));
    $poster      = clean($_POST['poster']);
    $release     = $_POST['release_date'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $movie_id    = $_POST['movie_id'] ?? '';

    if (!empty($movie_id)) {
        // Update existing movie. Type string: s s s s s i s s s s i i
        // title, genre, language, industry, quality_tag, duration, description, trailer_url, poster, release_date, is_featured, movie_id
        $mid = (int)$movie_id;
        $upd = $conn->prepare("UPDATE movies SET title=?, genre=?, language=?, industry=?, quality_tag=?, duration_minutes=?, description=?, trailer_url=?, poster=?, release_date=?, is_featured=? WHERE movie_id=?");
        $upd->bind_param("sssssissssii", $title, $genre, $language, $industry, $quality_tag, $duration, $description, $trailer_url, $poster, $release, $is_featured, $mid);
        if (!$upd->execute()) {
            $error = "Could not save changes: " . $upd->error;
        }
        $upd->close();
    } else {
        // Insert new movie. Type string: s s s s s i s s s s i
        $ins = $conn->prepare("INSERT INTO movies (title, genre, language, industry, quality_tag, duration_minutes, description, trailer_url, poster, release_date, is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $ins->bind_param("sssssissssi", $title, $genre, $language, $industry, $quality_tag, $duration, $description, $trailer_url, $poster, $release, $is_featured);
        if (!$ins->execute()) {
            $error = "Could not add movie: " . $ins->error;
        }
        $ins->close();
    }
    if (!$error) {
        redirect('movies.php');
    } else {
        // Keep the form populated with what was just submitted so nothing is lost.
        $edit_movie = [
            'movie_id' => $movie_id, 'title' => $title, 'genre' => $genre, 'language' => $language,
            'industry' => $industry, 'quality_tag' => $quality_tag, 'duration_minutes' => $duration,
            'description' => $description, 'trailer_url' => $trailer_url, 'poster' => $poster,
            'release_date' => $release, 'is_featured' => $is_featured,
        ];
    }
}

// Industry filter (Hollywood / Bollywood) and Genre filter (Horror / Comedy / etc.)
// for the movies table below. Both can be combined at once.
$industry_filter = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$genre_filter    = isset($_GET['genre']) ? trim($_GET['genre']) : '';

$movie_where = [];
$movie_params = [];
$movie_types = '';
if ($industry_filter !== '') {
    $movie_where[] = "LOWER(industry) = LOWER(?)";
    $movie_params[] = $industry_filter;
    $movie_types .= 's';
}
if ($genre_filter !== '') {
    $movie_where[] = "LOWER(genre) = LOWER(?)";
    $movie_params[] = $genre_filter;
    $movie_types .= 's';
}
$movie_where_sql = $movie_where ? (' WHERE ' . implode(' AND ', $movie_where)) : '';

$movies_stmt = $conn->prepare("SELECT * FROM movies" . $movie_where_sql . " ORDER BY created_at DESC");
if ($movie_types !== '') {
    $movies_stmt->bind_param($movie_types, ...$movie_params);
}
$movies_stmt->execute();
$movies = $movies_stmt->get_result();

// Distinct genres currently in the catalog, to populate the genre dropdown.
$genre_options = [];
$gres = $conn->query("SELECT DISTINCT genre FROM movies WHERE genre <> '' ORDER BY genre ASC");
while ($grow = $gres->fetch_assoc()) $genre_options[] = $grow['genre'];

include __DIR__ . '/admin_header.php';
?> <h1>Movies</h1>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:600px;"> <h2><?php echo $edit_movie ? 'Edit Movie' : 'Add New Movie'; ?></h2> <input type="hidden" name="movie_id" value="<?php echo $edit_movie['movie_id'] ?? ''; ?>"> <label>Title</label> <input type="text" name="title" value="<?php echo clean($edit_movie['title'] ?? ''); ?>" required> <label>Genre</label> <input type="text" name="genre" value="<?php echo clean($edit_movie['genre'] ?? ''); ?>"> <label>Language</label> <input type="text" name="language" value="<?php echo clean($edit_movie['language'] ?? ''); ?>"> <label>Industry</label> <select name="industry"> <option value="">— Select —</option> <option value="Hollywood" <?php echo (($edit_movie['industry'] ?? '') === 'Hollywood') ? 'selected' : ''; ?>>Hollywood</option> <option value="Bollywood" <?php echo (($edit_movie['industry'] ?? '') === 'Bollywood') ? 'selected' : ''; ?>>Bollywood</option> <option value="Other" <?php echo (($edit_movie['industry'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option> </select> <label>Quality Tag (e.g. 4K, HD, IMAX)</label> <input type="text" name="quality_tag" placeholder="4K" value="<?php echo clean($edit_movie['quality_tag'] ?? ''); ?>"> <label>Duration (minutes)</label> <input type="number" name="duration_minutes" value="<?php echo clean($edit_movie['duration_minutes'] ?? ''); ?>"> <label>Description</label> <textarea name="description" rows="4"><?php echo clean($edit_movie['description'] ?? ''); ?></textarea> <label>Poster Image URL (paste a direct image link)</label> <input type="text" name="poster" placeholder="https://example.com/poster.jpg" value="<?php echo clean($edit_movie['poster'] ?? ''); ?>"> <?php if (!empty($edit_movie['poster'])): ?> <img src="<?php echo clean(posterUrl($edit_movie, '../')); ?>" alt="Current poster"
             onerror="<?php echo posterOnError('../'); ?>"
             style="width:90px;height:120px;object-fit:cover;border-radius:6px;margin-top:8px;display:block;"> <?php endif; ?> <label>Trailer URL (YouTube embed link)</label> <input type="text" name="trailer_url" placeholder="https://www.youtube.com/embed/VIDEO_ID" value="<?php echo clean($edit_movie['trailer_url'] ?? ''); ?>"> <label>Release Date</label> <input type="date" name="release_date" value="<?php echo clean($edit_movie['release_date'] ?? ''); ?>"> <label style="display:flex;align-items:center;gap:8px;margin-top:16px;"> <input type="checkbox" name="is_featured" value="1" style="width:auto;" <?php echo !empty($edit_movie['is_featured']) ? 'checked' : ''; ?>> Show in the "Featured Movies" section on the homepage </label> <button type="submit"><?php echo $edit_movie ? 'Update Movie' : 'Add Movie'; ?></button>
</form> <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin:28px 0 12px;"> <h2 style="margin:0;">All Movies</h2> <div style="display:flex;gap:10px;flex-wrap:wrap;margin-left:auto;"> <select class="category-industry-select" id="adminGenreSelect" aria-label="Filter by genre" onchange="mvAdminFilterMovies();" style="margin-left:0;"> <option value="">All Genres</option> <?php foreach ($genre_options as $g): ?> <option value="<?php echo clean($g); ?>" <?php echo (strtolower($genre_filter) === strtolower($g)) ? 'selected' : ''; ?>><?php echo clean($g); ?></option> <?php endforeach; ?> </select> <select class="category-industry-select" id="adminIndustrySelect" aria-label="Filter by industry" onchange="mvAdminFilterMovies();" style="margin-left:0;"> <option value="">All Industries</option> <option value="Hollywood" <?php echo (strtolower($industry_filter) === 'hollywood') ? 'selected' : ''; ?>>Hollywood</option> <option value="Bollywood" <?php echo (strtolower($industry_filter) === 'bollywood') ? 'selected' : ''; ?>>Bollywood</option> </select> </div> </div> <script> function mvAdminFilterMovies() { var genre = document.getElementById('adminGenreSelect').value; var industry = document.getElementById('adminIndustrySelect').value; var params = new URLSearchParams(); if (genre) params.set('genre', genre); if (industry) params.set('industry', industry); var qs = params.toString(); window.location.href = qs ? ('movies.php?' + qs) : 'movies.php'; } </script> <div class="admin-table-scroll"> <table class="admin-table-nowrap"> <tr><th>Poster</th><th>Title</th><th>Genre</th><th>Industry</th><th>Quality</th><th>Duration</th><th>Release</th><th>Featured</th><th>Actions</th></tr> <?php while ($m = $movies->fetch_assoc()): ?> <tr> <td><img src="<?php echo clean(posterUrl($m, '../')); ?>" alt="" onerror="<?php echo posterOnError('../'); ?>" style="width:40px;height:56px;object-fit:cover;border-radius:4px;"></td> <td><?php echo clean($m['title']); ?></td> <td><?php echo clean($m['genre']); ?></td> <td><?php echo clean($m['industry'] ?: '—'); ?></td> <td><?php echo clean($m['quality_tag'] ?: '—'); ?></td> <td><?php echo (int)$m['duration_minutes']; ?> min</td> <td><?php echo clean($m['release_date']); ?></td> <td><?php echo !empty($m['is_featured']) ? ' Yes' : '—'; ?></td> <td> <a href="movies.php?edit=<?php echo $m['movie_id']; ?>">Edit</a> | <a href="movies.php?delete=<?php echo $m['movie_id']; ?>" onclick="return confirm('Delete this movie?');">Delete</a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>