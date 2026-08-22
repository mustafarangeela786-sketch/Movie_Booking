<?php
/* ==========================================================
   admin/screen_seats.php - Fine-tune individual seat classes
   on one screen (e.g. move a couple of seats from Platinum to
   Box). Click a seat to cycle its class, then Save.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$screen_id = isset($_GET['screen_id']) ? (int)$_GET['screen_id'] : 0;

$stmt = $conn->prepare("SELECT sc.*, t.theater_name FROM screens sc JOIN theaters t ON sc.theater_id = t.theater_id WHERE sc.screen_id = ?");
$stmt->bind_param("i", $screen_id);
$stmt->execute();
$screen = $stmt->get_result()->fetch_assoc();
if (!$screen) {
    redirect('screens.php');
}

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat_class'])) {
    $upd = $conn->prepare("UPDATE seats SET seat_class = ? WHERE seat_id = ? AND screen_id = ?");
    foreach ($_POST['seat_class'] as $seat_id => $class) {
        if (!in_array($class, ['Gold', 'Platinum', 'Box'], true)) continue;
        $seat_id = (int)$seat_id;
        $upd->bind_param("sii", $class, $seat_id, $screen_id);
        $upd->execute();
    }
    $upd->close();
    $saved = true;
}

$seats = getScreenSeats($conn, $screen_id);
$rows = groupSeatsByRow($seats);

include __DIR__ . '/admin_header.php';
?> <h1>Edit Seats - <?php echo clean($screen['screen_name']); ?> (<?php echo clean($screen['theater_name']); ?>)</h1>
<p style="color:#a0a0a0;">Click a seat to cycle Gold &rarr; Platinum &rarr; Box &rarr; Gold, then Save.</p>
<p><a href="screens.php">&larr; Back to Screens</a></p> <?php if ($saved): ?><div class="alert alert-success">Seat classes updated.</div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:100%;"> <div class="admin-seat-map"> <div class="admin-seat-screen-label">SCREEN THIS WAY</div> <?php foreach ($rows as $row_letter => $row_seats): ?> <div class="admin-seat-row"> <span class="admin-seat-row-label"><?php echo clean($row_letter); ?></span> <?php foreach ($row_seats as $seat): ?> <button type="button"
                        class="admin-seat admin-seat-<?php echo strtolower($seat['seat_class']); ?>"
                        data-seat-id="<?php echo $seat['seat_id']; ?>"
                        title="<?php echo clean($seat['seat_code']); ?> - <?php echo clean($seat['seat_class']); ?>"> <?php echo (int)$seat['seat_number']; ?> </button> <input type="hidden" name="seat_class[<?php echo $seat['seat_id']; ?>]" id="cls-<?php echo $seat['seat_id']; ?>" value="<?php echo clean($seat['seat_class']); ?>"> <?php endforeach; ?> </div> <?php endforeach; ?> </div> <div class="admin-seat-legend"> <span><span class="legend-swatch admin-seat-gold"></span> Gold</span> <span><span class="legend-swatch admin-seat-platinum"></span> Platinum</span> <span><span class="legend-swatch admin-seat-box"></span> Box</span> </div> <button type="submit" style="margin-top:20px;">Save Seat Classes</button>
</form> <script>
(function () {
    var cycle = { gold: 'platinum', platinum: 'box', box: 'gold' };
    document.querySelectorAll('.admin-seat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var seatId = btn.getAttribute('data-seat-id');
            var current = ['gold', 'platinum', 'box'].find(function (c) { return btn.classList.contains('admin-seat-' + c); });
            var next = cycle[current] || 'gold';
            btn.classList.remove('admin-seat-' + current);
            btn.classList.add('admin-seat-' + next);
            btn.title = btn.title.split(' - ')[0] + ' - ' + next.charAt(0).toUpperCase() + next.slice(1);
            document.getElementById('cls-' + seatId).value = next.charAt(0).toUpperCase() + next.slice(1);
        });
    });
})();
</script> <?php include __DIR__ . '/admin_footer.php'; ?>
