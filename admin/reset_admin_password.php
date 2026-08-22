<?php
/* ==========================================================
   reset_admin_password.php
   ONE-TIME USE ONLY - lets you set your OWN admin username
   and password (instead of the shared default admin/admin123).

   How to use:
     1. Open this file in your browser:
        http://localhost/Movie%20Booking/admin/reset_admin_password.php
     2. Fill in a username and a strong password only you know,
        submit.
     3. DELETE this file straight after. Anyone who finds it
        while it still exists could set their own admin login,
        so it must not stay on the server.
   ========================================================== */
require_once __DIR__ . '/../config/db.php';

$done = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || strlen($password) < 8) {
        $error = "Please enter a username and a password of at least 8 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Replace whatever admin account(s) exist with this single one,
        // so the old default admin/admin123 login stops working.
        $conn->query("DELETE FROM admin");
        $ins = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $ins->bind_param("ss", $username, $hash);
        if ($ins->execute()) {
            $done = true;
        } else {
            $error = "Something went wrong: " . $conn->error;
        }
        $ins->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Set Admin Credentials</title>
<style> body { font-family: 'Algerian', -apple-system, Segoe UI, sans-serif; background:#0f1116; color:#eaeaea; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
    .box { background:#1a1c23; padding:32px; border-radius:12px; max-width:380px; width:100%; }
    label { display:block; margin:14px 0 6px; font-size:14px; }
    input { width:100%; padding:10px; border-radius:6px; border:1px solid #333; background:#0f1116; color:#eaeaea; box-sizing:border-box; }
    button { margin-top:20px; width:100%; padding:10px; border:none; border-radius:6px; background:#ffb400; color:#111; font-weight:bold; cursor:pointer; }
    .warn { color:#ff6b6b; font-size:13px; margin-top:16px; }
    .ok { color:#2ecc71; }
</style>
</head>
<body>
<div class="box">
<?php if ($done): ?> <h2 class="ok">Done!</h2> <p>Your admin login has been updated. Log in at <a href="login.php" style="color:#ffb400;">admin/login.php</a> with the
        username and password you just set.</p> <p class="warn"><b>Now delete this file (admin/reset_admin_password.php) from your server.</b> Leaving it in place would let anyone who finds the URL set their own admin login.</p>
<?php else: ?> <h2>Set Admin Credentials</h2> <p style="color:#a0a0a0;font-size:14px;">This replaces the default admin/admin123 login with your own. One-time use - delete this file after.</p> <?php if ($error): ?><p class="warn"><?php echo htmlspecialchars($error); ?></p><?php endif; ?> <form method="POST"> <label>New admin username</label> <input type="text" name="username" required autocomplete="off"> <label>New admin password (min 8 characters)</label> <input type="password" name="password" required autocomplete="new-password"> <button type="submit">Set Credentials</button> </form>
<?php endif; ?>
</div>
</body>
</html>
