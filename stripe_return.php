<?php
/* ==========================================================
   stripe_return.php

   Stripe redirects the customer here after Checkout, either:
     - success: ?booking_id=X&session_id=cs_test_...
     - cancel:  ?booking_id=X&cancelled=1

   IMPORTANT: we never trust the redirect by itself (anyone
   could craft that URL). On a "success" redirect we always
   re-verify the payment server-side with Stripe's API before
   marking the booking as Paid.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$cancelled  = isset($_GET['cancelled']);
$session_id = $_GET['session_id'] ?? '';

// Load the booking + show details, making sure it belongs to this user
$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, s.theater_id, m.title, m.genre, t.theater_name, t.location
    FROM bookings b
    JOIN shows s ON b.show_id = s.show_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    redirect('my_bookings.php');
}

// ---- Customer cancelled on Stripe's page (or closed the tab) ----
if ($cancelled) {
    if ($booking['payment_status'] === 'Pending') {
        $upd = $conn->prepare("UPDATE bookings SET status = 'Cancelled', payment_status = 'Failed' WHERE booking_id = ?");
        $upd->bind_param("i", $booking_id);
        $upd->execute();
    }
    $GLOBALS['page_genre'] = $booking['genre'];
    include __DIR__ . '/includes/header.php';
    ?> <div class="form-box" style="margin-left:0;max-width:520px;"> <h2>Payment Cancelled</h2> <p>Your payment for <strong><?php echo clean($booking['title']); ?></strong> was not completed, so the seat(s) have been released.</p> <p><a href="index.php">&larr; Browse movies</a> or try booking again.</p> </div> <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

// ---- Already confirmed earlier (e.g. user refreshed this page) ----
if ($booking['payment_status'] === 'Paid') {
    redirect('booking_confirm.php?id=' . $booking_id);
}

// ---- Verify the payment with Stripe server-side ----
if ($session_id === '') {
    redirect('my_bookings.php');
}

require_once __DIR__ . '/includes/stripe_helper.php';
$session = stripe_retrieve_checkout_session($session_id);

$paid = isset($session['payment_status']) && $session['payment_status'] === 'paid';
$matches_booking = isset($session['metadata']['booking_id']) && (int)$session['metadata']['booking_id'] === $booking_id;

if ($paid && $matches_booking) {
    $upd = $conn->prepare("UPDATE bookings SET payment_status = 'Paid', stripe_session_id = ? WHERE booking_id = ?");
    $upd->bind_param("si", $session_id, $booking_id);
    $upd->execute();

    // Now that payment is confirmed, send the e-ticket confirmation email
    require_once __DIR__ . '/Email/BookingMailer.php';
    send_booking_confirmation_email($conn, [
        'booking_id'     => $booking_id,
        'customer_name'  => $_SESSION['user_name'] ?? '',
        'customer_email' => $booking['contact_email'],
        'movie_title'    => $booking['title'],
        'theater_name'   => $booking['theater_name'],
        'location'       => $booking['location'],
        'show_date'      => date('D, d M Y', strtotime($booking['show_date'])),
        'show_time'      => date('h:i A', strtotime($booking['show_time'])),
        'seat_class'     => $booking['seat_class'],
        'adult_seats'    => $booking['adult_seats'],
        'kid_seats'      => $booking['kid_seats'],
        'total_amount'   => number_format($booking['total_amount'], 2),
    ]);

    redirect('booking_confirm.php?id=' . $booking_id);
}

// ---- Payment did not go through - release the seats ----
$upd = $conn->prepare("UPDATE bookings SET status = 'Cancelled', payment_status = 'Failed' WHERE booking_id = ?");
$upd->bind_param("i", $booking_id);
$upd->execute();

$GLOBALS['page_genre'] = $booking['genre'];
include __DIR__ . '/includes/header.php';
?>
<div class="form-box" style="margin-left:0;max-width:520px;"> <h2>Payment Not Confirmed</h2> <p>We couldn't verify your Stripe payment for <strong><?php echo clean($booking['title']); ?></strong>, so the seat(s) have been released. No booking was made.</p> <p><a href="index.php">&larr; Browse movies</a> or try booking again.</p>
</div>
<?php
include __DIR__ . '/includes/footer.php';
