<?php
/* ==========================================================
   google_login.php - Verifies a Google Sign-In credential
   (ID token) sent from the browser, then creates/logs in the
   matching user account. No external library needed - the
   token is verified directly against Google's tokeninfo API.
   ========================================================== */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/google.php';

header('Content-Type: application/json');

try {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $credential = $data['credential'] ?? '';

    if ($credential === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing Google credential.']);
        exit;
    }

    // Verify the ID token directly with Google - no extra libraries needed.
    $verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
    $response  = @file_get_contents($verifyUrl);

    if ($response === false) {
        http_response_code(502);
        echo json_encode(['success' => false, 'error' => 'Could not reach Google to verify sign-in.']);
        exit;
    }

    $payload = json_decode($response, true);

    if (!$payload || !isset($payload['aud']) || $payload['aud'] !== GOOGLE_CLIENT_ID) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Google sign-in verification failed. Check GOOGLE_CLIENT_ID in config/google.php.']);
        exit;
    }
    if (!isset($payload['email_verified']) || $payload['email_verified'] !== 'true') {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => "This Google account's email is not verified."]);
        exit;
    }

    $email   = strtolower($payload['email']);
    $name    = $payload['name'] ?? $email;
    $picture = $payload['picture'] ?? null;

    // Look up an existing account by email
    $stmt = $conn->prepare("SELECT user_id, full_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        $user_id = $user['user_id'];
        $upd = $conn->prepare("UPDATE users SET picture = ?, google_id = ? WHERE user_id = ?");
        $google_sub = $payload['sub'] ?? null;
        $upd->bind_param("ssi", $picture, $google_sub, $user_id);
        $upd->execute();
        $upd->close();
        $full_name = $user['full_name'];
    } else {
        // Create a new account. Password stays NULL - this user only signs in via Google.
        $google_sub = $payload['sub'] ?? null;
        $ins = $conn->prepare("INSERT INTO users (full_name, email, password, picture, google_id) VALUES (?, ?, NULL, ?, ?)");
        $ins->bind_param("ssss", $name, $email, $picture, $google_sub);
        $ins->execute();
        $user_id = $ins->insert_id;
        $ins->close();
        $full_name = $name;
    }

    $_SESSION['user_id']      = $user_id;
    $_SESSION['user_name']    = $full_name;
    $_SESSION['user_picture'] = $picture;

    echo json_encode(['success' => true, 'redirect' => 'index.php']);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage() . ' (Did you run database/migration_google_login.sql?)']);
}
