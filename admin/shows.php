<?php
/* ==========================================================
   admin/shows.php - Schedule showtimes for a movie at a
   theater, and set ticket prices for Gold/Platinum/Box class.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Delete a show
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM shows WHERE show_id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    redirect('shows.php');
}

$has_screens = seatMapSchemaExists($conn);

$error = "";

// Add a new show
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id   = (int)$_POST['movie_id'];
    $theater_id = (int)$_POST['theater_id'];
    $screen_id  = ($has_screens && !empty($_POST['screen_id'])) ? (int)$_POST['screen_id'] : null;
    $show_date  = $_POST['show_date'];
    $show_time  = $_POST['show_time'];
    $gold       = (float)$_POST['price_gold'];
    $platinum   = (float)$_POST['price_platinum'];
    $box        = (float)$_POST['price_box'];

    if ($has_screens && !$screen_id) {
        $error = "Please choose a screen for this show, so it always gets a real seat map. Add one under \"Screens & Seats\" first if this theater has none yet.";
    } elseif ($has_screens) {
        $ins = $conn->prepare("INSERT INTO shows (movie_id, theater_id, screen_id, show_date, show_time, price_gold, price_platinum, price_box) VALUES (?,?,?,?,?,?,?,?)");
        $ins->bind_param("iiissddd", $movie_id, $theater_id, $screen_id, $show_date, $show_time, $gold, $platinum, $box);
    } else {
        $ins = $conn->prepare("INSERT INTO shows (movie_id, theater_id, show_date, show_time, price_gold, price_platinum, price_box) VALUES (?,?,?,?,?,?,?)");
        $ins->bind_param("iissddd", $movie_id, $theater_id, $show_date, $show_time, $gold, $platinum, $box);
    }
    if (!$error) {
        $ins->execute();
        redirect('shows.php');
    }
}

$movies   = $conn->query("SELECT movie_id, title FROM movies ORDER BY title");
$theaters = $conn->query("SELECT theater_id, theater_name FROM theaters ORDER BY theater_name");
$theater_list = [];
while ($t = $theaters->fetch_assoc()) $theater_list[] = $t;

$screens_by_theater = [];
if ($has_screens) {
    $sres = $conn->query("SELECT screen_id, theater_id, screen_name FROM screens ORDER BY screen_name");
    while ($sc = $sres->fetch_assoc()) {
        $screens_by_theater[$sc['theater_id']][] = $sc;
    }
}

// Search the shows list by theater name (same idea as the theater search
// on the Theaters page) so a long schedule can be narrowed down fast.
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';

$shows_sql = "
    SELECT s.*, m.title, t.theater_name" . ($has_screens ? ", sc.screen_name" : "") . "
    FROM shows s
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    " . ($has_screens ? "LEFT JOIN screens sc ON s.screen_id = sc.screen_id" : "") . "
    " . ($search_q !== '' ? "WHERE t.theater_name LIKE ?" : "") . "
    ORDER BY s.show_date DESC, s.show_time DESC
";
$shows_stmt = $conn->prepare($shows_sql);
if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $shows_stmt->bind_param("s", $like);
}
$shows_stmt->execute();
$shows = $shows_stmt->get_result();

include __DIR__ . '/admin_header.php';
?> <h1>Shows & Rates</h1> <?php if ($error): ?><div class="alert alert-error"><?php echo clean($error); ?></div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:600px;"> <h2>Schedule a New Show</h2> <label>Movie</label> <select name="movie_id" required> <?php while ($m = $movies->fetch_assoc()): ?> <option value="<?php echo $m['movie_id']; ?>"><?php echo clean($m['title']); ?></option> <?php endwhile; ?> </select> <label>Theater</label> <select name="theater_id" id="theaterSelect" required> <?php foreach ($theater_list as $t): ?> <option value="<?php echo $t['theater_id']; ?>"><?php echo clean($t['theater_name']); ?></option> <?php endforeach; ?> </select> <?php if ($has_screens): ?> <label>Screen</label> <select name="screen_id" id="screenSelect" required> <?php foreach ($theater_list as $t): ?> <?php foreach (($screens_by_theater[$t['theater_id']] ?? []) as $sc): ?> <option value="<?php echo $sc['screen_id']; ?>" data-theater="<?php echo $t['theater_id']; ?>"> <?php echo clean($t['theater_name']); ?> - <?php echo clean($sc['screen_name']); ?> </option> <?php endforeach; ?> <?php endforeach; ?> </select> <p id="noScreenWarning" style="display:none;font-size:12.5px;color:var(--cinema-red);margin-top:-8px;margin-bottom:16px;">This theater has no screen yet. <a href="screens.php" style="color:var(--cinema-red);text-decoration:underline;">Add one first</a> before scheduling a show here.</p> <script> (function () {
        var theaterSelect = document.getElementById('theaterSelect');
        var screenSelect = document.getElementById('screenSelect');
        var warning = document.getElementById('noScreenWarning');
        var submitBtn = document.querySelector('form button[type="submit"]');
        var allOptions = Array.prototype.slice.call(screenSelect.options);
        function filterScreens() {
            var theaterId = theaterSelect.value;
            screenSelect.innerHTML = '';
            var matches = allOptions.filter(function (opt) { return opt.getAttribute('data-theater') === theaterId; });
            matches.forEach(function (opt) { screenSelect.appendChild(opt.cloneNode(true)); });
            var hasScreen = matches.length > 0;
            screenSelect.style.display = hasScreen ? '' : 'none';
            warning.style.display = hasScreen ? 'none' : 'block';
            submitBtn.disabled = !hasScreen;
        }
        theaterSelect.addEventListener('change', filterScreens);
        filterScreens();
    })(); </script> <?php else: ?> <p style="font-size:12px;color:#888;">Want a real interactive seat map instead of class+quantity? Import <code>database/migration_v5_seat_map.sql</code>, then set up screens under "Screens &amp; Seats".</p> <?php endif; ?> <label>Show Date</label> <input type="date" name="show_date" required> <label>Show Time</label> <input type="time" name="show_time" required> <label>Gold Class Price</label> <input type="number" step="0.01" name="price_gold" required> <label>Platinum Class Price</label> <input type="number" step="0.01" name="price_platinum" required> <label>Box Class Price</label> <input type="number" step="0.01" name="price_box" required> <button type="submit">Add Show</button>
</form> <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin:28px 0 12px;"> <h2 style="margin:0;">All Shows</h2> <form method="GET" class="nav-search" style="max-width:320px;width:100%;"> <input type="text" name="q" placeholder="Search by theater..." aria-label="Search shows by theater" autocomplete="off" value="<?php echo clean($search_q); ?>"> </form> </div> <?php if ($search_q !== '' && $shows->num_rows === 0): ?><div class="alert alert-warning">No shows found for a theater matching "<?php echo clean($search_q); ?>".</div><?php endif; ?> <div class="admin-table-scroll"> <table class="admin-table-nowrap"> <tr><th>Movie</th><th>Theater</th><?php if ($has_screens): ?><th>Screen</th><?php endif; ?><th>Date</th><th>Time</th><th>Gold</th><th>Platinum</th><th>Box</th><th>Actions</th></tr> <?php while ($s = $shows->fetch_assoc()): ?> <tr> <td><?php echo clean($s['title']); ?></td> <td><?php echo clean($s['theater_name']); ?></td> <?php if ($has_screens): ?><td><?php echo clean($s['screen_name'] ?? '—'); ?></td><?php endif; ?> <td><?php echo date('d M Y', strtotime($s['show_date'])); ?></td> <td><?php echo date('h:i A', strtotime($s['show_time'])); ?></td> <td>Rs. <?php echo number_format($s['price_gold'],2); ?></td> <td>Rs. <?php echo number_format($s['price_platinum'],2); ?></td> <td>Rs. <?php echo number_format($s['price_box'],2); ?></td> <td><a href="shows.php?delete=<?php echo $s['show_id']; ?>" onclick="return confirm('Delete this show?');">Delete</a></td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>