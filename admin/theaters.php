<?php
/* ==========================================================
   admin/theaters.php - Add, edit and delete movie theaters,
   including their live latitude/longitude for Google Maps.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$edit_theater = null;
$error = "";
$has_screens = seatMapSchemaExists($conn);

// Delete a theater
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM theaters WHERE theater_id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    redirect('theaters.php');
}

// Load a theater to edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM theaters WHERE theater_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_theater = $stmt->get_result()->fetch_assoc();
}

// Add or update a theater. Once the seat-map schema is in place, "Total
// Seats" is no longer something the admin types in - it's auto-calculated
// from this theater's actual screens (see the query below), so there is
// nothing to read from the form or save here. Sites that haven't run the
// seat-map migration keep the original manual field, since there's no
// screens/seats data to calculate it from.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = clean($_POST['theater_name']);
    $loc   = clean($_POST['location']);
    $seats = $has_screens ? null : (int)$_POST['total_seats'];
    $lat   = $_POST['latitude'] !== '' ? (float)$_POST['latitude'] : null;
    $lng   = $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;

    if (!empty($_POST['theater_id'])) {
        $tid = (int)$_POST['theater_id'];
        if ($has_screens) {
            $upd = $conn->prepare("UPDATE theaters SET theater_name=?, location=?, latitude=?, longitude=? WHERE theater_id=?");
            $upd->bind_param("ssddi", $name, $loc, $lat, $lng, $tid);
        } else {
            $upd = $conn->prepare("UPDATE theaters SET theater_name=?, location=?, total_seats=?, latitude=?, longitude=? WHERE theater_id=?");
            $upd->bind_param("ssiddi", $name, $loc, $seats, $lat, $lng, $tid);
        }
        if (!$upd->execute()) {
            $error = "Could not save changes: " . $upd->error;
        }
    } else {
        if ($has_screens) {
            // Starts at 0 real seats until screens are added below.
            $ins = $conn->prepare("INSERT INTO theaters (theater_name, location, total_seats, latitude, longitude) VALUES (?, ?, 0, ?, ?)");
            $ins->bind_param("ssdd", $name, $loc, $lat, $lng);
        } else {
            $ins = $conn->prepare("INSERT INTO theaters (theater_name, location, total_seats, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("ssidd", $name, $loc, $seats, $lat, $lng);
        }
        if (!$ins->execute()) {
            $error = "Could not add theater: " . $ins->error;
        }
    }
    if (!$error) {
        redirect('theaters.php');
    } else {
        $edit_theater = [
            'theater_id' => $_POST['theater_id'] ?? '', 'theater_name' => $name, 'location' => $loc,
            'total_seats' => $seats, 'latitude' => $lat, 'longitude' => $lng,
        ];
    }
}

// Search by theater name or location (like the movie search on index.php)
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $theaters_stmt = $conn->prepare("SELECT * FROM theaters WHERE theater_name LIKE ? OR location LIKE ? ORDER BY theater_name");
    $theaters_stmt->bind_param("ss", $like, $like);
    $theaters_stmt->execute();
    $theaters = $theaters_stmt->get_result();
} else {
    $theaters = $conn->query("SELECT * FROM theaters ORDER BY theater_name");
}

// Real seat count per theater, straight from the actual screens/seats
// tables - this is what customers really see on the seat picker, and
// (once the seat-map schema is in place) it's the only "Total Seats"
// number shown anywhere in this admin page now.
$live_seats_by_theater = [];
if ($has_screens) {
    $lc = $conn->query("
        SELECT sc.theater_id, COUNT(se.seat_id) AS live_count
        FROM screens sc LEFT JOIN seats se ON se.screen_id = sc.screen_id
        GROUP BY sc.theater_id
    ");
    while ($row = $lc->fetch_assoc()) $live_seats_by_theater[$row['theater_id']] = (int)$row['live_count'];
}

include __DIR__ . '/admin_header.php';
?> <h1>Theaters</h1> <?php if ($error): ?><div class="alert alert-error"><?php echo clean($error); ?></div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:500px;"> <h2><?php echo $edit_theater ? 'Edit Theater' : 'Add New Theater'; ?></h2> <input type="hidden" name="theater_id" value="<?php echo $edit_theater['theater_id'] ?? ''; ?>"> <label>Theater Name</label> <input type="text" name="theater_name" value="<?php echo clean($edit_theater['theater_name'] ?? ''); ?>" required> <label>Location</label> <input type="text" name="location" value="<?php echo clean($edit_theater['location'] ?? ''); ?>" required> <?php if (!$has_screens): ?> <label>Total Seats</label> <input type="number" name="total_seats" value="<?php echo clean($edit_theater['total_seats'] ?? '100'); ?>" required> <?php endif; ?> <label>Latitude</label> <input type="text" name="latitude" value="<?php echo clean($edit_theater['latitude'] ?? ''); ?>" placeholder="e.g. 24.8995649"> <label>Longitude</label> <input type="text" name="longitude" value="<?php echo clean($edit_theater['longitude'] ?? ''); ?>" placeholder="e.g. 67.1167719"> <button type="submit"><?php echo $edit_theater ? 'Update Theater' : 'Add Theater'; ?></button>
</form> <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin:28px 0 12px;"> <h2 style="margin:0;">All Theaters</h2> <form method="GET" class="nav-search" style="max-width:320px;width:100%;"> <input type="text" name="q" placeholder="Search by name or location..." aria-label="Search theaters" autocomplete="off" value="<?php echo clean($search_q); ?>"> </form> </div> <?php if ($search_q !== '' && $theaters->num_rows === 0): ?><div class="alert alert-warning">No theaters found matching "<?php echo clean($search_q); ?>".</div><?php endif; ?> <div class="admin-table-scroll"> <table class="admin-table-nowrap"> <tr><th>Name</th><th>Location</th><th>Total Seats</th><th>Coordinates</th><th>Actions</th></tr> <?php while ($t = $theaters->fetch_assoc()): $seat_total = $has_screens ? ($live_seats_by_theater[$t['theater_id']] ?? 0) : (int)$t['total_seats']; ?> <tr> <td><?php echo clean($t['theater_name']); ?></td> <td><?php echo clean($t['location']); ?></td> <td><?php echo $seat_total; ?><?php if ($has_screens && $seat_total === 0): ?> <span style="color:#888;font-size:11.5px;">(no screens yet)</span><?php endif; ?></td> <td><?php echo $t['latitude'] ? clean($t['latitude'] . ', ' . $t['longitude']) : '—'; ?></td> <td> <a href="theaters.php?edit=<?php echo $t['theater_id']; ?>">Edit</a> | <a href="screens.php">Screens</a> | <a href="theaters.php?delete=<?php echo $t['theater_id']; ?>" onclick="return confirm('Delete this theater?');">Delete</a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>