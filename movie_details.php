<?php
/* ==========================================================
   movie_details.php - Editorial Cinema Movie Details
   Shows full movie info, official trailer, showtimes, and reviews.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) {
    redirect('index.php');
}

// Handle review submission
$review_error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    $rating  = (int)$_POST['rating'];
    $comment = clean($_POST['comment']);
    if ($rating >= 1 && $rating <= 5) {
        $ins = $conn->prepare("INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $ins->bind_param("iiis", $movie_id, $_SESSION['user_id'], $rating, $comment);
        $ins->execute();
        $ins->close();
        redirect('movie_details.php?id=' . $movie_id . '#reviews');
    } else {
        $review_error = "Please choose a rating between 1 and 5.";
    }
}

// Fetch upcoming shows
$has_seat_map = seatMapSchemaExists($conn);
$shows_sql = "
    SELECT s.*, t.theater_name, t.location, t.latitude, t.longitude";
if ($has_seat_map) {
    $shows_sql .= ", sc.screen_name";
}
$shows_sql .= "
    FROM shows s
    JOIN theaters t ON s.theater_id = t.theater_id";
if ($has_seat_map) {
    $shows_sql .= "
    LEFT JOIN screens sc ON s.screen_id = sc.screen_id";
}
$shows_sql .= "
    WHERE s.movie_id = ? AND s.show_date >= CURDATE()
    ORDER BY s.show_date, s.show_time";

$shows = $conn->prepare($shows_sql);
$shows->bind_param("i", $movie_id);
$shows->execute();
$shows_result = $shows->get_result();

// Fetch reviews
$reviews = $conn->prepare("
    SELECT r.*, u.full_name FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.movie_id = ? ORDER BY r.created_at DESC
");
$reviews->bind_param("i", $movie_id);
$reviews->execute();
$reviews_result = $reviews->get_result();

$avg = getAverageRating($conn, $movie_id);

$GLOBALS['page_genre'] = $movie['genre'];
include __DIR__ . '/includes/header.php';
$poster = posterUrl($movie);
?> <div class="details-wrap" data-genre="<?php echo clean($movie['genre']); ?>"> <!-- Cinematic Header Backdrop --> <div class="details-backdrop-hero" style="background-image: url('<?php echo clean($poster); ?>');"> <div class="details-backdrop-overlay"></div> <div class="details-poster-box"> <img src="<?php echo clean($poster); ?>" alt="<?php echo clean($movie['title']); ?>"
                 onerror="<?php echo posterOnError(); ?>"> </div> <div class="details-info-box"> <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;"> <span class="hero-pill-badge" style="background:var(--cinema-red);"><?php echo clean($movie['genre'] ?: 'Feature'); ?></span> <?php if (!empty($movie['industry'])): ?><span class="hero-pill-badge"><?php echo clean($movie['industry']); ?></span><?php endif; ?> </div> <h1><?php echo clean($movie['title']); ?></h1> <div class="details-meta-row"> <span> <?php echo (int)$movie['duration_minutes']; ?> minutes</span> &bull; <span> <?php echo clean($movie['language'] ?: 'English'); ?></span> &bull; <?php if ($avg['total'] > 0): ?> <span style="color:var(--cinema-gold);font-weight:700;"> <?php echo round($avg['avg_rating'], 1); ?>/5 (<?php echo $avg['total']; ?> reviews)</span> <?php else: ?> <span style="color:var(--cinema-blue);font-weight:600;"> New Release</span> <?php endif; ?> </div> <p class="details-desc"><?php echo nl2br(clean($movie['description'] ?: 'Experience this cinematic release on the big screen with surround sound and luxury theater seating.')); ?></p> <div style="display:flex;gap:12px;flex-wrap:wrap;"> <a href="#showtimes" class="btn" style="padding:11px 26px;"> Book Tickets </a> <?php if (!empty($movie['trailer_url'])): ?> <a href="#trailer" class="btn" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid var(--border-card);"> Watch Trailer </a> <?php endif; ?> </div> </div> </div> <!-- Official Trailer Section --> <?php if (!empty($movie['trailer_url'])): ?> <div id="trailer" class="details-media-row"> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;"> <h3 style="display:flex;align-items:center;gap:8px;"> <span style="color:var(--cinema-red);"></span> Official Trailer </h3> </div> <div class="details-trailer-half"> <iframe src="<?php echo clean(youtubeEmbedUrl($movie['trailer_url'])); ?>" loading="lazy" allowfullscreen title="Trailer for <?php echo clean($movie['title']); ?>"></iframe> </div> </div> <?php endif; ?>
</div> <!-- Showtimes Section -->
<div id="showtimes" style="margin-top: 40px;"> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;"> <h2>Select Showtime</h2> <span style="font-size:13px;color:var(--text-muted);font-weight:600;">Instant Seat Reservation</span> </div> <?php if ($shows_result->num_rows === 0): ?> <div class="alert alert-warning">No upcoming shows currently scheduled for this movie. Please check back soon!</div> <?php else: ?> <div class="showtimes-grid"> <?php while ($show = $shows_result->fetch_assoc()): ?> <?php
                $maps_link = ($show['latitude'] && $show['longitude'])
                    ? "https://www.google.com/maps/search/?api=1&query=" . $show['latitude'] . "," . $show['longitude']
                    : "https://www.google.com/maps/search/?api=1&query=" . urlencode($show['theater_name'] . ', ' . $show['location']);
            ?> <div class="showtime-card"> <div style="display:flex;align-items:center;justify-content:space-between;"> <span style="font-size:12.5px;color:var(--text-secondary);font-weight:700;"> <?php echo date('D, d M Y', strtotime($show['show_date'])); ?> </span> <span style="font-size:14px;font-weight:800;color:var(--cinema-gold);"> <?php echo date('h:i A', strtotime($show['show_time'])); ?> </span> </div> <div class="showtime-theater"> <?php echo clean($show['theater_name']); ?> </div> <div class="showtime-meta"> <?php echo clean($show['location']); ?> <?php if (!empty($show['screen_name'])): ?> &bull; <strong><?php echo clean($show['screen_name']); ?></strong><?php endif; ?> </div> <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:8px;"> <a href="<?php echo clean($maps_link); ?>" target="_blank" rel="noopener" style="font-size:12px;color:var(--text-muted);"> Maps </a> <a class="showtime-btn-book" href="book_ticket.php?show_id=<?php echo $show['show_id']; ?>"> Select Seats </a> </div> </div> <?php endwhile; ?> </div> <?php endif; ?>
</div> <!-- Reviews Section -->
<div id="reviews" style="margin-top: 48px;"> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;"> <h2>Audience Reviews</h2> <span style="font-size:13.5px;color:var(--cinema-gold);font-weight:700;"> <?php echo $avg['total'] > 0 ? " " . round($avg['avg_rating'], 1) . " Average" : "No reviews yet"; ?> </span> </div> <?php if (isLoggedIn()): ?> <?php if ($review_error): ?><div class="alert alert-error"><?php echo $review_error; ?></div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:640px;"> <h3 style="margin-bottom:14px;">Leave a Review</h3> <label>Rating</label> <select name="rating" required> <option value="">Select rating (1 to 5 stars)</option> <option value="5"> (5/5) - Excellent</option> <option value="4"> (4/5) - Very Good</option> <option value="3"> (3/5) - Average</option> <option value="2"> (2/5) - Poor</option> <option value="1"> (1/5) - Terrible</option> </select> <label>Your Feedback</label> <textarea name="comment" rows="3" placeholder="Share your experience..." required></textarea> <button type="submit" name="submit_review" class="btn">Submit Review</button> </form> <?php else: ?> <div class="form-box" style="margin-left:0;max-width:640px;text-align:center;"> <p style="margin-bottom:12px;">Log in to share your rating and review.</p> <button type="button" class="btn" onclick="openAuth('login')">Log In to Review</button> </div> <?php endif; ?> <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px,1fr));gap:16px;margin-top:20px;"> <?php if ($reviews_result->num_rows === 0): ?> <p style="color:var(--text-muted);">Be the first to review this movie!</p> <?php endif; ?> <?php while ($rev = $reviews_result->fetch_assoc()): ?> <div class="form-box" style="margin-bottom:0;padding:18px;"> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;"> <strong style="color:var(--text-primary);font-size:14.5px;"><?php echo clean($rev['full_name']); ?></strong> <span class="rating-badge star-rating-badge"> <?php echo (int)$rev['rating']; ?>/5</span> </div> <p style="margin:0;font-size:13.5px;color:var(--text-secondary);line-height:1.5;"> <?php echo nl2br(clean($rev['comment'])); ?> </p> <div style="font-size:11.5px;color:var(--text-muted);margin-top:8px;"> <?php echo date('d M Y', strtotime($rev['created_at'])); ?> </div> </div> <?php endwhile; ?> </div>
</div> <?php include __DIR__ . '/includes/footer.php'; ?>
