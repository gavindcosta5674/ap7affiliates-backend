<?php
// ====================================================
// GET /api/reports.php
// Returns player-level reports with revenue/commission
// Supports filtering by affiliate, date range, player
// ====================================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDB();
    
    // Build query with optional filters
    $where = [];
    $params = [];

    // Filter by affiliate
    if (!empty($_GET['affiliate_id'])) {
        $where[] = "p.affiliate_id = ?";
        $params[] = $_GET['affiliate_id'];
    }

    // Filter by player username
    if (!empty($_GET['player_username'])) {
        $where[] = "p.player_username LIKE ?";
        $params[] = '%' . $_GET['player_username'] . '%';
    }

    // Filter by date range
    if (!empty($_GET['date_from'])) {
        $where[] = "p.ftd_date >= ?";
        $params[] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $where[] = "p.ftd_date <= ?";
        $params[] = $_GET['date_to'];
    }

    $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
        SELECT 
            p.player_id,
            p.player_username,
            p.affiliate_id,
            p.ftd_date,
            COALESCE((SELECT SUM(d.deposit_amount) FROM deposits d WHERE d.player_id = p.player_id), 0) AS deposit_total,
            (SELECT COUNT(*) FROM deposits d WHERE d.player_id = p.player_id) AS deposit_count,
            (SELECT COUNT(*) FROM withdrawals w WHERE w.player_id = p.player_id) AS withdrawal_count,
            (SELECT COUNT(*) FROM bonuses b WHERE b.player_id = p.player_id) AS bonus_count,
            COALESCE((SELECT SUM(w.withdrawal_amount) FROM withdrawals w WHERE w.player_id = p.player_id), 0) AS withdrawal_total,
            COALESCE((SELECT SUM(b.bonus_amount) FROM bonuses b WHERE b.player_id = p.player_id), 0) AS bonus_total,
            COALESCE(a.commission_percentage, 20.00) AS commission_percentage
        FROM players p
        LEFT JOIN affiliates a ON a.affiliate_id = p.affiliate_id
        $whereClause
        ORDER BY p.ftd_date DESC, p.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $players = $stmt->fetchAll();

    // Calculate revenue and commission for each player
    $results = [];
    foreach ($players as $player) {
        $revenue = round($player['deposit_total'] - $player['withdrawal_total'] - $player['bonus_total'], 2);
        $commission = round($revenue * ($player['commission_percentage'] / 100), 2);

        $results[] = [
            'id' => $player['player_id'],
            'player_id' => $player['player_id'],
            'player_username' => $player['player_username'],
            'affiliate_id' => $player['affiliate_id'],
            'ftd_date' => $player['ftd_date'] ?? 'N/A',
            'deposit' => $player['deposit_total'],
            'count' => $player['deposit_count'],
            'deposit_count' => $player['deposit_count'],
            'withdrawal' => $player['withdrawal_total'],
            'withdrawal_count' => $player['withdrawal_count'],
            'bonus' => $player['bonus_total'],
            'bonus_count' => $player['bonus_count'],
            'transaction_count' => intval($player['deposit_count']) + intval($player['withdrawal_count']) + intval($player['bonus_count']),
            'revenue' => $revenue,
            'commission' => $commission,
            'commission_percentage' => $player['commission_percentage'],
        ];
    }

    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}