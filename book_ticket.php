<?php
/* ==========================================================
   book_ticket.php
   - If the show has a screen assigned (shows.screen_id): a real
     interactive seat map - pick individual seats, mark each as
     Adult or Kid, live total, and the server double-checks no
     one else grabbed the same seat first.
   - If not (older shows / sites that haven't run
     migration_v5_seat_map.sql): falls back to the original
     "seat class + how many seats" flow, unchanged.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$show_id = isset($_GET['show_id']) ? (int)$_GET['show_id'] : 0;

$stmt = $conn->prepare("
    SELECT s.*, m.title, m.poster, m.genre, t.theater_name, t.location, t.latitude, t.longitude, t.total_seats
    FROM shows s
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE s.show_id = ?
");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$show = $stmt->get_result()->fetch_assoc();

if (!$show) {
    redirect('index.php');
}

$seat_map_mode = seatMapSchemaExists($conn) && !empty($show['screen_id']);

// Pre-fill the confirmation email/phone with the account's details
$user_stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$account = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

require_once __DIR__ . '/config/stripe.php';
$stripe_configured = strpos(STRIPE_SECRET_KEY, 'REPLACE_WITH_YOUR') === false;

$payment_methods = ['Credit/Debit Card', 'EasyPaisa', 'JazzCash', 'PayPal', 'UPaisa', 'Cash at Counter'];
// Contact / account number to send payment to for each mobile-wallet method
$payment_contacts = [
    'EasyPaisa' => '+92 322 3680866',
    'JazzCash'  => '+92 342 2655726',
    'PayPal'    => '+92 322 2838344',
    'UPaisa'    => '+92 301 0805442',
];
$error = "";
$all_seats = [];
$booked_seat_ids = [];
$seat_rows_grouped = [];
$available_seats = getAvailableSeats($conn, $show_id, $show['total_seats']);

$prices = ['Gold' => (float)$show['price_gold'], 'Platinum' => (float)$show['price_platinum'], 'Box' => (float)$show['price_box']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = clean($_POST['payment_method'] ?? '');
    $contact_email  = clean($_POST['contact_email'] ?? '');
    $contact_phone  = clean($_POST['contact_phone'] ?? '');
    $coupon_code    = clean($_POST['coupon_code'] ?? '');
    $phone_digits   = preg_replace('/\D/', '', $contact_phone);

    if ($seat_map_mode) {
        // ---- Seat-map booking: individual seats picked on the grid ----
        $selected_raw = json_decode($_POST['seats_json'] ?? '[]', true);
        $selected = [];
        if (is_array($selected_raw)) {
            foreach ($selected_raw as $row) {
                $seat_id = (int)($row['seat_id'] ?? 0);
                $type    = ($row['ticket_type'] ?? 'Adult') === 'Kid' ? 'Kid' : 'Adult';
                if ($seat_id > 0) $selected[$seat_id] = $type;
            }
        }

        $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');

        if (empty($selected)) {
            $error = "Please select at least one seat.";
        } elseif (!in_array($payment_method, $payment_methods, true)) {
            $error = "Please select a payment method.";
        } elseif ($payment_method === 'Credit/Debit Card' && strlen($card_number) < 12) {
            $error = "Please enter a valid card number.";
        } elseif (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email for your booking confirmation.";
        } elseif (strlen($phone_digits) < 10) {
            $error = "Please enter a valid phone number for your booking.";
        } else {
            // Load the chosen seats fresh from the DB (never trust class/price from the form)
            $seat_ids = array_keys($selected);
            $placeholders = implode(',', array_fill(0, count($seat_ids), '?'));
            $types = str_repeat('i', count($seat_ids));
            $seat_stmt = $conn->prepare("SELECT * FROM seats WHERE seat_id IN ($placeholders)");
            $seat_stmt->bind_param($types, ...$seat_ids);
            $seat_stmt->execute();
            $seat_rows = [];
            $sres = $seat_stmt->get_result();
            while ($sr = $sres->fetch_assoc()) $seat_rows[$sr['seat_id']] = $sr;
            $seat_stmt->close();

            if (count($seat_rows) !== count($seat_ids)) {
                $error = "One or more selected seats could not be found. Please reselect your seats.";
            } else {
                // Re-check live availability right before booking (prevents double-booking
                // if someone else grabbed one of these seats in the meantime).
                $taken = getBookedSeatIds($conn, $show_id);
                $conflict = array_intersect($seat_ids, $taken);
                if (!empty($conflict)) {
                    $error = "Sorry, seat(s) " . implode(', ', array_map(fn($id) => clean($seat_rows[$id]['seat_code']), $conflict)) . " were just booked by someone else. Please pick different seats.";
                } else {
                    $adult = 0; $kid = 0; $subtotal = 0;
                    $seat_breakdown = [];
                    foreach ($selected as $seat_id => $type) {
                        $class = $seat_rows[$seat_id]['seat_class'];
                        $price = $prices[$class] ?? 0;
                        if ($type === 'Kid') { $price = $price * 0.5; $kid++; } else { $adult++; }
                        $subtotal += $price;
                        $seat_breakdown[] = ['seat_id' => $seat_id, 'seat_code' => $seat_rows[$seat_id]['seat_code'], 'seat_class' => $class, 'ticket_type' => $type, 'price' => $price];
                    }

                    $discount = 0;
                    $applied_coupon = null;
                    if ($coupon_code !== '') {
                        $coupon = findActiveCoupon($conn, $coupon_code);
                        if ($coupon) {
                            $discount = round($subtotal * ($coupon['discount_percent'] / 100), 2);
                            $applied_coupon = $coupon['code'];
                        } else {
                            $error = "That coupon code is invalid or has expired.";
                        }
                    }

                    if (!$error) {
                        $total = $subtotal - $discount;
                        $class_summary = count(array_unique(array_column($seat_breakdown, 'seat_class'))) === 1
                            ? $seat_breakdown[0]['seat_class']
                            : 'Mixed';

                        $is_card         = ($payment_method === 'Credit/Debit Card');
                        $is_stripe_card  = ($is_card && $stripe_configured);
                        $payment_status  = $is_stripe_card ? 'Pending' : 'Paid';

                        $conn->begin_transaction();
                        try {
                            $ins = $conn->prepare("INSERT INTO bookings (user_id, show_id, seat_class, adult_seats, kid_seats, total_amount, discount_amount, coupon_code, contact_email, contact_phone, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $ins->bind_param("iisiiddsssss", $_SESSION['user_id'], $show_id, $class_summary, $adult, $kid, $total, $discount, $applied_coupon, $contact_email, $contact_phone, $payment_method, $payment_status);
                            $ins->execute();
                            $booking_id = $ins->insert_id;
                            $ins->close();

                            $bs_ins = $conn->prepare("INSERT INTO booking_seats (booking_id, show_id, seat_id, seat_code, seat_class, ticket_type, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            foreach ($seat_breakdown as $sb) {
                                $bs_ins->bind_param("iiissds", $booking_id, $show_id, $sb['seat_id'], $sb['seat_code'], $sb['seat_class'], $sb['ticket_type'], $sb['price']);
                                $bs_ins->execute();
                            }
                            $bs_ins->close();

                            $conn->commit();
                        } catch (\Throwable $e) {
                            $conn->rollback();
                            error_log('Booking failed (show_id=' . $show_id . '): ' . $e->getMessage());
                            $error = "Something went wrong while confirming your seats: " . $e->getMessage();
                        }

                        if (!$error && $is_stripe_card) {
                            // Real payment: send the customer to Stripe's hosted Checkout
                            // page. Booking already exists (seats reserved) with
                            // payment_status = 'Pending' - stripe_return.php confirms
                            // the payment server-side and only then marks it Paid.
                            require_once __DIR__ . '/includes/stripe_helper.php';
                            $base = appBaseUrl();
                            $checkout = stripe_create_checkout_session(
                                $total,
                                $show['title'] . ' - ' . $class_summary . ' (' . count($seat_breakdown) . ' seat(s))',
                                $booking_id,
                                $base . '/stripe_return.php?booking_id=' . $booking_id,
                                $base . '/stripe_return.php?booking_id=' . $booking_id . '&cancelled=1'
                            );
                            if (isset($checkout['url'])) {
                                header('Location: ' . $checkout['url']);
                                exit;
                            } else {
                                // Stripe call failed (e.g. keys not set up yet) - free the seats
                                $cancel = $conn->prepare("UPDATE bookings SET status = 'Cancelled', payment_status = 'Failed' WHERE booking_id = ?");
                                $cancel->bind_param("i", $booking_id);
                                $cancel->execute();
                                $error = "Card payment couldn't be started: " . clean($checkout['error']['message'] ?? 'Unknown error') . ". Please choose another payment method.";
                            }
                        } elseif (!$error) {
                            require_once __DIR__ . '/Email/BookingMailer.php';
                            send_booking_confirmation_email($conn, [
                                'booking_id'     => $booking_id,
                                'customer_name'  => $account['full_name'] ?? '',
                                'customer_email' => $contact_email,
                                'movie_title'    => $show['title'],
                                'theater_name'   => $show['theater_name'],
                                'location'       => $show['location'],
                                'show_date'      => date('D, d M Y', strtotime($show['show_date'])),
                                'show_time'      => date('h:i A', strtotime($show['show_time'])),
                                'seat_class'     => $class_summary . ' (' . implode(', ', array_column($seat_breakdown, 'seat_code')) . ')',
                                'adult_seats'    => $adult,
                                'kid_seats'      => $kid,
                                'total_amount'   => number_format($total, 2),
                            ]);

                            redirect('booking_confirm.php?id=' . $booking_id);
                        }
                    }
                }
            }
        }
    } else {
        // ---- Legacy booking: seat class + quantity ----
        $class = $_POST['seat_class'];
        $adult = max(0, (int)$_POST['adult_seats']);
        $kid   = max(0, (int)$_POST['kid_seats']);
        $seats_requested = $adult + $kid;

        // Re-check live availability at submit time too, so two people racing
        // to book the last seats can't both succeed (prevents double-booking).
        $available_seats = getAvailableSeats($conn, $show_id, $show['total_seats']);

        if (!isset($prices[$class])) {
            $error = "Please select a valid seat class.";
        } elseif ($seats_requested <= 0) {
            $error = "Please select at least one seat.";
        } elseif ($seats_requested > $available_seats) {
            $error = "Sorry, only {$available_seats} seat(s) are left for this show. Please reduce your seat count.";
        } elseif (!in_array($payment_method, $payment_methods, true)) {
            $error = "Please select a payment method.";
        } elseif ($payment_method === 'Credit/Debit Card' && strlen(preg_replace('/\D/', '', $_POST['card_number'] ?? '')) < 12) {
            $error = "Please enter a valid card number.";
        } elseif (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email for your booking confirmation.";
        } elseif (strlen($phone_digits) < 10) {
            $error = "Please enter a valid phone number for your booking.";
        } else {
            $price_per_seat = $prices[$class];
            // Kids (3-12 yrs) get a 50% concession on the ticket price
            $subtotal = ($adult * $price_per_seat) + ($kid * $price_per_seat * 0.5);

            $discount = 0;
            $applied_coupon = null;
            if ($coupon_code !== '') {
                $coupon = findActiveCoupon($conn, $coupon_code);
                if ($coupon) {
                    $discount = round($subtotal * ($coupon['discount_percent'] / 100), 2);
                    $applied_coupon = $coupon['code'];
                } else {
                    $error = "That coupon code is invalid or has expired.";
                }
            }

            if (!$error) {
                $total = $subtotal - $discount;

                $is_card        = ($payment_method === 'Credit/Debit Card');
                $is_stripe_card = ($is_card && $stripe_configured);
                $payment_status = $is_stripe_card ? 'Pending' : 'Paid';

                $ins = $conn->prepare("INSERT INTO bookings (user_id, show_id, seat_class, adult_seats, kid_seats, total_amount, discount_amount, coupon_code, contact_email, contact_phone, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("iisiiddsssss", $_SESSION['user_id'], $show_id, $class, $adult, $kid, $total, $discount, $applied_coupon, $contact_email, $contact_phone, $payment_method, $payment_status);
                $ins->execute();
                $booking_id = $ins->insert_id;
                $ins->close();

                if ($is_stripe_card) {
                    require_once __DIR__ . '/includes/stripe_helper.php';
                    $base = appBaseUrl();
                    $checkout = stripe_create_checkout_session(
                        $total,
                        $show['title'] . ' - ' . $class . ' (' . $seats_requested . ' seat(s))',
                        $booking_id,
                        $base . '/stripe_return.php?booking_id=' . $booking_id,
                        $base . '/stripe_return.php?booking_id=' . $booking_id . '&cancelled=1'
                    );
                    if (isset($checkout['url'])) {
                        header('Location: ' . $checkout['url']);
                        exit;
                    } else {
                        $cancel = $conn->prepare("UPDATE bookings SET status = 'Cancelled', payment_status = 'Failed' WHERE booking_id = ?");
                        $cancel->bind_param("i", $booking_id);
                        $cancel->execute();
                        $error = "Card payment couldn't be started: " . clean($checkout['error']['message'] ?? 'Unknown error') . ". Please choose another payment method.";
                    }
                } else {
                    // Send the booking confirmation email to the address the customer entered
                    require_once __DIR__ . '/Email/BookingMailer.php';
                    send_booking_confirmation_email($conn, [
                        'booking_id'     => $booking_id,
                        'customer_name'  => $account['full_name'] ?? '',
                        'customer_email' => $contact_email,
                        'movie_title'    => $show['title'],
                        'theater_name'   => $show['theater_name'],
                        'location'       => $show['location'],
                        'show_date'      => date('D, d M Y', strtotime($show['show_date'])),
                        'show_time'      => date('h:i A', strtotime($show['show_time'])),
                        'seat_class'     => $class,
                        'adult_seats'    => $adult,
                        'kid_seats'      => $kid,
                        'total_amount'   => number_format($total, 2),
                    ]);

                    redirect('booking_confirm.php?id=' . $booking_id);
                }
            }
        }
    }
}

// Re-fetch fresh seat map data for rendering (in case a POST attempt above failed)
if ($seat_map_mode) {
    $all_seats = getScreenSeats($conn, $show['screen_id']);
    $booked_seat_ids = getBookedSeatIds($conn, $show_id);
    $seat_rows_grouped = groupSeatsByRow($all_seats);
}

$maps_link = ($show['latitude'] && $show['longitude'])
    ? "https://www.google.com/maps/search/?api=1&query=" . $show['latitude'] . "," . $show['longitude']
    : "https://www.google.com/maps/search/?api=1&query=" . urlencode($show['theater_name'] . ', ' . $show['location']);
$map_query = ($show['latitude'] && $show['longitude'])
    ? $show['latitude'] . ',' . $show['longitude']
    : ($show['theater_name'] . ', ' . $show['location']);
$embed_src = "https://www.google.com/maps?q=" . urlencode($map_query) . "&hl=en&z=15&output=embed";

$GLOBALS['page_genre'] = $show['genre'];
include __DIR__ . '/includes/header.php';
?> <h1>Book Tickets</h1>
<div class="form-box" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;"> <div> <h2 style="margin:0 0 4px;font-size:22px;color:var(--text-primary);"><?php echo clean($show['title']); ?></h2> <div style="font-size:13.5px;color:var(--text-muted);"> <?php echo clean($show['theater_name']); ?>, <?php echo clean($show['location']); ?> &bull; <a href="<?php echo clean($maps_link); ?>" target="_blank" rel="noopener">Google Maps</a> </div> </div> <div style="text-align:right;"> <div style="font-family:var(--font-tech);font-weight:700;color:var(--neon-cyan);font-size:14px;"> <?php echo date('D, d M Y', strtotime($show['show_date'])); ?> </div> <div style="font-weight:800;color:var(--neon-amber);font-size:16px;"> <?php echo date('h:i A', strtotime($show['show_time'])); ?> </div> </div>
</div> <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <?php if ($seat_map_mode): ?> <div class="cinema-screen-wrap"> <div class="cinema-screen-curve"></div> <div class="cinema-screen-beam"></div> <div class="cinema-screen-lbl"> SCREEN </div>
</div> <form method="POST" class="form-box" style="margin-left:0;max-width:100%;" id="seatBookingForm"> <div class="seat-map-wrap"> <button type="button" class="row-arrow row-arrow-left seat-map-arrow" id="seatMapArrowLeft" aria-label="Slide seats left">Prev</button> <div class="seat-map-grid" id="seatMapGrid"> <?php foreach ($seat_rows_grouped as $row_letter => $row_seats): ?> <div class="seat-row-wrap"> <span class="seat-row-lbl"><?php echo clean($row_letter); ?></span> <div class="seat-row-items"> <?php foreach ($row_seats as $seat):
                $is_booked = in_array((int)$seat['seat_id'], $booked_seat_ids, true);
                $tier_class = 'tier-' . strtolower($seat['seat_class']);
            ?> <button type="button"
                        class="seat-btn <?php echo $tier_class; ?><?php echo $is_booked ? ' is-booked' : ''; ?>"
                        data-seat-id="<?php echo $seat['seat_id']; ?>"
                        data-seat-code="<?php echo clean($seat['seat_code']); ?>"
                        data-seat-class="<?php echo clean($seat['seat_class']); ?>"
                        data-price="<?php echo $prices[$seat['seat_class']] ?? 0; ?>" <?php echo $is_booked ? 'disabled' : ''; ?> title="<?php echo clean($seat['seat_code']); ?> - <?php echo clean($seat['seat_class']); ?> - Rs. <?php echo number_format($prices[$seat['seat_class']] ?? 0, 2); ?>"> <?php echo (int)$seat['seat_number']; ?> </button> <?php endforeach; ?> </div> </div> <?php endforeach; ?> </div> <button type="button" class="row-arrow row-arrow-right seat-map-arrow" id="seatMapArrowRight" aria-label="Slide seats right">Next</button> </div> <div style="display:flex;justify-content:center;gap:18px;margin-bottom:28px;flex-wrap:wrap;font-size:12.5px;font-family:var(--font-tech);color:var(--text-muted);"> <span style="display:flex;align-items:center;gap:6px;"><span class="seat-btn tier-gold" style="width:16px;height:16px;display:inline-block;cursor:default;"></span> Gold (Rs. <?php echo number_format($prices['Gold'], 0); ?>)</span> <span style="display:flex;align-items:center;gap:6px;"><span class="seat-btn tier-platinum" style="width:16px;height:16px;display:inline-block;cursor:default;"></span> Platinum (Rs. <?php echo number_format($prices['Platinum'], 0); ?>)</span> <span style="display:flex;align-items:center;gap:6px;"><span class="seat-btn tier-box" style="width:16px;height:16px;display:inline-block;cursor:default;"></span> VIP Box (Rs. <?php echo number_format($prices['Box'], 0); ?>)</span> <span style="display:flex;align-items:center;gap:6px;"><span class="seat-btn is-selected" style="width:16px;height:16px;display:inline-block;cursor:default;"></span> Selected</span> <span style="display:flex;align-items:center;gap:6px;"><span class="seat-btn is-booked" style="width:16px;height:16px;display:inline-block;cursor:default;"></span> Booked</span> </div> <div id="selectedSeatsList" class="selected-seats-list"></div> <label>Coupon Code (optional)</label> <input type="text" name="coupon_code" id="couponCodeInput" placeholder="e.g. WELCOME10" value="<?php echo clean($_POST['coupon_code'] ?? ''); ?>"> <label>Send Booking Confirmation To (Email)</label> <input type="email" name="contact_email" id="contactEmailInput" value="<?php echo clean($account['email'] ?? ''); ?>" required> <label>Phone Number</label> <input type="tel" name="contact_phone" id="contactPhoneInput" placeholder="03xx-xxxxxxx" pattern="[0-9+\-\s()]{10,}" title="Enter a valid phone number (at least 10 digits)" value="<?php echo clean($account['phone'] ?? ''); ?>" required> <p id="liveTotalHint" class="payment-contact-hint">Estimated total: Rs. 0.00</p> <input type="hidden" name="seats_json" id="seatsJsonInput" value="[]"> <button type="button" id="proceedCheckoutBtn" disabled>Select Seats to Continue</button> <div id="checkoutStep" style="display:none;"> <label>Payment Method</label> <select name="payment_method" id="paymentMethod" required> <?php foreach ($payment_methods as $pm):
            ?> <option value="<?php echo clean($pm); ?>" data-contact="<?php echo clean($payment_contacts[$pm] ?? ''); ?>"><?php echo clean($pm); ?></option> <?php endforeach; ?> </select> <p id="paymentContactHint" class="payment-contact-hint" style="display:none;"></p> <div id="cardFieldsWrap" style="display:none;"> <label for="cardNumberInput">Card Number</label> <input type="text" id="cardNumberInput" name="card_number" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="1234 5678 9012 3456"> <label for="cardLimitInput">Card Limit (optional)</label> <input type="text" id="cardLimitInput" name="card_limit" inputmode="numeric" placeholder="e.g. Rs. 200,000"> </div> <button type="submit" id="confirmBookingBtn">Confirm Booking</button> </div>
</form> <script>
(function () {
    var pmSelect = document.getElementById('paymentMethod');
    var cardWrap = document.getElementById('cardFieldsWrap');
    var cardInput = document.getElementById('cardNumberInput');

    if (pmSelect.options[pmSelect.selectedIndex] && pmSelect.options[pmSelect.selectedIndex].disabled) {
        for (var i = 0; i < pmSelect.options.length; i++) {
            if (!pmSelect.options[i].disabled) { pmSelect.selectedIndex = i; break; }
        }
    }

    function toggleCardField() {
        var isCard = pmSelect.value === 'Credit/Debit Card';
        cardWrap.style.display = isCard ? 'block' : 'none';
        if (cardInput) {
            cardInput.required = isCard;
            if (!isCard) cardInput.value = '';
        }
    }
    pmSelect.addEventListener('change', toggleCardField);
    toggleCardField();

    if (cardInput) {
        cardInput.addEventListener('input', function () {
            var digits = cardInput.value.replace(/\D/g, '').slice(0, 16);
            cardInput.value = digits.replace(/(.{4})/g, '$1 ').trim();
        });
    }
})();
</script>
<script>
(function () {
    var selected = {}; // seat_id -> { code, cls, price, ticket_type }
    var seatButtons = document.querySelectorAll('.seat-btn:not(.is-booked)');
    var listEl = document.getElementById('selectedSeatsList');
    var totalHint = document.getElementById('liveTotalHint');
    var jsonInput = document.getElementById('seatsJsonInput');
    var proceedBtn = document.getElementById('proceedCheckoutBtn');
    var checkoutStep = document.getElementById('checkoutStep');
    var emailInput = document.getElementById('contactEmailInput');
    var phoneInput = document.getElementById('contactPhoneInput');
    var confirmBtn = document.getElementById('confirmBookingBtn');

    // Arrow-based sliding for the seat map instead of a visible scrollbar,
    // so a big screen with lots of seats stays slide-able like the movie rows.
    var seatMapGrid = document.getElementById('seatMapGrid');
    var seatMapLeft = document.getElementById('seatMapArrowLeft');
    var seatMapRight = document.getElementById('seatMapArrowRight');
    if (seatMapGrid && seatMapLeft && seatMapRight) {
        function updateSeatMapArrows() {
            var overflowing = seatMapGrid.scrollWidth > seatMapGrid.clientWidth + 2;
            seatMapLeft.classList.toggle('is-hidden', !overflowing || seatMapGrid.scrollLeft <= 4);
            seatMapRight.classList.toggle('is-hidden', !overflowing || seatMapGrid.scrollLeft + seatMapGrid.clientWidth >= seatMapGrid.scrollWidth - 4);
        }
        seatMapLeft.addEventListener('click', function () { seatMapGrid.scrollBy({ left: -220, behavior: 'smooth' }); });
        seatMapRight.addEventListener('click', function () { seatMapGrid.scrollBy({ left: 220, behavior: 'smooth' }); });
        seatMapGrid.addEventListener('scroll', updateSeatMapArrows);
        window.addEventListener('resize', updateSeatMapArrows);
        updateSeatMapArrows();
    }

    function renderList() {
        var ids = Object.keys(selected);
        if (ids.length === 0) {
            listEl.innerHTML = '<p style="color:#888;font-size:13px;">No seats selected yet - click seats above to select them.</p>';
        } else {
            var html = '<table style="width:100%;"><tr><th>Seat</th><th>Class</th><th>Type</th><th>Price</th></tr>';
            var total = 0;
            ids.forEach(function (id) {
                var s = selected[id];
                var price = s.ticket_type === 'Kid' ? s.price * 0.5 : s.price;
                total += price;
                html += '<tr><td>' + s.code + '</td><td>' + s.cls + '</td><td>' +
                    '<select data-seat-id="' + id + '" class="ticket-type-select">' +
                        '<option value="Adult"' + (s.ticket_type === 'Adult' ? ' selected' : '') + '>Adult</option>' +
                        '<option value="Kid"' + (s.ticket_type === 'Kid' ? ' selected' : '') + '>Kid (3-12, 50% off)</option>' +
                    '</select></td><td>Rs. ' + price.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
            });
            html += '</table>';
            listEl.innerHTML = html;
            totalHint.textContent = 'Estimated total: Rs. ' + total.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (before any coupon discount)';

            listEl.querySelectorAll('.ticket-type-select').forEach(function (sel) {
                sel.addEventListener('change', function () {
                    selected[sel.getAttribute('data-seat-id')].ticket_type = sel.value;
                    renderList();
                });
            });
        }
        jsonInput.value = JSON.stringify(ids.map(function (id) {
            return { seat_id: parseInt(id, 10), ticket_type: selected[id].ticket_type };
        }));
        proceedBtn.disabled = ids.length === 0;
        proceedBtn.textContent = ids.length === 0 ? 'Select Seats to Continue' : 'Proceed to Checkout (' + ids.length + ' seat' + (ids.length > 1 ? 's' : '') + ')';
        // Editing seats after checkout was opened re-locks it until "Proceed" is pressed again.
        if (checkoutStep.style.display !== 'none') {
            checkoutStep.style.display = 'none';
            proceedBtn.style.display = 'inline-block';
        }
    }

    seatButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-seat-id');
            if (selected[id]) {
                delete selected[id];
                btn.classList.remove('is-selected');
            } else {
                selected[id] = {
                    code: btn.getAttribute('data-seat-code'),
                    cls: btn.getAttribute('data-seat-class'),
                    price: parseFloat(btn.getAttribute('data-price')) || 0,
                    ticket_type: 'Adult'
                };
                btn.classList.add('is-selected');
            }
            renderList();
        });
    });

    renderList();

    proceedBtn.addEventListener('click', function () {
        if (Object.keys(selected).length === 0) return;
        if (!emailInput.checkValidity()) { emailInput.reportValidity(); return; }
        if (!phoneInput.checkValidity()) { phoneInput.reportValidity(); return; }
        proceedBtn.style.display = 'none';
        checkoutStep.style.display = 'block';
        checkoutStep.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    var pmSelect = document.getElementById('paymentMethod');
    var pmHint = document.getElementById('paymentContactHint');
    function updatePmHint() {
        var contact = pmSelect.options[pmSelect.selectedIndex].getAttribute('data-contact');
        if (contact) { pmHint.style.display = 'block'; pmHint.textContent = 'Send payment to (' + pmSelect.value + '): ' + contact; }
        else { pmHint.style.display = 'none'; }
    }
    pmSelect.addEventListener('change', updatePmHint);
    updatePmHint();
})();
</script> <?php else: ?> <p class="seats-left-hint <?php echo $available_seats <= 10 ? 'seats-low' : ''; ?>"> <?php echo $available_seats > 0 ? " {$available_seats} seat(s) left for this show" : " This show is fully booked."; ?>
</p> <form method="POST" class="form-box" style="margin-left:0;" data-genre="<?php echo clean($show['genre']); ?>"> <label>Seat Class</label> <select name="seat_class" id="seatClass" required
            data-price-gold="<?php echo $show['price_gold']; ?>"
            data-price-platinum="<?php echo $show['price_platinum']; ?>"
            data-price-box="<?php echo $show['price_box']; ?>"> <option value="Gold">Gold - Rs. <?php echo number_format($show['price_gold'],2); ?></option> <option value="Platinum">Platinum - Rs. <?php echo number_format($show['price_platinum'],2); ?></option> <option value="Box">Box - Rs. <?php echo number_format($show['price_box'],2); ?></option> </select> <label>Adult Seats</label> <input type="number" name="adult_seats" id="adultSeats" min="0" max="<?php echo $available_seats; ?>" value="<?php echo $available_seats > 0 ? 1 : 0; ?>" required> <label>Kid Seats (age 3-12, 50% concession)</label> <input type="number" name="kid_seats" id="kidSeats" min="0" max="<?php echo $available_seats; ?>" value="0"> <label>Coupon Code (optional)</label> <input type="text" name="coupon_code" placeholder="e.g. WELCOME10" value="<?php echo clean($_POST['coupon_code'] ?? ''); ?>"> <label>Send Booking Confirmation To (Email)</label> <input type="email" name="contact_email" id="contactEmailInput2" value="<?php echo clean($account['email'] ?? ''); ?>" required> <label>Phone Number</label> <input type="tel" name="contact_phone" id="contactPhoneInput2" placeholder="03xx-xxxxxxx" pattern="[0-9+\-\s()]{10,}" title="Enter a valid phone number (at least 10 digits)" value="<?php echo clean($account['phone'] ?? ''); ?>" required> <p id="liveTotalHint" class="payment-contact-hint" style="display:block;">Estimated total: Rs. 0.00</p> <button type="button" id="proceedCheckoutBtn2" <?php echo $available_seats <= 0 ? 'disabled' : ''; ?>> <?php echo $available_seats <= 0 ? 'Sold Out' : 'Proceed to Checkout'; ?> </button> <div id="checkoutStep2" style="display:none;"> <label>Payment Method</label> <select name="payment_method" id="paymentMethod" required> <?php foreach ($payment_methods as $pm):
            ?> <option value="<?php echo clean($pm); ?>" data-contact="<?php echo clean($payment_contacts[$pm] ?? ''); ?>"><?php echo clean($pm); ?></option> <?php endforeach; ?> </select> <p id="paymentContactHint" class="payment-contact-hint" style="display:none;"></p> <script> (function () {
            var select = document.getElementById('paymentMethod');
            var hint = document.getElementById('paymentContactHint');
            if (select.options[select.selectedIndex] && select.options[select.selectedIndex].disabled) {
                for (var i = 0; i < select.options.length; i++) {
                    if (!select.options[i].disabled) { select.selectedIndex = i; break; }
                }
            }
            function updateHint() {
                var contact = select.options[select.selectedIndex].getAttribute('data-contact');
                if (contact) {
                    hint.style.display = 'block';
                    hint.textContent = 'Send payment to (' + select.value + '): ' + contact;
                } else {
                    hint.style.display = 'none';
                }
            }
            select.addEventListener('change', updateHint);
            updateHint();
        })(); </script> <div id="cardFieldsWrap" style="display:none;"> <label for="cardNumberInput">Card Number</label> <input type="text" id="cardNumberInput" name="card_number" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="1234 5678 9012 3456"> <label for="cardLimitInput">Card Limit (optional)</label> <input type="text" id="cardLimitInput" name="card_limit" inputmode="numeric" placeholder="e.g. Rs. 200,000"> </div> <script> (function () {
            var pmSelect = document.getElementById('paymentMethod');
            var cardWrap = document.getElementById('cardFieldsWrap');
            var cardInput = document.getElementById('cardNumberInput');

            function toggleCardField() {
                var isCard = pmSelect.value === 'Credit/Debit Card';
                cardWrap.style.display = isCard ? 'block' : 'none';
                if (cardInput) {
                    cardInput.required = isCard;
                    if (!isCard) cardInput.value = '';
                }
            }
            pmSelect.addEventListener('change', toggleCardField);
            toggleCardField();

            if (cardInput) {
                cardInput.addEventListener('input', function () {
                    var digits = cardInput.value.replace(/\D/g, '').slice(0, 16);
                    cardInput.value = digits.replace(/(.{4})/g, '$1 ').trim();
                });
            }
        })(); </script> <button type="submit" id="confirmBookingBtn">Confirm Booking</button> </div> <script>
        (function () {
            var proceedBtn = document.getElementById('proceedCheckoutBtn2');
            var checkoutStep = document.getElementById('checkoutStep2');
            var emailInput = document.getElementById('contactEmailInput2');
            var phoneInput = document.getElementById('contactPhoneInput2');
            if (!proceedBtn) return;
            proceedBtn.addEventListener('click', function () {
                if (!emailInput.checkValidity()) { emailInput.reportValidity(); return; }
                if (!phoneInput.checkValidity()) { phoneInput.reportValidity(); return; }
                proceedBtn.style.display = 'none';
                checkoutStep.style.display = 'block';
                checkoutStep.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        })();
    </script>
</form> <script> (function () {
        var seatClass = document.getElementById('seatClass');
        var adultSeats = document.getElementById('adultSeats');
        var kidSeats = document.getElementById('kidSeats');
        var totalHint = document.getElementById('liveTotalHint');
        function updateTotal() {
            var opt = seatClass.options[seatClass.selectedIndex];
            var price = parseFloat(seatClass.getAttribute('data-price-' + opt.value.toLowerCase())) || 0;
            var adults = Math.max(0, parseInt(adultSeats.value, 10) || 0);
            var kids = Math.max(0, parseInt(kidSeats.value, 10) || 0);
            var total = (adults * price) + (kids * price * 0.5);
            totalHint.textContent = 'Estimated total: Rs. ' + total.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (before any coupon discount)';
        }
        [seatClass, adultSeats, kidSeats].forEach(function (el) { el.addEventListener('input', updateTotal); el.addEventListener('change', updateTotal); });
        updateTotal();
    })();
</script> <?php endif; ?> <script> // Popcorn celebration burst: plays for 4 seconds from the bottom-centre
    // of the screen right when "Confirm Booking" is pressed, then the form
    // actually submits (so the booking still goes through as normal).
    (function () {
        var form = document.getElementById('confirmBookingBtn') ? document.getElementById('confirmBookingBtn').closest('form') : null;
        var submitBtn = document.getElementById('confirmBookingBtn');
        if (!form || !submitBtn) return;

        form.addEventListener('submit', function (e) {
            if (submitBtn.disabled) return;
            e.preventDefault();
            launchPopcornBurst();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Confirming...';
            setTimeout(function () { form.submit(); }, 4000);
        });

        function launchPopcornBurst() {
            var overlay = document.createElement('div');
            overlay.className = 'popcorn-overlay';
            document.body.appendChild(overlay);

            var caption = document.createElement('div');
            caption.className = 'popcorn-caption';
            caption.textContent = ' Confirming your booking...';
            overlay.appendChild(caption);

            var pieceCount = 18;
            for (var i = 0; i < pieceCount; i++) {
                var piece = document.createElement('span');
                piece.className = 'popcorn-piece';
                piece.textContent = '';
                var dx = Math.round(Math.random() * 260 - 130) + 'px';
                var peak = Math.round(-(140 + Math.random() * 160)) + 'px';
                var rot = Math.round(Math.random() * 360 - 180) + 'deg';
                var duration = (1.5 + Math.random() * 1.3).toFixed(2) + 's';
                var delay = (Math.random() * 1.0).toFixed(2) + 's';
                piece.style.setProperty('--dx', dx);
                piece.style.setProperty('--peak', peak);
                piece.style.setProperty('--rot', rot);
                piece.style.animationDuration = duration;
                piece.style.animationDelay = delay;
                overlay.appendChild(piece);
            }

            setTimeout(function () { overlay.remove(); }, 4000);
        }
    })();
</script> <?php include __DIR__ . '/includes/footer.php'; ?>
