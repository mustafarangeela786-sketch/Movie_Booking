<?php
/* ==========================================================
   admin/screens.php - Manage screens for each theater.
   Adding a screen auto-generates its seat grid (rows x seats
   per row), split into Gold / Platinum / Box classes by
   default (front rows Gold, middle Platinum, back rows Box -
   the premium seats). Fine-tune individual seats on
   screen_seats.php after generating.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if (!seatMapSchemaExists($conn)) {
    include __DIR__ . '/admin_header.php';
    echo '<h1>Screens &amp; Seats</h1>';
    echo '<div class="alert alert-error">This feature needs one extra database update. Please import <code>database/migration_v5_seat_map.sql</code> in phpMyAdmin, then reload this page.</div>';
    include __DIR__ . '/admin_footer.php';
    exit;
}

// Delete a screen (cascades to its seats)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM screens WHERE screen_id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    redirect('screens.php');
}

$error = "";

// Load a screen to resize
$edit_screen = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $es = $conn->prepare("SELECT * FROM screens WHERE screen_id = ?");
    $es->bind_param("i", $eid);
    $es->execute();
    $edit_screen = $es->get_result()->fetch_assoc();
}

// Resize an existing screen's seat map (add/remove rows or seats-per-row)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['resize_screen_id'])) {
    $screen_id     = (int)$_POST['resize_screen_id'];
    $rows_count    = max(1, min(26, (int)$_POST['rows_count']));
    $seats_per_row = max(1, min(40, (int)$_POST['seats_per_row']));
    $row_letters   = range('A', 'Z');

    $gold_rows     = (int)round($rows_count * 0.4);
    $platinum_rows = (int)round($rows_count * 0.4);

    // Which seat_codes already exist for this screen (keep their class as-is)?
    $existing = [];
    $eres = $conn->prepare("SELECT seat_code FROM seats WHERE screen_id = ?");
    $eres->bind_param("i", $screen_id);
    $eres->execute();
    $er = $eres->get_result();
    while ($row = $er->fetch_assoc()) $existing[$row['seat_code']] = true;
    $eres->close();

    // Add any newly-needed seats (this grows the seat map without touching existing seats/bookings).
    $seat_ins = $conn->prepare("INSERT INTO seats (screen_id, seat_row, seat_number, seat_code, seat_class) VALUES (?, ?, ?, ?, ?)");
    $wanted = [];
    for ($r = 0; $r < $rows_count; $r++) {
        $row_letter = $row_letters[$r] ?? ('R' . $r);
        $class = $r < $gold_rows ? 'Gold' : ($r < $gold_rows + $platinum_rows ? 'Platinum' : 'Box');
        for ($n = 1; $n <= $seats_per_row; $n++) {
            $seat_code = $row_letter . $n;
            $wanted[$seat_code] = true;
            if (!isset($existing[$seat_code])) {
                $seat_ins->bind_param("isiss", $screen_id, $row_letter, $n, $seat_code, $class);
                $seat_ins->execute();
            }
        }
    }
    $seat_ins->close();

    // If the map shrank, drop seats that fall outside the new grid - but only
    // if no one has ever booked them, so we never break a past booking.
    $removable = array_diff(array_keys($existing), array_keys($wanted));
    $skipped_booked = 0;
    if (!empty($removable)) {
        foreach ($removable as $code) {
            $chk = $conn->prepare("SELECT bs.seat_id FROM booking_seats bs JOIN seats s ON bs.seat_id = s.seat_id WHERE s.screen_id = ? AND s.seat_code = ? LIMIT 1");
            $chk->bind_param("is", $screen_id, $code);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                $skipped_booked++;
            } else {
                $del = $conn->prepare("DELETE FROM seats WHERE screen_id = ? AND seat_code = ?");
                $del->bind_param("is", $screen_id, $code);
                $del->execute();
            }
            $chk->close();
        }
    }

    $upd = $conn->prepare("UPDATE screens SET rows_count = ?, seats_per_row = ? WHERE screen_id = ?");
    $upd->bind_param("iii", $rows_count, $seats_per_row, $screen_id);
    $upd->execute();

    $msg = $skipped_booked > 0
        ? ("resized=1&kept=" . $skipped_booked)
        : "resized=1";
    redirect("screens.php?$msg");
}

// Add a new screen + auto-generate its seat grid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theater_id    = (int)$_POST['theater_id'];
    $screen_name   = clean($_POST['screen_name']);
    $rows_count    = max(1, min(26, (int)$_POST['rows_count']));
    $seats_per_row = max(1, min(40, (int)$_POST['seats_per_row']));

    if ($theater_id <= 0 || $screen_name === '') {
        $error = "Please choose a theater and enter a screen name.";
    } else {
        $ins = $conn->prepare("INSERT INTO screens (theater_id, screen_name, rows_count, seats_per_row) VALUES (?, ?, ?, ?)");
        $ins->bind_param("isii", $theater_id, $screen_name, $rows_count, $seats_per_row);
        $ins->execute();
        $screen_id = $ins->insert_id;
        $ins->close();

        // Auto-assign classes by row: front ~40% Gold, middle ~40%
        // Platinum, back ~20% Box (premium/back-row seating).
        $gold_rows     = (int)round($rows_count * 0.4);
        $platinum_rows = (int)round($rows_count * 0.4);
        $row_letters   = range('A', 'Z');

        $seat_ins = $conn->prepare("INSERT INTO seats (screen_id, seat_row, seat_number, seat_code, seat_class) VALUES (?, ?, ?, ?, ?)");
        for ($r = 0; $r < $rows_count; $r++) {
            $row_letter = $row_letters[$r] ?? ('R' . $r);
            if ($r < $gold_rows) {
                $class = 'Gold';
            } elseif ($r < $gold_rows + $platinum_rows) {
                $class = 'Platinum';
            } else {
                $class = 'Box';
            }
            for ($n = 1; $n <= $seats_per_row; $n++) {
                $seat_code = $row_letter . $n;
                $seat_ins->bind_param("isiss", $screen_id, $row_letter, $n, $seat_code, $class);
                $seat_ins->execute();
            }
        }
        $seat_ins->close();

        redirect('screens.php?added=1');
    }
}

$theaters = $conn->query("SELECT theater_id, theater_name FROM theaters ORDER BY theater_name");
$theater_list = [];
while ($t = $theaters->fetch_assoc()) $theater_list[] = $t;

// Search the screens list by theater name (same idea as the Theaters page).
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';

$screens_sql = "
    SELECT sc.*, t.theater_name,
        (SELECT COUNT(*) FROM seats se WHERE se.screen_id = sc.screen_id) AS seat_count
    FROM screens sc
    JOIN theaters t ON sc.theater_id = t.theater_id
    " . ($search_q !== '' ? "WHERE t.theater_name LIKE ?" : "") . "
    ORDER BY t.theater_name, sc.screen_name
";
$screens_stmt = $conn->prepare($screens_sql);
if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $screens_stmt->bind_param("s", $like);
}
$screens_stmt->execute();
$screens = $screens_stmt->get_result();

include __DIR__ . '/admin_header.php';
?> <h1>Screens &amp; Seats</h1>
<?php if (isset($_GET['added'])): ?><div class="alert alert-success">Screen added and its seat map was generated.</div><?php endif; ?>
<?php if (isset($_GET['resized'])): ?><div class="alert alert-success">Seat map resized.<?php if (isset($_GET['kept'])): ?> (<?php echo (int)$_GET['kept']; ?> already-booked seat(s) were kept as-is so past bookings stay valid.)<?php endif; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <?php if ($edit_screen): ?> <form method="POST" class="form-box" style="margin-left:0;max-width:520px;"> <h2>Resize "<?php echo clean($edit_screen['screen_name']); ?>"</h2> <input type="hidden" name="resize_screen_id" value="<?php echo $edit_screen['screen_id']; ?>"> <label>Rows</label> <input type="number" name="rows_count" min="1" max="26" value="<?php echo (int)$edit_screen['rows_count']; ?>" required> <label>Seats Per Row</label> <input type="number" name="seats_per_row" min="1" max="40" value="<?php echo (int)$edit_screen['seats_per_row']; ?>" required> <button type="submit">Save New Size</button> <a href="screens.php" style="display:block;margin-top:10px;font-size:13px;color:var(--text-muted);">Cancel</a> </form> <?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:520px;"> <h2>Add a New Screen</h2> <label>Theater</label> <select name="theater_id" required> <?php foreach ($theater_list as $t): ?> <option value="<?php echo $t['theater_id']; ?>"><?php echo clean($t['theater_name']); ?></option> <?php endforeach; ?> </select> <label>Screen Name</label> <input type="text" name="screen_name" placeholder="e.g. Screen 1, IMAX" required> <label>Rows</label> <input type="number" name="rows_count" min="1" max="26" value="8" required> <label>Seats Per Row</label> <input type="number" name="seats_per_row" min="1" max="40" value="10" required> <button type="submit">Add Screen &amp; Generate Seats</button>
</form> <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin:28px 0 12px;"> <h2 style="margin:0;">All Screens</h2> <form method="GET" class="nav-search" style="max-width:320px;width:100%;"> <input type="text" name="q" placeholder="Search by theater..." aria-label="Search screens by theater" autocomplete="off" value="<?php echo clean($search_q); ?>"> </form> </div> <?php if ($search_q !== '' && $screens->num_rows === 0): ?><div class="alert alert-warning">No screens found for a theater matching "<?php echo clean($search_q); ?>".</div><?php endif; ?> <div class="admin-table-scroll"> <table class="admin-table-nowrap"> <tr><th>Theater</th><th>Screen</th><th>Layout</th><th>Total Seats</th><th>Actions</th></tr> <?php while ($s = $screens->fetch_assoc()): ?> <tr> <td><?php echo clean($s['theater_name']); ?></td> <td><?php echo clean($s['screen_name']); ?></td> <td><?php echo (int)$s['rows_count']; ?> rows &times; <?php echo (int)$s['seats_per_row']; ?></td> <td><?php echo (int)$s['seat_count']; ?></td> <td> <a href="screens.php?edit=<?php echo $s['screen_id']; ?>">Resize</a> | <a href="screen_seats.php?screen_id=<?php echo $s['screen_id']; ?>">Edit Seats</a> | <a href="screens.php?delete=<?php echo $s['screen_id']; ?>" onclick="return confirm('Delete this screen and all its seats? Shows using it will be unassigned.');">Delete</a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>