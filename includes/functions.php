<?php
/* ==========================================================
   functions.php
   Common helper functions used across the whole site.
   Starts the session and provides small reusable utilities.
   ========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    // Force the login cookie to be a true "session cookie" - i.e. it must
    // expire the moment the browser is actually closed, never survive a
    // restart. (Some local server setups (XAMPP/WAMP) ship with a php.ini
    // session.cookie_lifetime greater than 0, which silently turns every
    // login into a "remember me" login - this overrides that no matter
    // what php.ini says.)
    session_set_cookie_params([
        'lifetime' => 0, // 0 = expires when the browser is closed, not a fixed duration
        'path'     => '/',
        'httponly' => true,   // JS can't read the cookie (helps against XSS session theft)
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();

    // Extra safety net: if this session has sat idle too long (2 hours),
    // log it out automatically instead of trusting the cookie forever.
    // This protects shared/public computers even if someone's browser is
    // set to "restore previous session" on relaunch.
    $idle_limit = 7200; // seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $idle_limit) {
        $_SESSION = [];
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

require_once __DIR__ . '/../config/db.php';

// Fallback poster image shown for any movie that doesn't have its own
// poster set (or whose poster link is broken).
define('FALLBACK_POSTER_URL', 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="580" viewBox="0 0 400 580"><rect width="400" height="580" fill="#181c26"/><rect x="0" y="0" width="400" height="580" fill="none" stroke="#2a2f3d" stroke-width="2"/><g fill="none" stroke="#3a4152" stroke-width="3"><rect x="40" y="40" width="320" height="500" rx="10"/></g><g fill="#3a4152"><rect x="40" y="40" width="26" height="26" rx="4"/><rect x="86" y="40" width="26" height="26" rx="4"/><rect x="288" y="40" width="26" height="26" rx="4"/><rect x="334" y="40" width="26" height="26" rx="4"/><rect x="40" y="514" width="26" height="26" rx="4"/><rect x="86" y="514" width="26" height="26" rx="4"/><rect x="288" y="514" width="26" height="26" rx="4"/><rect x="334" y="514" width="26" height="26" rx="4"/></g><circle cx="200" cy="270" r="46" fill="#20242f" stroke="#3a4152" stroke-width="2"/><path d="M188 250 L226 270 L188 290 Z" fill="#5b6577"/><text x="200" y="360" font-family="Arial, sans-serif" font-size="20" font-weight="700" fill="#5b6577" text-anchor="middle">POSTER UNAVAILABLE</text></svg>'));

// Secret used to sign e-ticket QR codes so verify_ticket.php can trust
// the booking_id in the URL wasn't just guessed/incremented by someone
// scanning a stranger's ticket. Change this in production deployments.
if (!defined('TICKET_QR_SECRET')) {
    define('TICKET_QR_SECRET', 'cine-verse-ticket-secret-v1-change-me');
}

// Short signature proving a given booking_id came from a genuine,
// server-generated QR code (not just an incrementing URL guess).
function ticketVerifyToken($booking_id) {
    return substr(hash_hmac('sha256', (string)$booking_id, TICKET_QR_SECRET), 0, 16);
}

// Full absolute URL encoded into the ticket's QR code. Scanning it with
// any phone camera opens verify_ticket.php, which shows real-time
// paid/unpaid + confirmed/cancelled status for that booking.
function ticketVerifyUrl($booking_id) {
    return appBaseUrl() . '/verify_ticket.php?id=' . (int)$booking_id . '&t=' . ticketVerifyToken($booking_id);
}

// Renders a decorative (non-scannable) barcode as inline SVG bars, seeded
// deterministically from $seed so the same booking always draws the same
// pattern. Purely visual flourish for the e-ticket - the QR code above it
// is what's actually used for real verification.
function renderBarcodeSvg($seed, $width = 260, $height = 46, $color = '#0f172a') {
    mt_srand(crc32((string)$seed));
    $bars = '';
    $x = 0;
    while ($x < $width) {
        $bar_w = mt_rand(2, 6);
        if (mt_rand(0, 4) > 0) { // ~80% of slots draw a bar, rest stay blank (gap)
            $bars .= '<rect x="' . $x . '" y="0" width="' . $bar_w . '" height="' . $height . '" fill="' . $color . '"/>';
        }
        $x += $bar_w + mt_rand(1, 3);
    }
    mt_srand(); // reseed randomly so nothing else in the request is affected
    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" height="' . $height . '" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">' . $bars . '</svg>';
}

// Clean any user supplied text before using it (prevents XSS)
function clean($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// Builds the site's absolute base URL (e.g. https://example.com/movie_booking),
// used for Stripe's success_url / cancel_url which must be full URLs.
function appBaseUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $root   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $scheme . '://' . $host . $root;
}

// Resolve a movie's poster to an actual, safe-to-use image src.
// - If the DB 'poster' value is a full link (http/https), use it directly -
//   this is the only way this project sets a poster (paste a URL in Admin).
// - Anything else (empty, or a stray bare filename) falls back to the
//   generic placeholder graphic below.
// The front-end also has an onerror handler (see posterOnError()) so a
// broken/removed link never shows a broken-image icon.
function posterUrl($movie, $base = '') {
    $poster = trim($movie['poster'] ?? '');
    // This project only supports pasting a direct image URL (no file upload
    // handler exists), so anything that isn't a real http(s) link - e.g. a
    // leftover bare filename like "m016.jpg" - would 404 both on <img> and
    // on a CSS background-image (which has no onerror to recover with).
    // Treat those as "no poster" up front instead of pointing at a dead file.
    if ($poster === '' || !preg_match('#^https?://#i', $poster)) {
        return FALLBACK_POSTER_URL;
    }
    return $poster;
}

// Inline onerror attribute value: if the real poster fails to load, drop
// back to the fallback placeholder image.
function posterOnError($base = '') {
    return "this.onerror=null;this.src='" . FALLBACK_POSTER_URL . "';";
}

// Turn whatever YouTube link an admin pastes (watch?v=, youtu.be/, shorts/,
// live/, or an already-correct /embed/ link) into a proper /embed/ URL so
// the trailer <iframe> always plays instead of showing a "blocked" icon.
// Non-YouTube links are returned untouched (e.g. a direct .mp4 or Vimeo URL).
function youtubeEmbedUrl($url) {
    $url = trim($url ?? '');
    if ($url === '') {
        return '';
    }
    $video_id = null;
    if (preg_match('#youtu\.be/([A-Za-z0-9_-]{6,})#i', $url, $m)) {
        $video_id = $m[1];
    } elseif (preg_match('#youtube\.com/(?:watch\?v=|embed/|shorts/|live/)([A-Za-z0-9_-]{6,})#i', $url, $m)) {
        $video_id = $m[1];
    } elseif (preg_match('#[?&]v=([A-Za-z0-9_-]{6,})#i', $url, $m)) {
        $video_id = $m[1];
    }
    if ($video_id) {
        return 'https://www.youtube.com/embed/' . $video_id;
    }
    // Not a recognisable YouTube link (could be Vimeo, direct mp4, etc.) - leave as-is.
    return $url;
}

// Returns true if a visitor is logged in as a normal user
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Returns true if an admin is logged in
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Redirect helper
function redirect($location) {
    header("Location: " . $location);
    exit();
}

// Force a normal user to be logged in before viewing a page
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// Force an admin to be logged in before viewing an admin page
function requireAdmin() {
    if (!isAdmin()) {
        redirect('login.php');
    }
}

// Calculate the average rating of a movie from the reviews table
function getAverageRating($conn, $movie_id) {
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

// Same as getAverageRating() but for every movie at once, in a single query.
// Used on listing pages (home page) that render many movie cards, so we
// don't run one extra SQL query per card (which was the main reason the
// home page loaded slowly with 90+ movies on it).
function getAllAverageRatings($conn) {
    $ratings = [];
    $result = $conn->query("SELECT movie_id, AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews GROUP BY movie_id");
    while ($row = $result->fetch_assoc()) {
        $ratings[(int)$row['movie_id']] = $row;
    }
    return $ratings;
}

// How many seats are already booked (Confirmed only) for a given show.
function getBookedSeatsCount($conn, $show_id) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(adult_seats + kid_seats), 0) AS n FROM bookings WHERE show_id = ? AND status = 'Confirmed'");
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    $n = $stmt->get_result()->fetch_assoc()['n'];
    $stmt->close();
    return (int)$n;
}

// How many seats are still free for a show, based on its theater's total_seats.
function getAvailableSeats($conn, $show_id, $theater_total_seats) {
    return max(0, (int)$theater_total_seats - getBookedSeatsCount($conn, $show_id));
}

// Work out whether a movie is "Coming Soon" (release date still in the
// future) or "Now Showing" (already released), and return a ready-to-use
// label + CSS class for the little badge shown on its poster card.
function movieStatusTag($movie) {
    if (!empty($movie['release_date']) && $movie['release_date'] > date('Y-m-d')) {
        return ['label' => 'Coming Soon', 'class' => 'tag-coming-soon'];
    }
    return ['label' => 'Now Showing', 'class' => 'tag-now-showing'];
}

function renderMovieCard($conn, $movie, $forced_tag = null, $with_id = false, $ratings_cache = null) {
    $tag = $forced_tag ?: movieStatusTag($movie);
    $is_coming_soon = !empty($movie['release_date']) && $movie['release_date'] > date('Y-m-d');
    ?> <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" class="movie-card" <?php echo $with_id ? 'id="movie-' . (int)$movie['movie_id'] . '"' : ''; ?>> <span class="status-tag <?php echo clean($tag['class']); ?>"><?php echo clean($tag['label']); ?></span> <div class="card-poster-wrap"> <img src="<?php echo clean(posterUrl($movie)); ?>" alt="<?php echo clean($movie['title']); ?>" loading="lazy"
                 onerror="<?php echo posterOnError(); ?>"> <div class="poster-overlay"> <span class="play-action-btn" aria-label="View Details"> <svg viewBox="0 0 24 24" width="26" height="26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 5.14v13.72c0 .8.87 1.29 1.56.87l10.99-6.86a1 1 0 0 0 0-1.7L9.56 4.27A1 1 0 0 0 8 5.14Z" fill="currentColor"/></svg> </span> </div> </div> <div class="info"> <h3 class="card-title"><?php echo clean($movie['title']); ?></h3> <div class="meta"> <span><?php echo clean($movie['genre'] ?: 'Cinema'); ?></span> <span class="meta-dot">&bull;</span> <span><?php echo (int)$movie['duration_minutes']; ?> min</span> </div> <div class="card-bottom-row"> <?php if ($is_coming_soon): ?> <span class="rating-badge release-date-badge"> <?php echo date('d M', strtotime($movie['release_date'])); ?></span> <?php else:
                    $rating = $ratings_cache !== null
                        ? ($ratings_cache[(int)$movie['movie_id']] ?? ['avg_rating' => null, 'total' => 0])
                        : getAverageRating($conn, $movie['movie_id']);
                    if ($rating['total'] > 0): ?> <span class="rating-badge star-rating-badge"> <?php echo round($rating['avg_rating'], 1); ?></span> <?php else: ?> <span class="rating-badge premiere-badge">Popular</span> <?php endif; endif; ?> </div> </div> </a> <?php
}

// Look up an active, non-expired coupon by code. Returns null if invalid.
function findActiveCoupon($conn, $code) {
    $code = trim($code);
    if ($code === '') return null;
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND (expires_on IS NULL OR expires_on >= CURDATE())");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/* ==========================================================
   Seat-map helpers (screens & seats).
   A show can optionally be tied to a specific screen
   (shows.screen_id). If it is, book_ticket.php shows a real,
   interactive seat grid. If not (older shows created before
   screens existed), the site quietly falls back to the
   original "pick a class + how many seats" flow.
   ========================================================== */

