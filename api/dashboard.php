<?php
// ====================================================
// GET /api/dashboard.php
// Returns aggregated dashboard metrics
// ====================================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDB();

    // Total affiliates
    $totalAffiliates = $pdo->query("SELECT COUNT(*) FROM affiliates")->fetchColumn();

    // Active affiliates
    $activeAffiliates = $pdo->query("SELECT COUNT(*) FROM affiliates WHERE status = 'active'")->fetchColumn();

    // Total players
    $totalPlayers = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();

    // Total deposits
    $totalDeposits = $pdo->query("SELECT COALESCE(SUM(deposit_amount), 0) FROM deposits")->fetchColumn();

    // Total withdrawals
    $totalWithdrawals = $pdo->query("SELECT COALESCE(SUM(withdrawal_amount), 0) FROM withdrawals")->fetchColumn();

    // Total bonuses
    $totalBonuses = $pdo->query("SELECT COALESCE(SUM(bonus_amount), 0) FROM bonuses")->fetchColumn();

    // Total revenue = deposits - withdrawals - bonuses
    $totalRevenue = round($totalDeposits - $totalWithdrawals - $totalBonuses, 2);

    // Total commission (sum all commission records or calculate)
    $totalCommission = $pdo->query("SELECT COALESCE(SUM(commission_amount), 0) FROM commissions")->fetchColumn();
    if ($totalCommission == 0 && $totalRevenue > 0) {
        // Fallback: calculate from affiliates
        $stmt = $pdo->query("SELECT commission_percentage FROM affiliates WHERE status = 'active'");
        $pcts = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $avgPct = count($pcts) > 0 ? array_sum($pcts) / count($pcts) : 20;
        $totalCommission = round($totalRevenue * ($avgPct / 100), 2);
    }

    // Recent activity (top 10 affiliates with stats)
    $recent = $pdo->query("
        SELECT 
            a.affiliate_id,
            a.first_name,
            a.last_name,
            (SELECT COUNT(*) FROM players p WHERE p.affiliate_id = a.affiliate_id) AS total_players,
            COALESCE((SELECT SUM(d.deposit_amount) FROM deposits d JOIN players p ON d.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_deposits,
            COALESCE((SELECT SUM(w.withdrawal_amount) FROM withdrawals w JOIN players p ON w.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_withdrawals,
            COALESCE((SELECT SUM(b.bonus_amount) FROM bonuses b JOIN players p ON b.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_bonuses,
            a.commission_percentage
        FROM affiliates a
        ORDER BY total_players DESC, a.id DESC
        LIMIT 10
    ")->fetchAll();

    foreach ($recent as &$row) {
        $row['revenue'] = round($row['total_deposits'] - $row['total_withdrawals'] - $row['total_bonuses'], 2);
        $row['commission'] = round($row['revenue'] * ($row['commission_percentage'] / 100), 2);
    }

    echo json_encode([
        'total_affiliates' => (int) $totalAffiliates,
        'active_affiliates' => (int) $activeAffiliates,
        'total_players' => (int) $totalPlayers,
        'total_deposits' => round((float) $totalDeposits, 2),
        'total_withdrawals' => round((float) $totalWithdrawals, 2),
        'total_bonuses' => round((float) $totalBonuses, 2),
        'total_revenue' => $totalRevenue,
        'total_commission' => $totalCommission,
        'recent_activity' => $recent,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}