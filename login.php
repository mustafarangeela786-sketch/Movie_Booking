<?php
/* ==========================================================
   login.php - Visitor / registered user login
   (Admin has a separate login at admin/login.php)
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = "";
$registered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, full_name, password, picture FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] === null) {
            $error = "This account was created with Google Sign-In. Please use the Google button below.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['user_id'];
            $_SESSION['user_name']    = $user['full_name'];
            $_SESSION['user_picture'] = $user['picture'];
            redirect('index.php');
        } else {
            $error = "Incorrect email or password.";
        }
    } else {
        $error = "Incorrect email or password.";
    }
    $stmt->close();
}

include __DIR__ . '/includes/header.php';
?> <div class="auth-page-wrap"> <div class="form-box"> <h2>Login</h2> <?php if ($registered): ?><div class="alert alert-success">Registration successful! Please login.</div><?php endif; ?> <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <form method="POST" action="login.php"> <label>Email</label> <input type="email" name="email" autocomplete="off" required> <label>Password</label> <input type="password" name="password" autocomplete="new-password" required> <button type="submit">Login</button> </form> <?php include __DIR__ . '/includes/google_signin.php'; ?> <p style="margin-top:14px;">New here? <a href="register.php">Create an account</a></p>
</div> </div> <?php include __DIR__ . '/includes/footer.php'; ?>
