<?php
/* ==========================================================
   register.php - New visitor registration
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($_POST['full_name']);
    $email     = clean($_POST['email']);
    $phone     = clean($_POST['phone']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    if ($full_name === '' || $email === '' || $password === '') {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already registered
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "This email is already registered. Please login.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $full_name, $email, $hashed, $phone);
            if ($insert->execute()) {
                redirect('login.php?registered=1');
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
}

include __DIR__ . '/includes/header.php';
?> <div class="auth-page-wrap"> <div class="form-box"> <h2>Create Account</h2> <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <form method="POST" action="register.php"> <label>Full Name</label> <input type="text" name="full_name" required> <label>Email</label> <input type="email" name="email" required> <label>Phone</label> <input type="text" name="phone"> <label>Password</label> <input type="password" name="password" required> <label>Confirm Password</label> <input type="password" name="confirm_password" required> <button type="submit">Register</button> </form> <?php include __DIR__ . '/includes/google_signin.php'; ?> <p style="margin-top:14px;">Already have an account? <a href="login.php">Login here</a></p>
</div> </div> <?php include __DIR__ . '/includes/footer.php'; ?>
