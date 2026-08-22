<?php
/* ==========================================================
   booking_confirm.php - Flip E-Ticket (ticket-stub design)
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, m.title, m.genre, m.poster, t.theater_name, t.location
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
    redirect('index.php');
}

$seat_codes = [];
if (seatMapSchemaExists($conn)) {
    $sc_stmt = $conn->prepare("SELECT seat_code, ticket_type FROM booking_seats WHERE booking_id = ? ORDER BY seat_code");
    $sc_stmt->bind_param("i", $booking_id);
    $sc_stmt->execute();
    $sc_res = $sc_stmt->get_result();
    while ($sc = $sc_res->fetch_assoc()) $seat_codes[] = $sc;
    $sc_stmt->close();
}

$GLOBALS['page_genre'] = $booking['genre'];
include __DIR__ . '/includes/header.php';

$pay_badges = [
    'Paid'    => ['label' => 'PAID',    'color' => 'var(--neon-green)', 'bg' => 'rgba(16, 185, 129, 0.14)'],
    'Pending' => ['label' => 'PENDING', 'color' => 'var(--neon-amber)', 'bg' => 'rgba(245, 158, 11, 0.14)'],
    'Failed'  => ['label' => 'FAILED',  'color' => 'var(--neon-red)',   'bg' => 'rgba(255, 51, 75, 0.14)'],
];
$pay_badge  = $pay_badges[$booking['payment_status']] ?? $pay_badges['Paid'];
$verify_url = ticketVerifyUrl($booking['booking_id']);
$seats_text = !empty($seat_codes)
    ? implode(', ', array_map(fn($s) => $s['seat_code'], $seat_codes))
    : ((int)$booking['adult_seats'] . 'A' . ((int)$booking['kid_seats'] > 0 ? ' + ' . (int)$booking['kid_seats'] . 'K' : ''));
?> <div class="alert alert-success" style="max-width:600px;margin:0 auto 24px;"> <strong>Booking Confirmed!</strong> Your electronic admission pass is ready below. Tap the card to flip it.
</div> <div class="eticket-flip-scene"> <div class="eticket-flip-card" id="ticketFlipCard"> <!-- ============ FRONT FACE ============ --> <div class="eticket-face eticket-face-front" id="ticketFaceFront"> <div class="eticket-stripe-edge"></div> <div class="eticket-front-pad"> <div class="eticket-front-top"> <div> <div class="eticket-eyebrow">Movie</div> <h2 class="eticket-movie-title"><?php echo clean($booking['title']); ?></h2> </div> <span class="eticket-flip-hint" title="Tap to flip"></span> </div> <div class="eticket-pay-badge" style="color:<?php echo $pay_badge['color']; ?>;background:<?php echo $pay_badge['bg']; ?>;border-color:<?php echo $pay_badge['color']; ?>;"> <span class="dot" style="background:<?php echo $pay_badge['color']; ?>;box-shadow:0 0 8px <?php echo $pay_badge['color']; ?>;"></span> <?php echo $pay_badge['label']; ?> &middot; <?php echo clean($booking['payment_method'] ?? 'Cash at Counter'); ?> </div> <div class="eticket-info-grid"> <div> <div class="eticket-eyebrow">Theater</div> <div class="eticket-info-value"><?php echo clean($booking['theater_name']); ?></div> </div> <div> <div class="eticket-eyebrow">Class</div> <div class="eticket-info-value accent"><?php echo clean($booking['seat_class']); ?></div> </div> <div> <div class="eticket-eyebrow">Seats</div> <div class="eticket-info-value accent"><?php echo clean($seats_text); ?></div> </div> </div> <div class="eticket-info-grid"> <div> <div class="eticket-eyebrow">Date</div> <div class="eticket-info-value"><?php echo date('d M Y', strtotime($booking['show_date'])); ?></div> </div> <div> <div class="eticket-eyebrow">Time</div> <div class="eticket-info-value"><?php echo date('h:i A', strtotime($booking['show_time'])); ?></div> </div> <div> <div class="eticket-eyebrow">Pass ID</div> <div class="eticket-info-value">#<?php echo (int)$booking['booking_id']; ?></div> </div> </div> <div class="eticket-barcode"><?php echo renderBarcodeSvg('bk-' . $booking['booking_id'], 280, 44, '#0f172a'); ?></div> <div class="eticket-serial">SERIAL NUMBER. <?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></div> </div> <div class="eticket-front-footer"> <div> <div class="eticket-eyebrow light">Name</div> <div class="eticket-footer-name"><?php echo clean($booking['contact_email'] ? explode('@', $booking['contact_email'])[0] : ($_SESSION['user_name'] ?? 'Guest')); ?></div> <div class="eticket-eyebrow light" style="margin-top:10px;">Price</div> <div class="eticket-footer-price">Rs. <?php echo number_format($booking['total_amount'], 0); ?></div> </div> <div class="eticket-qr-box"> <div id="ticketQrCode"></div> </div> </div> </div> <!-- ============ BACK FACE ============ --> <div class="eticket-face eticket-face-back" id="ticketFaceBack"> <div class="eticket-back-pattern"></div> <div class="eticket-back-content"> <div class="eticket-back-top"> <span class="eticket-back-tag"> VERIFIED E-TICKET</span> </div> <div class="eticket-back-center"> <div class="eticket-back-icon"> <img src="<?php echo $asset_base; ?>assets/img/ticket-logo.svg" alt=""> </div> <div class="eticket-back-brand">MOVIEBOOK</div> <div class="eticket-back-sub">CINEMAS</div> <p class="eticket-back-tagline">Your premier destination for blockbuster movies and instant seat reservations.</p> </div> <div class="eticket-back-bottom"> <div class="eticket-back-note">Scan the QR code on the front at any time to verify this pass is paid and valid.</div> <div class="eticket-back-site">www.moviebook.example</div> </div> </div> </div> </div>
</div> <div class="eticket-actions-standalone"> <button type="button" id="flipTicketBtn" class="btn" style="width:100%;background:rgba(255,255,255,0.08);color:#fff;border:1px solid var(--border-glass);"> Flip Ticket </button> <button type="button" id="downloadTicketBtn" class="btn" style="width:100%;"> Download Digital Pass (PNG) </button> <a class="btn" href="my_bookings.php" style="width:100%;background:rgba(255,255,255,0.08);color:#fff;border:1px solid var(--border-glass);">  View All My Bookings </a> <a href="index.php" style="text-align:center;font-size:13.5px;color:var(--text-muted);margin-top:4px;"> &larr; Back to Home </a>
</div> <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
new QRCode(document.getElementById('ticketQrCode'), {
    text: <?php echo json_encode($verify_url); ?>,
    width: 92,
    height: 92,
    colorDark: '#06070a',
    colorLight: '#ffffff'
});

var flipCard = document.getElementById('ticketFlipCard');
var flipBtn  = document.getElementById('flipTicketBtn');
var isFlipped = false;

function setFlipped(v) {
    isFlipped = v;
    flipCard.classList.toggle('flipped', isFlipped);
    flipBtn.textContent = isFlipped ? ' Flip to Front' : ' Flip Ticket';
}
flipCard.addEventListener('click', function () { setFlipped(!isFlipped); });
flipBtn.addEventListener('click', function (e) { e.stopPropagation(); setFlipped(!isFlipped); });

document.getElementById('downloadTicketBtn').addEventListener('click', function (e) {
    e.stopPropagation();
    var faceEl = isFlipped ? document.getElementById('ticketFaceBack') : document.getElementById('ticketFaceFront');
    var btn = this;
    var originalText = btn.textContent;
    btn.textContent = 'Generating Pass...';
    btn.disabled = true;

    // Neutralize the 3D flip transform just for the capture so html2canvas
    // (which doesn't handle rotateY/backface-visibility well) always
    // renders the currently visible face right-side-up and unmirrored.
    var prevCardTransform = flipCard.style.transform;
    var prevFaceTransform = faceEl.style.transform;
    flipCard.style.transition = 'none';
    flipCard.style.transform = 'none';
    faceEl.style.transform = 'none';

    html2canvas(faceEl, {
        backgroundColor: isFlipped ? '#7f0a12' : '#11141c',
        scale: 2,
        useCORS: true
    }).then(function (canvas) {
        flipCard.style.transform = prevCardTransform;
        faceEl.style.transform = prevFaceTransform;
        flipCard.style.transition = '';
        var link = document.createElement('a');
        link.download = 'MovieBook-Pass-<?php echo $booking['booking_id']; ?>' + (isFlipped ? '-back' : '') + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        btn.textContent = originalText;
        btn.disabled = false;
    }).catch(function () {
        flipCard.style.transform = prevCardTransform;
        faceEl.style.transform = prevFaceTransform;
        flipCard.style.transition = '';
        btn.textContent = originalText;
        btn.disabled = false;
        alert('Could not download pass directly. You can take a screenshot of this ticket.');
    });
});
</script> <?php include __DIR__ . '/includes/footer.php'; ?>
