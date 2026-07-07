<?php
// ====================================================
// /api/affiliates.php
// GET  - List all affiliates
// POST - Create new affiliate
// DELETE - Delete affiliate (via ?id= parameter)
// ====================================================
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: List all affiliates ─────────────────────────────────────────
if ($method === 'GET') {
    try {
        $pdo = getDB();
        $stmt = $pdo->query("
            SELECT 
                a.affiliate_id,
                a.first_name,
                a.last_name,
                a.username,
                a.status,
                a.commission_percentage,
                a.created_at,
                (SELECT COUNT(*) FROM players p WHERE p.affiliate_id = a.affiliate_id) AS total_players,
                COALESCE((SELECT SUM(d.deposit_amount) FROM deposits d JOIN players p ON d.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_deposits,
                COALESCE((SELECT SUM(w.withdrawal_amount) FROM withdrawals w JOIN players p ON w.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_withdrawals,
                COALESCE((SELECT SUM(b.bonus_amount) FROM bonuses b JOIN players p ON b.player_id = p.player_id WHERE p.affiliate_id = a.affiliate_id), 0) AS total_bonuses
            FROM affiliates a
            ORDER BY a.id DESC
        ");
        $affiliates = $stmt->fetchAll();

        // Calculate revenue and commission for each
        foreach ($affiliates as &$aff) {
            $aff['revenue'] = round($aff['total_deposits'] - $aff['total_withdrawals'] - $aff['total_bonuses'], 2);
            $aff['commission'] = round($aff['revenue'] * ($aff['commission_percentage'] / 100), 2);
            $aff['id'] = $aff['affiliate_id'];
        }

        echo json_encode($affiliates);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── POST: Create new affiliate ──────────────────────────────────────
if ($method === 'POST') {
    try {
        $pdo = getDB();
        $data = getJsonInput();

        $first_name = $data['first_name'] ?? '';
        $last_name  = $data['last_name'] ?? '';
        $username   = $data['username'] ?? '';
        $password   = $data['password'] ?? '';
        $percentage = floatval($data['percentage'] ?? 20.00);

        if (empty($first_name) || empty($last_name) || empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required']);
            exit;
        }

        // Check duplicate username
        $check = $pdo->prepare("SELECT id FROM affiliates WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            exit;
        }

        $affiliate_id = getNextAffiliateId($pdo);
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO affiliates (affiliate_id, first_name, last_name, username, password, commission_percentage, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$affiliate_id, $first_name, $last_name, $username, $hashed, $percentage]);

        echo json_encode([
            'id' => $affiliate_id,
            'affiliate_id' => $affiliate_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'status' => 'active',
            'commission_percentage' => $percentage,
            'total_players' => 0,
            'total_deposits' => 0,
            'total_withdrawals' => 0,
            'total_bonuses' => 0,
            'revenue' => 0,
            'commission' => 0
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── DELETE: Remove affiliate ────────────────────────────────────────
if ($method === 'DELETE') {
    try {
        $pdo = getDB();
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Affiliate ID required']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM affiliates WHERE affiliate_id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Affiliate not found']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Affiliate deleted']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── PUT: Toggle affiliate status ────────────────────────────────
if ($method === 'PUT') {
    try {
        $pdo = getDB();
        $id = $_GET['id'] ?? '';
        $status = $_GET['status'] ?? '';

        if (empty($id) || empty($status)) {
            http_response_code(400);
            echo json_encode(['error' => 'Affiliate ID and status required']);
            exit;
        }

        if (!in_array($status, ['active', 'inactive'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status value']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE affiliates SET status = ? WHERE affiliate_id = ?");
        $stmt->execute([$status, $id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Affiliate not found']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => "Affiliate $id status changed to $status"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
