<?php
/* ==========================================================
   admin/coupons.php - Create and manage discount coupon codes
   that customers can enter at booking time.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM coupons WHERE coupon_id = $id");
    redirect('coupons.php');
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE coupons SET active = 1 - active WHERE coupon_id = $id");
    redirect('coupons.php');
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code    = strtoupper(trim(clean($_POST['code'])));
    $percent = (int)$_POST['discount_percent'];
    $expires = $_POST['expires_on'] !== '' ? $_POST['expires_on'] : null;

    if ($code === '' || $percent < 1 || $percent > 100) {
        $error = "Please enter a valid code and a discount between 1-100%.";
    } else {
        $ins = $conn->prepare("INSERT INTO coupons (code, discount_percent, active, expires_on) VALUES (?, ?, 1, ?)
                                ON DUPLICATE KEY UPDATE discount_percent = VALUES(discount_percent), expires_on = VALUES(expires_on)");
        $ins->bind_param("sis", $code, $percent, $expires);
        $ins->execute();
        redirect('coupons.php');
    }
}

$coupons = $conn->query("SELECT * FROM coupons ORDER BY coupon_id DESC");

include __DIR__ . '/admin_header.php';
?> <h1>Discount Coupons</h1> <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <form method="POST" class="form-box" style="margin-left:0;max-width:420px;"> <label>Coupon Code</label> <input type="text" name="code" placeholder="e.g. SUMMER15" required> <label>Discount %</label> <input type="number" name="discount_percent" min="1" max="100" value="10" required> <label>Expires On</label> <input type="date" name="expires_on"> <button type="submit">Save Coupon</button>
</form> <div class="admin-table-scroll"> <table class="admin-table-nowrap" style="margin-top:24px;"> <tr><th>Code</th><th>Discount</th><th>Expires</th><th>Status</th><th>Actions</th></tr> <?php while ($c = $coupons->fetch_assoc()): ?> <tr> <td><?php echo clean($c['code']); ?></td> <td><?php echo (int)$c['discount_percent']; ?>%</td> <td><?php echo $c['expires_on'] ? date('d M Y', strtotime($c['expires_on'])) : 'Never'; ?></td> <td><?php echo $c['active'] ? 'Active' : 'Disabled'; ?></td> <td> <a href="?toggle=<?php echo $c['coupon_id']; ?>"><?php echo $c['active'] ? 'Disable' : 'Enable'; ?></a> &middot; <a href="?delete=<?php echo $c['coupon_id']; ?>" onclick="return confirm('Delete this coupon?');" style="color:#ff5c5c;">Delete</a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>