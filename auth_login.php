<?php
/* ==========================================================
   auth_login.php - JSON endpoint used by the popup Login/Signup
   modal (assets/js/auth-modal.js). Mirrors login.php's logic
   but responds with JSON instead of rendering a page.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $email    = clean($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if ($email === '' || $password === '') {
        echo json_encode(['success' => false, 'error' => 'Please enter your email and password.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, full_name, password, picture FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] === null) {
            echo json_encode(['success' => false, 'error' => 'This account uses Google Sign-In. Please use the Google button.']);
            exit;
        }
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['user_id'];
            $_SESSION['user_name']    = $user['full_name'];
            $_SESSION['user_picture'] = $user['picture'];
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Incorrect email or password.']);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
