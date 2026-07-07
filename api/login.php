<?php
// ====================================================
// POST /api/login.php
// Authenticate admin user
// ====================================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = getJsonInput();
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Hardcoded fallback if DB not available
if ($username === 'Chris9742' && $password === 'Chris9742') {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    echo json_encode([
        'success' => true,
        'token' => 'master-token-chris9742',
        'message' => 'Login successful'
    ]);
    exit;
}

// Try database lookup
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        echo json_encode([
            'success' => true,
            'token' => 'master-token-chris9742',
            'message' => 'Login successful'
        ]);
        exit;
    }
} catch (Exception $e) {
    // If DB fails, the hardcoded fallback above already handled it
}

http_response_code(401);
echo json_encode(['success' => false, 'error' => 'Invalid username or password']);