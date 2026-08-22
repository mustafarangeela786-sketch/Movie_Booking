<?php
/* ==========================================================
   admin/login.php - Administrator login
   Default credentials: username "admin" / password "admin123"
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['username'];
            redirect('dashboard.php');
        } else {
            $error = "Incorrect username or password.";
        }
    } else {
        $error = "Incorrect username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head> <meta charset="UTF-8"> <title>Admin Login - MovieBook</title> <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<main class="container"> <div class="auth-page-wrap"> <div class="form-box"> <h2>Admin Login</h2> <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?> <form method="POST" action="login.php"> <label>Username</label> <input type="text" name="username" autocomplete="off" required> <label>Password</label> <input type="password" name="password" autocomplete="new-password" required> <button type="submit">Login</button> </form> <p style="margin-top:14px;"><a href="../index.php">&larr; Back to site</a></p> </div> </div>
</main>
</body>
</html>