// Does the seat-map schema exist in this database? We check both the
// required tables and the `shows.screen_id` column because older installs
// can have some of the migration pieces but not all of them.
function screenIdColumnExists($conn) {
    static $exists = null;
    if ($exists !== null) return $exists;
    $r = $conn->query("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shows' AND COLUMN_NAME = 'screen_id' LIMIT 1");
    $exists = $r && $r->num_rows > 0;
    return $exists;
}

function seatMapSchemaExists($conn) {
    static $exists = null;
    if ($exists !== null) return $exists;

    $screens = $conn->query("SHOW TABLES LIKE 'screens'");
    $seats = $conn->query("SHOW TABLES LIKE 'seats'");
    $has_screen_column = screenIdColumnExists($conn);

    $exists = $screens && $screens->num_rows > 0 && $seats && $seats->num_rows > 0 && $has_screen_column;
    return $exists;
}

// All seats belonging to a screen, ordered row-by-row.
function getScreenSeats($conn, $screen_id) {
    $stmt = $conn->prepare("SELECT * FROM seats WHERE screen_id = ? ORDER BY seat_row, seat_number");
    $stmt->bind_param("i", $screen_id);
    $stmt->execute();
    $rows = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    $stmt->close();
    return $rows;
}

// Seat IDs already held by a *Confirmed* booking for this show (i.e.
// physically unavailable). Cancelled bookings free their seats up
// automatically since they're excluded here.
function getBookedSeatIds($conn, $show_id) {
    $stmt = $conn->prepare("
        SELECT bs.seat_id
        FROM booking_seats bs
        JOIN bookings b ON bs.booking_id = b.booking_id
        WHERE bs.show_id = ? AND b.status = 'Confirmed'
    ");
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    $ids = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $ids[] = (int)$r['seat_id'];
    $stmt->close();
    return $ids;
}

// Group a flat seat list into rows for easy grid rendering:
// ['A' => [seat, seat, ...], 'B' => [...], ...]
function groupSeatsByRow($seats) {
    $grouped = [];
    foreach ($seats as $s) {
        $grouped[$s['seat_row']][] = $s;
    }
    return $grouped;
}

/* ==========================================================
   Card number validation for the dummy "Credit/Debit Card"
   payment option. Digits-only length check per card network:
   Visa / MasterCard / Discover -> 16 digits, American Express
   -> 15 digits, anything else -> 15-19 digits (generic range).
   This is NOT a real payment gateway - no real card is charged,
   so we deliberately never store the full number, only the last
   4 digits (for the receipt), matching how real receipts look.
   ========================================================== */
function validateCardNumber($raw) {
    $digits = preg_replace('/\D/', '', (string)$raw);
    $len = strlen($digits);
    if ($len < 15 || $len > 19) return false;
    if (preg_match('/^4/', $digits))               return $len === 16; // Visa
    if (preg_match('/^5[1-5]/', $digits))          return $len === 16; // MasterCard
    if (preg_match('/^6(?:011|5)/', $digits))      return $len === 16; // Discover
    if (preg_match('/^3[47]/', $digits))           return $len === 15; // American Express
    return true; // other networks: generic 15-19 digit range
}

// Last 4 digits of a card number, for masked display ("**** 1234")
function cardLast4($raw) {
    $digits = preg_replace('/\D/', '', (string)$raw);
    return substr($digits, -4);
}
?>
