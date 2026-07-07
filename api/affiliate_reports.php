<?php
// ====================================================
// GET /api/affiliate_reports.php
// Returns rows from the affiliate_reports table (for manual imports)
// Supports optional filters: month, year, finalized
// ====================================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDB();

    $where = [];
    $params = [];

    if (!empty($_GET['month'])) {
        $where[] = "month = ?";
        $params[] = $_GET['month'];
    }
    if (!empty($_GET['year'])) {
        $where[] = "year = ?";
        $params[] = intval($_GET['year']);
    }
    if (isset($_GET['finalized'])) {
        $where[] = "finalized = ?";
        $params[] = intval($_GET['finalized']) ? 1 : 0;
    }

    $whereClause = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

    $sql = "SELECT id, sr_no, affiliate_code, affiliate_name, username, deposit_count, deposit, withdraw, bonus, pl, month, year, finalized, created_at FROM affiliate_reports $whereClause ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    echo json_encode($rows);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
