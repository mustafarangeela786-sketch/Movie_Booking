<?php
/* ==========================================================
   admin/bookings.php - View every booking in the system, with
   the ability to cancel one (e.g. a fraudulent or duplicate
   booking) or restore it back to Confirmed.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Toggle a booking's status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE bookings SET status = IF(status='Confirmed','Cancelled','Confirmed') WHERE booking_id = $id");
    redirect('bookings.php');
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "
    SELECT b.*, u.full_name, u.email AS user_email, m.title, s.show_date, s.show_time
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN shows s ON b.show_id = s.show_id
    JOIN movies m ON s.movie_id = m.movie_id
";
if ($q !== '') {
    $like = '%' . $conn->real_escape_string($q) . '%';
    $sql .= " WHERE m.title LIKE '$like' OR u.full_name LIKE '$like' OR u.email LIKE '$like' OR b.booking_id = " . (int)$q;
}
$sql .= " ORDER BY b.booking_date DESC LIMIT 300";
$bookings = $conn->query($sql);

$has_seat_map = seatMapSchemaExists($conn);
function bookingSeatCodes($conn, $booking_id) {
    $stmt = $conn->prepare("SELECT seat_code FROM booking_seats WHERE booking_id = ? ORDER BY seat_code");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $codes = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $codes[] = $r['seat_code'];
    $stmt->close();
    return $codes;
}

include __DIR__ . '/admin_header.php';
?> <h1>Manage Bookings</h1> <form method="GET" class="form-box" style="margin-left:0;max-width:420px;">  <input type="text" name="q" value="<?php echo clean($q); ?>" placeholder="e.g. 42, Titanic, ali@example.com"> <button type="submit">Search</button>
</form> <div class="admin-table-scroll"> <table class="admin-table-nowrap" style="margin-top:20px;"> <tr> <th>ID</th><th>Customer</th><th>Movie</th><th>Show</th><th>Class</th> <th>Seats</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th> </tr> <?php while ($b = $bookings->fetch_assoc()): ?> <tr> <td>#<?php echo $b['booking_id']; ?></td> <td style="white-space:normal;"><?php echo clean($b['full_name']); ?><br><span class="meta"><?php echo clean($b['user_email']); ?></span><?php if (!empty($b['contact_phone'])): ?><br><span class="meta"><?php echo clean($b['contact_phone']); ?></span><?php endif; ?></td> <td><?php echo clean($b['title']); ?></td> <td><?php echo date('d M Y', strtotime($b['show_date'])); ?> <?php echo date('h:i A', strtotime($b['show_time'])); ?></td> <td><?php echo clean($b['seat_class']); ?></td> <td><?php
            $codes = $has_seat_map ? bookingSeatCodes($conn, $b['booking_id']) : [];
            echo $codes ? clean(implode(', ', $codes)) : ((int)$b['adult_seats'] + (int)$b['kid_seats']);
        ?></td> <td>Rs. <?php echo number_format($b['total_amount'], 2); ?></td> <td><?php echo clean($b['payment_method']); ?></td> <td><?php echo clean($b['status']); ?></td> <td> <a href="?toggle=<?php echo $b['booking_id']; ?><?php echo $q !== '' ? '&q=' . urlencode($q) : ''; ?>"
               onclick="return confirm('<?php echo $b['status'] === 'Confirmed' ? 'Cancel' : 'Restore'; ?> this booking?');"
               style="font-size:13px;"> <?php echo $b['status'] === 'Confirmed' ? 'Cancel' : 'Restore'; ?> </a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>