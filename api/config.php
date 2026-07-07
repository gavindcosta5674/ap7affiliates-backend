<?php
// ====================================================
// Master Affiliate Management Panel
// Database Configuration
// ====================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'u837790764_masterpanel');
define('DB_USER', 'u837790764_affiliates');
define('DB_PASS', 'Gavin@1991');

// Auto-generate next affiliate ID
function getNextAffiliateId($pdo) {
    $stmt = $pdo->query("SELECT affiliate_id FROM affiliates ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($last) {
        $num = intval(substr($last['affiliate_id'], 3)) + 1;
    } else {
        $num = 100001;
    }
    return 'AFF' . $num;
}

// Get PDO connection
function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
}

// JSON input helper
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

// Start session for login persistence
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}