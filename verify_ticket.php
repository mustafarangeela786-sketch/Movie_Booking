<?php
/* ==========================================================
   verify_ticket.php - Public Ticket Verification Screen

   This is what opens when someone scans the QR code printed on
   a CineVerse Pass. Deliberately does NOT require login - the
   person scanning is usually cinema staff at the turnstile /
   counter, not the ticket holder. Instead of trusting the raw
   booking_id in the URL, it checks a signed token (see
   ticketVerifyToken() in includes/functions.php) so a stranger
   can't just increment the id to browse other people's bookings.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token      = isset($_GET['t']) ? trim($_GET['t']) : '';

$valid_token = ($booking_id > 0 && $token !== '' && hash_equals(ticketVerifyToken($booking_id), $token));

$booking = null;
if ($valid_token) {
    $stmt = $conn->prepare("
        SELECT b.*, s.show_date, s.show_time, m.title, m.poster, t.theater_name, t.location
        FROM bookings b
        JOIN shows s ON b.show_id = s.show_id
        JOIN movies m ON s.movie_id = m.movie_id
        JOIN theaters t ON s.theater_id = t.theater_id
        WHERE b.booking_id = ?
    ");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$seat_codes = [];
if ($booking && seatMapSchemaExists($conn)) {
    $sc_stmt = $conn->prepare("SELECT seat_code, ticket_type FROM booking_seats WHERE booking_id = ? ORDER BY seat_code");
    $sc_stmt->bind_param("i", $booking_id);
    $sc_stmt->execute();
    $sc_res = $sc_stmt->get_result();
    while ($sc = $sc_res->fetch_assoc()) $seat_codes[] = $sc;
    $sc_stmt->close();
}

// Work out the badge to show: cancelled bookings always read as invalid
// regardless of payment status, since the seats are no longer honoured.
if (!$booking) {
    $state = 'invalid';
} elseif ($booking['status'] === 'Cancelled') {
    $state = 'cancelled';
} elseif ($booking['payment_status'] === 'Paid') {
    $state = 'paid';
} elseif ($booking['payment_status'] === 'Failed') {
    $state = 'failed';
} else {
    $state = 'pending';
}

$state_map = [
    'paid'      => ['label' => 'PAID / VALID',      'sub' => 'Admit — payment confirmed.',            'color' => 'var(--neon-green)', 'bg' => 'rgba(16, 185, 129, 0.12)',  'icon' => ''],
    'pending'   => ['label' => 'UNPAID / PENDING',  'sub' => 'Do not admit — payment not received.',  'color' => 'var(--neon-amber)', 'bg' => 'rgba(245, 158, 11, 0.12)',  'icon' => ''],
    'failed'    => ['label' => 'PAYMENT FAILED',    'sub' => 'Do not admit — payment failed.',        'color' => 'var(--neon-red)',   'bg' => 'rgba(255, 51, 75, 0.12)',   'icon' => ''],
    'cancelled' => ['label' => 'CANCELLED',         'sub' => 'Do not admit — booking was cancelled.', 'color' => 'var(--neon-red)',   'bg' => 'rgba(255, 51, 75, 0.12)',   'icon' => ''],
    'invalid'   => ['label' => 'INVALID TICKET',    'sub' => 'This QR code could not be verified.',   'color' => 'var(--neon-red)',   'bg' => 'rgba(255, 51, 75, 0.12)',   'icon' => ''],
];
$info = $state_map[$state];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $info['label']; ?> — Ticket Verification</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=<?php echo file_exists(__DIR__.'/assets/css/style.css') ? filemtime(__DIR__.'/assets/css/style.css') : time(); ?>">
<style> body { align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
    .verify-wrap { width: 100%; max-width: 460px; margin: 0 auto; }
    .verify-badge {
        display: flex; align-items: center; gap: 14px;
        padding: 20px 22px; border-radius: var(--radius-lg);
        margin-bottom: 18px; border: 1px solid <?php echo $info['color']; ?>;
        background: <?php echo $info['bg']; ?>;
    }
    .verify-badge .icon { font-size: 32px; line-height: 1; }
    .verify-badge .label { font-family: var(--font-tech); font-weight: 700; font-size: 20px; color: <?php echo $info['color']; ?>; letter-spacing: 0.5px; }
    .verify-badge .sub { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }
    .verify-card { background: var(--bg-surface); border: 1px solid var(--border-card); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); }
    .verify-card-header { padding: 18px 22px; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 10px; }
    .verify-card-header .dot { width: 8px; height: 8px; border-radius: 50%; background: <?php echo $info['color']; ?>; box-shadow: 0 0 8px <?php echo $info['color']; ?>; }
    table.verify-table { width: 100%; }
    table.verify-table th { text-align: left; padding: 10px 22px; color: var(--text-muted); font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; border-bottom: 1px solid var(--border-subtle); white-space: nowrap; }
    table.verify-table td { padding: 10px 22px; text-align: right; font-weight: 700; color: var(--text-primary); border-bottom: 1px solid var(--border-subtle); }
    .verify-footer { text-align: center; margin-top: 20px; font-size: 12.5px; color: var(--text-muted); }
    .verify-footer a { color: var(--neon-cyan); }
</style>
</head>
<body>
<div class="verify-wrap"> <div class="verify-badge"> <span class="icon"><?php echo $info['icon']; ?></span> <div> <div class="label"><?php echo $info['label']; ?></div> <div class="sub"><?php echo $info['sub']; ?></div> </div> </div> <?php if ($booking): ?> <div class="verify-card"> <div class="verify-card-header"> <span class="dot"></span> <div> <div style="font-family:var(--font-heading);font-weight:800;font-size:17px;"><?php echo clean($booking['title']); ?></div> <div style="font-size:12.5px;color:var(--text-muted);">Pass #<?php echo (int)$booking['booking_id']; ?> &bull; <?php echo clean($booking['theater_name']); ?></div> </div> </div> <table class="verify-table"> <tr><th>Date &amp; Time</th><td><?php echo date('D, d M Y', strtotime($booking['show_date'])); ?> &bull; <?php echo date('h:i A', strtotime($booking['show_time'])); ?></td></tr> <tr><th>Class</th><td><?php echo clean($booking['seat_class']); ?></td></tr> <?php if (!empty($seat_codes)): ?> <tr><th>Seats</th><td><?php echo clean(implode(', ', array_map(fn($s) => $s['seat_code'], $seat_codes))); ?></td></tr> <?php else: ?> <tr><th>Seats</th><td><?php echo (int)$booking['adult_seats']; ?> Adult, <?php echo (int)$booking['kid_seats']; ?> Kid</td></tr> <?php endif; ?> <tr><th>Amount</th><td>Rs. <?php echo number_format($booking['total_amount'], 2); ?></td></tr> <tr><th>Payment</th><td><?php echo clean($booking['payment_method'] ?? '-'); ?></td></tr> <tr><th>Booking Status</th><td style="color:<?php echo $booking['status']==='Confirmed' ? 'var(--neon-green)' : 'var(--neon-red)'; ?>;"><?php echo clean($booking['status']); ?></td></tr> </table> </div> <?php endif; ?> <div class="verify-footer"> Verified live from the CineVerse booking database &bull; <a href="index.php">Back to MovieBook</a> </div>
</div>
</body>
</html>
