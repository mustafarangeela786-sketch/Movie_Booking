<?php
/* ==========================================================
   index.php - Premier Cinema Discovery Experience
   Clean, Modern UI/UX Designer-Crafted Interface
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

// Distinct genres in preferred catalog order
$preferred_order = ['Action', 'Romance', 'Horror', 'Comedy', 'Sci-Fi', 'Thriller', 'Drama', 'Animation', 'Adventure', 'Crime'];
$genre_result = $conn->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL AND genre != ''");
$genres_present = [];
while ($g = $genre_result->fetch_assoc()) {
    $genres_present[] = $g['genre'];
}
$ordered_genres = array_values(array_filter($preferred_order, fn($g) => in_array($g, $genres_present)));
foreach ($genres_present as $g) {
    if (!in_array($g, $ordered_genres)) $ordered_genres[] = $g;
}

// Search matching logic
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_matched_id = null;
if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $s = $conn->prepare("SELECT movie_id FROM movies WHERE title LIKE ? ORDER BY (title = ?) DESC, created_at DESC LIMIT 1");
    $s->bind_param("ss", $like, $search_q);
    $s->execute();
    $found = $s->get_result()->fetch_assoc();
    if ($found) $search_matched_id = (int)$found['movie_id'];
}

// Industry filter
$industry_filter = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$industry_clause = '';
if ($industry_filter !== '') {
    $industry_clause = " AND LOWER(industry) = LOWER('" . $conn->real_escape_string($industry_filter) . "')";
}

// Fetch top blockbusters for dynamic hero slider
$hero_query = $conn->query("
    SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating
    FROM movies m
    LEFT JOIN reviews r ON m.movie_id = r.movie_id
    WHERE m.poster IS NOT NULL AND m.poster != ''" . $industry_clause . "
    GROUP BY m.movie_id
    ORDER BY (m.is_featured = 1) DESC, avg_rating DESC, m.created_at DESC
    LIMIT 4
");
$hero_movies = [];
while ($hm = $hero_query->fetch_assoc()) {
    $hero_movies[] = $hm;
}

include __DIR__ . '/includes/header.php';
$ratings_cache = getAllAverageRatings($conn);
?> <!-- Editorial Cinema Hero Showcase -->
<?php if (!empty($hero_movies)): ?>
<div class="home-hero-wrap"> <section class="home-slider" id="homeSlider"> <div class="home-slider-track"> <?php foreach ($hero_movies as $idx => $hm): 
                $poster = posterUrl($hm);
                $is_coming = !empty($hm['release_date']) && $hm['release_date'] > date('Y-m-d');
                $rating_val = $hm['avg_rating'] > 0 ? round($hm['avg_rating'], 1) . '/5' : '4.8/5';
            ?> <div class="home-slide <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-slide-index="<?php echo $idx; ?>"> <div class="home-slide-bg" style="background-image: url('<?php echo clean($poster); ?>');"></div> <div class="home-slide-overlay"></div> <div class="home-slide-content"> <div class="hero-tag-row"> <span class="hero-pill-badge featured-badge">Featured Premiere</span> <span class="hero-pill-badge"><?php echo clean($hm['genre'] ?: 'Action'); ?></span> </div> <h2 class="hero-movie-title"><?php echo clean($hm['title']); ?></h2> <div class="hero-movie-meta"> <span class="meta-star"> <?php echo $rating_val; ?></span> &bull; <span><?php echo (int)$hm['duration_minutes']; ?> min</span> &bull; <span><?php echo clean($hm['language'] ?: 'English'); ?></span> <?php if (!empty($hm['industry'])): ?> &bull; <span><?php echo clean($hm['industry']); ?></span><?php endif; ?> </div> <div class="hero-actions"> <a href="movie_details.php?id=<?php echo (int)$hm['movie_id']; ?>" class="hero-btn-book"> <span>Book Tickets</span> </a> <?php if (!empty($hm['trailer_url'])): ?> <a href="movie_details.php?id=<?php echo (int)$hm['movie_id']; ?>#trailer" class="hero-btn-trailer"> <span>Watch Trailer</span> </a> <?php endif; ?> </div> </div> </div> <?php endforeach; ?> </div> <div class="home-slider-nav"> <?php foreach ($hero_movies as $idx => $hm): ?> <span class="home-slider-dot <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-index="<?php echo $idx; ?>"></span> <?php endforeach; ?> </div> </section>
</div>
<script>
(function () {
    const slides = document.querySelectorAll('#homeSlider .home-slide');
    const dots = document.querySelectorAll('#homeSlider .home-slider-dot');
    let current = 0;
    const total = slides.length;
    if (total < 2) return;

    function goTo(index) {
        slides[current].classList.remove('is-active');
        dots[current].classList.remove('is-active');
        current = (index + total) % total;
        slides[current].classList.add('is-active');
        dots[current].classList.add('is-active');
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.getAttribute('data-index'), 10));
        });
    });

    let autoSlide = setInterval(() => { goTo(current + 1); }, 6000);
    const wrap = document.getElementById('homeSlider');
    if (wrap) {
        wrap.addEventListener('mouseenter', () => clearInterval(autoSlide));
        wrap.addEventListener('mouseleave', () => {
            clearInterval(autoSlide);
            autoSlide = setInterval(() => { goTo(current + 1); }, 6000);
        });
    }
})();
</script>
<?php endif; ?> <!-- Category Filter Pills Bar -->
<div class="category-filter-bar"> <button type="button" class="filter-tab-pill is-active" data-filter="all">All Movies</button> <button type="button" class="filter-tab-pill" data-filter="now-showing">Now Showing</button> <button type="button" class="filter-tab-pill" data-filter="featured">Featured</button> <button type="button" class="filter-tab-pill" data-filter="coming-soon">Coming Soon</button> <button type="button" class="filter-tab-pill" data-filter="action">Action</button> <button type="button" class="filter-tab-pill" data-filter="romance">Romance</button> <button type="button" class="filter-tab-pill" data-filter="sci-fi">Sci-Fi</button> <button type="button" class="filter-tab-pill" data-filter="comedy">Comedy</button> <button type="button" class="filter-tab-pill" data-filter="horror">Horror</button> <select class="category-industry-select" id="industrySelect" aria-label="Filter by industry" onchange="mvGoToIndustry(this.value);"> <option value="">All Industries</option> <option value="Hollywood" <?php echo (isset($_GET['industry']) && strtolower($_GET['industry']) === 'hollywood') ? 'selected' : ''; ?>>Hollywood</option> <option value="Bollywood" <?php echo (isset($_GET['industry']) && strtolower($_GET['industry']) === 'bollywood') ? 'selected' : ''; ?>>Bollywood</option> </select>
</div> <?php
// "Now Showing" - Playing in cinemas
$now_showing = $conn->query("SELECT * FROM movies WHERE release_date <= CURDATE()" . $industry_clause . " ORDER BY release_date DESC, created_at DESC LIMIT 12");
?>
<?php if ($now_showing && $now_showing->num_rows > 0): ?>
<section class="genre-section" data-genre="now-showing"> <div class="genre-header"> <h2 class="genre-title">Now Showing in Theaters</h2> <span class="genre-count-tag"><?php echo $now_showing->num_rows; ?> Movies</span> </div> <div class="movie-row-wrap"> <button type="button" class="row-arrow row-arrow-left" aria-label="Scroll left">Prev</button> <div class="movie-row"> <?php while ($movie = $now_showing->fetch_assoc()): ?> <?php renderMovieCard($conn, $movie, ['label' => 'Now Showing', 'class' => 'tag-now-showing'], false, $ratings_cache); ?> <?php endwhile; ?> </div> <button type="button" class="row-arrow row-arrow-right" aria-label="Scroll right">Next</button> </div>
</section>
<?php endif; ?> <?php
// "Featured Blockbusters"
$featured = $conn->query("SELECT * FROM movies WHERE is_featured = 1" . $industry_clause . " ORDER BY release_date DESC, created_at DESC LIMIT 12");
?>
<?php if ($featured && $featured->num_rows > 0): ?>
<section class="genre-section" data-genre="featured"> <div class="genre-header"> <h2 class="genre-title">Featured Blockbusters</h2> <span class="genre-count-tag">Curated Picks</span> </div> <div class="movie-row-wrap"> <button type="button" class="row-arrow row-arrow-left" aria-label="Scroll left">Prev</button> <div class="movie-row"> <?php while ($movie = $featured->fetch_assoc()): ?> <?php renderMovieCard($conn, $movie, ['label' => 'Featured', 'class' => 'tag-featured'], false, $ratings_cache); ?> <?php endwhile; ?> </div> <button type="button" class="row-arrow row-arrow-right" aria-label="Scroll right">Next</button> </div>
</section>
<?php endif; ?> <?php
// "Coming Soon" - Future releases
$coming_soon = $conn->query("SELECT * FROM movies WHERE release_date > CURDATE()" . $industry_clause . " ORDER BY release_date ASC LIMIT 12");
?>
<?php if ($coming_soon && $coming_soon->num_rows > 0): ?>
<section class="genre-section" data-genre="coming-soon"> <div class="genre-header"> <h2 class="genre-title">Coming Soon</h2> <span class="genre-count-tag">Upcoming Releases</span> </div> <div class="movie-row-wrap"> <button type="button" class="row-arrow row-arrow-left" aria-label="Scroll left">Prev</button> <div class="movie-row"> <?php while ($movie = $coming_soon->fetch_assoc()): ?> <?php renderMovieCard($conn, $movie, ['label' => 'Coming Soon', 'class' => 'tag-coming-soon'], false, $ratings_cache); ?> <?php endwhile; ?> </div> <button type="button" class="row-arrow row-arrow-right" aria-label="Scroll right">Next</button> </div>
</section>
<?php endif; ?> <?php if ($search_q !== '' && !$search_matched_id): ?>
<div class="alert alert-warning">No movies found matching "<?php echo clean($search_q); ?>". Showing full catalog below:</div>
<?php endif; ?> <?php foreach ($ordered_genres as $genre):
    $stmt = $conn->prepare("SELECT * FROM movies WHERE genre = ?" . $industry_clause . " ORDER BY created_at DESC LIMIT 30");
    $stmt->bind_param("s", $genre);
    $stmt->execute();
    $movies = $stmt->get_result();
    if ($movies->num_rows === 0) continue;
?>
<section class="genre-section" data-genre="<?php echo strtolower(clean($genre)); ?>"> <div class="genre-header"> <h2 class="genre-title"><?php echo clean($genre); ?></h2> <span class="genre-count-tag"><?php echo $movies->num_rows; ?> Movies</span> </div> <div class="movie-row-wrap"> <button type="button" class="row-arrow row-arrow-left" aria-label="Scroll left">Prev</button> <div class="movie-row"> <?php while ($movie = $movies->fetch_assoc()): ?> <?php renderMovieCard($conn, $movie, null, true, $ratings_cache); ?> <?php endwhile; ?> </div> <button type="button" class="row-arrow row-arrow-right" aria-label="Scroll right">Next</button> </div>
</section>
<?php endforeach; ?> <?php if ($search_matched_id): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var target = document.getElementById('movie-<?php echo $search_matched_id; ?>');
    if (!target) return;
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    target.style.outline = '2px solid var(--cinema-red)';
    setTimeout(function () {
        target.style.outline = '';
    }, 3500);
});
</script>
<?php endif; ?> <?php if (isset($_GET['booking_cancelled'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.mvToast) mvToast('Your booking was cancelled.');
});
</script>
<?php endif; ?> <?php include __DIR__ . '/includes/footer.php'; ?>
