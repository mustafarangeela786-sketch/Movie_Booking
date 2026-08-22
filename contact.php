<?php
/* ==========================================================
   contact.php - Contact information + list of our cinema
   locations (theaters), pulled live from the database so it
   always reflects what the admin has set up.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

$sent = false;

// Basic contact form handler - stores nothing sensitive, just
// gives the visitor a confirmation. Wired to email support later
// if the site owner wants it.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($_POST['name'] ?? '');
    $email   = clean($_POST['email'] ?? '');
    $message = clean($_POST['message'] ?? '');
    if ($name !== '' && $email !== '' && $message !== '') {
        $sent = true;
    }
}

// Pull all cinema / theater locations currently in the system, grouped by
// city (the part of "location" after the last comma, e.g. "..., Karachi")
// so a large cinema list is easy to scan instead of one long grid.
// A cinema can also be searched by name or location, the same way movies
// are searched from the homepage search bar.
// "Total Seats" per theater comes from summing its real screens (once the
// seat-map schema is set up) rather than the old manually-typed number, so
// it always matches what the seat picker actually offers.
$has_screens = seatMapSchemaExists($conn);
$live_seats_join   = $has_screens ? "LEFT JOIN (SELECT sc.theater_id, COUNT(se.seat_id) AS live_seats FROM screens sc LEFT JOIN seats se ON se.screen_id = sc.screen_id GROUP BY sc.theater_id) ls ON ls.theater_id = theaters.theater_id" : "";
$live_seats_select = $has_screens ? ", COALESCE(ls.live_seats, 0) AS live_seats" : "";

$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $theaters_stmt = $conn->prepare("
        SELECT theaters.*, TRIM(SUBSTRING_INDEX(location, ',', -1)) AS city" . $live_seats_select . "
        FROM theaters
        $live_seats_join
        WHERE theater_name LIKE ? OR location LIKE ?
        ORDER BY city, theater_name
    ");
    $theaters_stmt->bind_param("ss", $like, $like);
    $theaters_stmt->execute();
    $theaters = $theaters_stmt->get_result();
} else {
    $theaters = $conn->query("
        SELECT theaters.*, TRIM(SUBSTRING_INDEX(location, ',', -1)) AS city" . $live_seats_select . "
        FROM theaters
        $live_seats_join
        ORDER BY city, theater_name
    ");
}
$total_theaters = $theaters ? $theaters->num_rows : 0;

include __DIR__ . '/includes/header.php';
?> <h1>Contact Us</h1>
<p style="color:#a0a0a0;">Questions about a booking, a show, or a cinema? Reach out below or visit one of our locations.</p> <h2 style="margin-top:36px;">Our Cinema Locations</h2>
<p style="color:#a0a0a0;"><?php echo $total_theaters; ?> cinemas and counting - find one near you.</p> <form action="<?php echo $asset_base; ?>contact.php" method="get" class="nav-search" role="search" style="max-width:420px;margin:0 0 24px;"> <input type="text" name="q" placeholder="Search cinemas by name or location..." aria-label="Search cinemas" autocomplete="off" value="<?php echo clean($search_q); ?>"> </form> <?php if ($search_q !== '' && $total_theaters === 0): ?> <div class="alert alert-warning">No cinemas found matching "<?php echo clean($search_q); ?>".</div> <?php endif; ?> <?php
$current_city = null;
while ($t = $theaters->fetch_assoc()):
    $theater_name = $t['theater_name'] !== '' ? $t['theater_name'] : 'Cinema';
    $location     = $t['location'] !== '' ? $t['location'] : 'Location not specified';
    $city         = $t['city'] !== '' ? $t['city'] : 'Other';
    $has_coords   = !empty($t['latitude']) && !empty($t['longitude']);
    $seat_total   = $has_screens ? (int)$t['live_seats'] : (int)$t['total_seats'];
    $map_query    = $has_coords ? $t['latitude'] . ',' . $t['longitude'] : ($theater_name . ', ' . $location);
    $embed_src    = "https://www.google.com/maps?q=" . urlencode($map_query) . "&hl=en&z=15&output=embed";
    $maps_link    = "https://www.google.com/maps/search/?api=1&query=" . urlencode($map_query);

    if ($city !== $current_city):
        if ($current_city !== null) echo '</div>'; // close previous city's grid
        $current_city = $city;
?> <h3 style="margin-top:28px;color:#ffb400;"> <?php echo clean($city); ?></h3> <div class="theater-grid">
<?php endif; ?> <div class="theater-card"> <iframe class="map-embed" src="<?php echo clean($embed_src); ?>" loading="lazy" allowfullscreen title="Map for <?php echo clean($theater_name); ?>"></iframe> <div class="info"> <h3> <?php echo clean($theater_name); ?></h3> <div class="meta"><?php echo clean($location); ?></div> <div class="meta" style="margin-top:6px;">Total Seats: <?php echo $seat_total; ?></div> <a href="<?php echo clean($maps_link); ?>" target="_blank" rel="noopener" class="btn" style="margin-top:12px;padding:8px 14px;font-size:13px;"> Open in Google Maps</a> </div> </div>
<?php
endwhile;
if ($current_city !== null) echo '</div>'; // close last city's grid
?> <h2 style="margin-top:36px;">Get in Touch</h2>
<?php if ($sent): ?> <div class="alert alert-success">Thanks for reaching out! We'll get back to you soon.</div>
<?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:500px;"> <label>Your Name</label> <input type="text" name="name" required> <label>Your Email</label> <input type="email" name="email" required> <label>Message</label> <textarea name="message" rows="4" required></textarea> <button type="submit">Send Message</button>
</form> <div class="form-box" style="margin-left:0;max-width:500px;"> <h2>Support</h2> <p style="color:#eaeaea;">Email: mustafarangeela786@gmail.com</p> <p style="color:#eaeaea;">Phone: +92 335 3924555 (Easy Paisa)</p> <p style="color:#eaeaea;">Phone: +92 322 3680866 (Jazz Cash)</p>
</div> <?php include __DIR__ . '/includes/footer.php'; ?>
