<?php
/* ==========================================================
   my_bookings.php - User Bookings & E-Tickets Dashboard
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$cancel_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancel_id = (int)$_POST['cancel_booking_id'];
    $chk = $conn->prepare("
        SELECT b.booking_id, s.show_date, s.show_time
        FROM bookings b JOIN shows s ON b.show_id = s.show_id
        WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'Confirmed'
    ");
    $chk->bind_param("ii", $cancel_id, $_SESSION['user_id']);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    $chk->close();

    if ($row && strtotime($row['show_date'] . ' ' . $row['show_time']) > time()) {
        $upd = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ? AND user_id = ?");
        $upd->bind_param("ii", $cancel_id, $_SESSION['user_id']);
        $upd->execute();
        $upd->close();
        // Once a confirmed booking is undone, send the user back to the homepage.
        redirect('index.php?booking_cancelled=1');
    } else {
        $cancel_message = "That booking can no longer be cancelled.";
    }
}

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, m.title, t.theater_name
    FROM bookings b
    JOIN shows s ON b.show_id = s.show_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.user_id = ?
    ORDER BY s.show_date DESC, s.show_time DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$has_seat_map = seatMapSchemaExists($conn);

$upcoming = [];
$previous = [];
while ($b = $result->fetch_assoc()) {
    if ($has_seat_map) {
        $seat_codes = [];
        $sc_stmt = $conn->prepare("SELECT seat_code FROM booking_seats WHERE booking_id = ? ORDER BY seat_code");
        $sc_stmt->bind_param("i", $b['booking_id']);
        $sc_stmt->execute();
        $sc_res = $sc_stmt->get_result();
        while ($sc = $sc_res->fetch_assoc()) $seat_codes[] = $sc['seat_code'];
        $sc_stmt->close();
        $b['seat_codes'] = $seat_codes;
    }
    $is_upcoming = strtotime($b['show_date'] . ' ' . $b['show_time']) > time() && $b['status'] === 'Confirmed';
    if ($is_upcoming) $upcoming[] = $b; else $previous[] = $b;
}

include __DIR__ . '/includes/header.php';

function renderBookingsTable($rows, $allow_cancel) {
    if (empty($rows)) {
        echo '<div style="padding:24px;text-align:center;color:var(--text-muted);">No bookings recorded in this section.</div>';
        return;
    }
    echo '<div style="overflow-x:auto;"><table class="bookings-table"><tr><th>Booking ID</th><th>Movie</th><th>Theater</th><th>Show Date & Time</th><th>Class</th><th>Seats</th><th>Total</th><th>Status</th><th>Actions</th></tr>';
    foreach ($rows as $b) {
        $seats_label = !empty($b['seat_codes']) ? implode(', ', $b['seat_codes']) : ((int)$b['adult_seats'] + (int)$b['kid_seats']) . ' seat(s)';
        $status_color = $b['status'] === 'Confirmed' ? 'var(--neon-green)' : 'var(--neon-red)';
        echo '<tr>';
        echo '<td style="font-family:var(--font-tech);font-weight:700;color:var(--neon-cyan);">#' . (int)$b['booking_id'] . '</td>';
        echo '<td><strong style="color:var(--text-primary);">' . clean($b['title']) . '</strong></td>';
        echo '<td>' . clean($b['theater_name']) . '</td>';
        echo '<td style="white-space:nowrap;">' . date('d M Y', strtotime($b['show_date'])) . ' <span style="color:var(--neon-amber);font-weight:700;">' . date('h:i A', strtotime($b['show_time'])) . '</span></td>';
        echo '<td><span class="quality-badge">' . clean($b['seat_class']) . '</span></td>';
        echo '<td style="font-family:var(--font-tech);font-weight:700;">' . clean($seats_label) . '</td>';
        echo '<td style="font-weight:800;color:var(--text-primary);">Rs. ' . number_format($b['total_amount'], 2) . '</td>';
        echo '<td><span style="color:' . $status_color . ';font-weight:800;font-size:12.5px;">' . clean($b['status']) . '</span></td>';
        echo '<td style="white-space:nowrap;">';
        echo '<a class="btn" style="padding:6px 14px;font-size:12px;min-width:88px;" href="booking_confirm.php?id=' . (int)$b['booking_id'] . '"> E-Ticket</a> ';
        if ($allow_cancel && $b['status'] === 'Confirmed') {
            echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to cancel this booking?\');">';
            echo '<input type="hidden" name="cancel_booking_id" value="' . (int)$b['booking_id'] . '">';
            echo '<button type="submit" style="padding:6px 14px;font-size:12px;min-width:88px;text-align:center;background:rgba(255,51,75,0.15);color:var(--neon-red);border:1px solid rgba(255,51,75,0.3);border-radius:var(--radius-full);cursor:pointer;">Cancel</button>';
            echo '</form>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table></div>';
}
?> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;"> <div> <h1 style="margin-bottom:4px;">My Cinema Passes</h1> <p style="margin-bottom:0;color:var(--text-muted);">Manage upcoming reservations and access your digital tickets anytime.</p> </div> <a href="index.php" class="btn"> Book Another Movie</a>
</div> <?php if ($cancel_message): ?><div class="alert alert-success"><?php echo clean($cancel_message); ?></div><?php endif; ?> <h2 style="margin-top:24px;margin-bottom:12px;">Upcoming Shows</h2>
<div class="form-box" style="padding:10px;margin-bottom:32px;"> <?php renderBookingsTable($upcoming, true); ?>
</div> <h2 style="margin-top:36px;margin-bottom:12px;">Booking History</h2>
<div class="form-box" style="padding:10px;"> <?php renderBookingsTable($previous, false); ?>
</div> <?php include __DIR__ . '/includes/footer.php'; ?>
