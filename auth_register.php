<?php
/* ==========================================================
   auth_register.php - JSON endpoint used by the popup
   Login/Signup modal. Mirrors register.php's logic but
   responds with JSON instead of rendering a page.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $full_name = clean($input['full_name'] ?? '');
    $email     = clean($input['email'] ?? '');
    $phone     = clean($input['phone'] ?? '');
    $password  = $input['password'] ?? '';

    if ($full_name === '' || $email === '' || $password === '') {
        echo json_encode(['success' => false, 'error' => 'Please fill in all fields.']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'This email is already registered. Please login instead.']);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $full_name, $email, $hashed, $phone);

    if ($insert->execute()) {
        $_SESSION['user_id']   = $insert->insert_id;
        $_SESSION['user_name'] = $full_name;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
