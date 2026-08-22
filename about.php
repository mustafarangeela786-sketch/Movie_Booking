<?php
/* ==========================================================
   about.php - About Us page. Tells visitors who we are, what
   we offer, and shows a few live stats pulled straight from
   the database so the numbers always stay accurate.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

// Live stats, pulled from the database
$movie_count   = (int)($conn->query("SELECT COUNT(*) AS c FROM movies")->fetch_assoc()['c'] ?? 0);
$theater_count = (int)($conn->query("SELECT COUNT(*) AS c FROM theaters")->fetch_assoc()['c'] ?? 0);
$genre_count   = (int)($conn->query("SELECT COUNT(DISTINCT genre) AS c FROM movies WHERE genre IS NOT NULL AND genre != ''")->fetch_assoc()['c'] ?? 0);
$booking_count = (int)($conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status = 'Confirmed'")->fetch_assoc()['c'] ?? 0);

// What we offer - the same "features" story told throughout the site
$features = [
    ['icon' => '', 'title' => 'Huge Movie Catalog',   'text' => "Browse $movie_count+ movies across every genre - action, romance, horror, comedy and more - updated regularly."],
    ['icon' => '', 'title' => 'Real Cinema Locations', 'text' => "Book seats at $theater_count real theaters, each with maps, seat classes and showtimes you can trust."],
    ['icon' => '', 'title' => 'Instant E-Tickets',     'text' => 'Confirm a booking and get a downloadable e-ticket instantly - just show it at the counter.'],
    ['icon' => '', 'title' => 'Genuine Ratings',        'text' => 'Every movie carries ratings and reviews from real moviegoers, so you always know what is worth watching.'],
    ['icon' => '', 'title' => 'Secure Payments',        'text' => 'Pay safely by card, Easypaisa or JazzCash, or simply reserve now and pay cash at the counter.'],
    ['icon' => '', 'title' => 'Search & Discover',      'text' => 'Use the search bar in the header to jump straight to any movie, anywhere on the site, in seconds.'],
];

include __DIR__ . '/includes/header.php';
?> <h1>About Us</h1>
<p style="color:#a0a0a0;max-width:720px;">MovieBook is your online home for booking movie tickets in seconds - from the latest blockbusters to timeless classics, across genres and theaters, all in one place.</p> <div class="form-box" style="margin-left:0;max-width:760px;"> <h2>Our Story</h2> <p style="color:#eaeaea;line-height:1.7;"> MovieBook started with a simple idea: booking a movie ticket should be as enjoyable as watching the movie itself.
        No queues, no confusion over seat classes, and no guessing whether a show is even running. Today we bring together <?php echo $movie_count; ?>+ movies across <?php echo $genre_count; ?> genres and <?php echo $theater_count; ?> partner theaters,
        so you can pick a film, pick a seat, and get an e-ticket in your inbox - all before the trailers even start. </p>
</div> <h2 style="margin-top:40px;">Why Choose Us</h2>
<div class="feature-grid"> <?php foreach ($features as $f): ?> <div class="feature-card"> <div class="feature-icon"><?php echo $f['icon']; ?></div> <h3><?php echo clean($f['title']); ?></h3> <p><?php echo clean($f['text']); ?></p> </div> <?php endforeach; ?>
</div> <h2 style="margin-top:40px;">MovieBook in Numbers</h2>
<div class="stats-grid"> <div class="stat-card"> <div class="stat-number"><?php echo $movie_count; ?>+</div> <div class="stat-label">Movies Listed</div> </div> <div class="stat-card"> <div class="stat-number"><?php echo $theater_count; ?></div> <div class="stat-label">Partner Theaters</div> </div> <div class="stat-card"> <div class="stat-number"><?php echo $genre_count; ?></div> <div class="stat-label">Genres to Explore</div> </div> <div class="stat-card"> <div class="stat-number"><?php echo $booking_count; ?>+</div> <div class="stat-label">Tickets Booked</div> </div>
</div> <div class="form-box" style="margin-left:0;max-width:760px;text-align:center;"> <h2>Ready for the next show?</h2> <p style="color:#eaeaea;">Browse the full catalog and book your seats in just a couple of clicks.</p> <a class="btn" href="index.php">Browse Movies</a>
</div> <?php include __DIR__ . '/includes/footer.php'; ?>
