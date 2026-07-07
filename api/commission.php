<?php
// ====================================================
// /api/commission.php
// GET  - Get current commission settings and calculations
// POST - Update global commission percentage
// ====================================================
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getDB();

        // Get all affiliates with their commission percentages
        $affiliates = $pdo->query("
            SELECT affiliate_id, first_name, last_name, commission_percentage,
                (SELECT COUNT(*) FROM players WHERE affiliate_id = affiliates.affiliate_id) AS total_players,
                COALESCE((SELECT SUM(deposit_amount) FROM deposits d JOIN players p ON d.player_id = p.player_id WHERE p.affiliate_id = affiliates.affiliate_id), 0) AS total_deposits,
                COALESCE((SELECT SUM(withdrawal_amount) FROM withdrawals w JOIN players p ON w.player_id = p.player_id WHERE p.affiliate_id = affiliates.affiliate_id), 0) AS total_withdrawals,
                COALESCE((SELECT SUM(bonus_amount) FROM bonuses b JOIN players p ON b.player_id = p.player_id WHERE p.affiliate_id = affiliates.affiliate_id), 0) AS total_bonuses
            FROM affiliates ORDER BY id DESC
        ")->fetchAll();

        foreach ($affiliates as &$a) {
            $revenue = round($a['total_deposits'] - $a['total_withdrawals'] - $a['total_bonuses'], 2);
            $a['revenue'] = $revenue;
            $a['commission'] = round($revenue * ($a['commission_percentage'] / 100), 2);
        }

        // Get overall stats
        $totalDeposits = $pdo->query("SELECT COALESCE(SUM(deposit_amount), 0) FROM deposits")->fetchColumn();
        $totalWithdrawals = $pdo->query("SELECT COALESCE(SUM(withdrawal_amount), 0) FROM withdrawals")->fetchColumn();
        $totalBonuses = $pdo->query("SELECT COALESCE(SUM(bonus_amount), 0) FROM bonuses")->fetchColumn();
        $totalRevenue = round($totalDeposits - $totalWithdrawals - $totalBonuses, 2);

        $avgPct = $pdo->query("SELECT COALESCE(AVG(commission_percentage), 20) FROM affiliates")->fetchColumn();
        $totalCommission = round($totalRevenue * ($avgPct / 100), 2);

        echo json_encode([
            'total_revenue' => $totalRevenue,
            'total_commission' => $totalCommission,
            'average_percentage' => round($avgPct, 2),
            'affiliates' => $affiliates
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($method === 'POST') {
    try {
        $pdo = getDB();
        $data = getJsonInput();

        // Update global percentage for all affiliates
        if (isset($data['global_percentage'])) {
            $pct = floatval($data['global_percentage']);
            $stmt = $pdo->prepare("UPDATE affiliates SET commission_percentage = ?");
            $stmt->execute([$pct]);
            echo json_encode(['success' => true, 'message' => 'Global commission updated', 'percentage' => $pct]);
            exit;
        }

        // Update single affiliate's percentage
        if (isset($data['affiliate_id']) && isset($data['percentage'])) {
            $stmt = $pdo->prepare("UPDATE affiliates SET commission_percentage = ? WHERE affiliate_id = ?");
            $stmt->execute([floatval($data['percentage']), $data['affiliate_id']]);
            echo json_encode(['success' => true, 'message' => 'Commission updated for ' . $data['affiliate_id']]);
            exit;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);