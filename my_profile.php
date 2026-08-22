<?php
/* ==========================================================
   my_profile.php - User Account Settings & Profile
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = clean($_POST['full_name']);
        $phone     = clean($_POST['phone']);

        if ($full_name === '') {
            $error = "Name cannot be empty.";
        } else {
            $upd = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
            $upd->bind_param("ssi", $full_name, $phone, $_SESSION['user_id']);
            $upd->execute();
            $upd->close();
            $_SESSION['user_name'] = $full_name;
            $user['full_name'] = $full_name;
            $user['phone'] = $phone;
            $message = "Profile updated successfully.";
        }
    } elseif (isset($_POST['change_password'])) {
        $current  = $_POST['current_password'];
        $new_pass = $_POST['new_password'];

        if (empty($user['password'])) {
            $error = "This account signed in with Google, so there's no local password to change.";
        } elseif (!password_verify($current, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif (strlen($new_pass) < 6) {
            $error = "New password must be at least 6 characters.";
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $upd->bind_param("si", $hashed, $_SESSION['user_id']);
            $upd->execute();
            $upd->close();
            $message = "Password changed successfully.";
        }
    }
}

$booking_count = $conn->query("SELECT COUNT(*) c FROM bookings WHERE user_id = " . (int)$_SESSION['user_id'])->fetch_assoc()['c'];

include __DIR__ . '/includes/header.php';
?> <div style="max-width:800px;margin:0 auto;"> <h1 style="margin-bottom:6px;">My Profile</h1> <p style="color:var(--text-muted);margin-bottom:28px;">Manage your account details, security credentials, and view booking stats.</p> <?php if ($message): ?><div class="alert alert-success"><?php echo clean($message); ?></div><?php endif; ?> <?php if ($error): ?><div class="alert alert-error"><?php echo clean($error); ?></div><?php endif; ?> <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(340px, 1fr));gap:24px;"> <!-- Account Info Form --> <div class="form-box" style="margin-bottom:0;"> <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;"> <div class="user-avatar-wrap" style="width:48px;height:48px;font-size:20px;"> <?php if (!empty($user['picture'])): ?> <img src="<?php echo clean($user['picture']); ?>" alt="" class="user-avatar" referrerpolicy="no-referrer"> <?php else: ?> <?php echo clean(mb_strtoupper(mb_substr($user['full_name'], 0, 1))); ?> <?php endif; ?> </div> <div> <h3 style="margin:0;font-size:17px;"><?php echo clean($user['full_name']); ?></h3> <span style="font-size:12px;color:var(--neon-cyan);font-family:var(--font-tech);"><?php echo (int)$booking_count; ?> Confirmed Bookings</span> </div> </div> <form method="POST"> <label>Full Name</label> <input type="text" name="full_name" value="<?php echo clean($user['full_name']); ?>" required> <label>Email Address</label> <input type="email" value="<?php echo clean($user['email']); ?>" disabled style="opacity:.6;cursor:not-allowed;"> <label>Phone Number</label> <input type="text" name="phone" value="<?php echo clean($user['phone'] ?? ''); ?>" placeholder="03xx-xxxxxxx"> <button type="submit" name="update_profile" class="btn" style="width:100%;">Save Profile</button> </form> </div> <!-- Security / Password Form --> <div class="form-box" style="margin-bottom:0;"> <h3 style="margin-bottom:16px;">Security & Password</h3> <?php if (empty($user['password'])): ?> <p style="color:var(--text-muted);font-size:13.5px;">This account is authenticated via <strong>Google Sign-In</strong>. There is no password required.</p> <?php else: ?> <form method="POST"> <label>Current Password</label> <input type="password" name="current_password" required> <label>New Password</label> <input type="password" name="new_password" minlength="6" required> <button type="submit" name="change_password" class="btn" style="width:100%;background:var(--bg-surface-elevated);color:#fff;border:1px solid var(--border-glass);">Update Password</button> </form> <?php endif; ?> <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--border-subtle);text-align:center;"> <a class="btn" href="my_bookings.php" style="width:100%;"> View All My Bookings</a> </div> </div> </div>
</div> <?php include __DIR__ . '/includes/footer.php'; ?>
