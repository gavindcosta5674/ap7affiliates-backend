<?php
// ====================================================
// /api/players.php
// GET    - List all players with their financial data
// POST   - Create a new player (links to affiliate)
// DELETE - Delete player (?id=)
// ====================================================
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: List all players ───────────────────────────────────────────
if ($method === 'GET') {
    try {
        $pdo = getDB();

        $sql = "
            SELECT 
                p.*,
                COALESCE((SELECT SUM(d.deposit_amount) FROM deposits d WHERE d.player_id = p.player_id), 0) AS deposit_total,
                (SELECT COUNT(*) FROM deposits d WHERE d.player_id = p.player_id) AS deposit_count,
                COALESCE((SELECT SUM(w.withdrawal_amount) FROM withdrawals w WHERE w.player_id = p.player_id), 0) AS withdrawal_total,
                COALESCE((SELECT SUM(b.bonus_amount) FROM bonuses b WHERE b.player_id = p.player_id), 0) AS bonus_total,
                COALESCE(a.commission_percentage, 20.00) AS commission_percentage
            FROM players p
            LEFT JOIN affiliates a ON a.affiliate_id = p.affiliate_id
            ORDER BY p.id DESC
        ";

        $stmt = $pdo->query($sql);
        $players = $stmt->fetchAll();

        foreach ($players as &$player) {
            $player['revenue'] = round($player['deposit_total'] - $player['withdrawal_total'] - $player['bonus_total'], 2);
            $player['commission'] = round($player['revenue'] * ($player['commission_percentage'] / 100), 2);
        }

        echo json_encode($players);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── POST: Create player ─────────────────────────────────────────────
if ($method === 'POST') {
    try {
        $pdo = getDB();
        $data = getJsonInput();

        $playerId = 'PLR' . strtoupper(uniqid());
        $playerUsername = $data['player_username'] ?? '';
        $affiliateId = $data['affiliate_id'] ?? '';
        $ftdDate = $data['ftd_date'] ?? date('Y-m-d');

        if (empty($playerUsername) || empty($affiliateId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Player username and affiliate ID required']);
            exit;
        }

        // Verify affiliate exists
        $check = $pdo->prepare("SELECT id FROM affiliates WHERE affiliate_id = ?");
        $check->execute([$affiliateId]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Affiliate not found']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO players (player_id, player_username, affiliate_id, ftd_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$playerId, $playerUsername, $affiliateId, $ftdDate]);

        // If deposit data is provided, store it
        if (isset($data['deposit_amount']) && floatval($data['deposit_amount']) > 0) {
            $depStmt = $pdo->prepare("INSERT INTO deposits (player_id, deposit_amount) VALUES (?, ?)");
            $depStmt->execute([$playerId, floatval($data['deposit_amount'])]);
        }

        // If withdrawal data is provided, store it
        if (isset($data['withdrawal_amount']) && floatval($data['withdrawal_amount']) > 0) {
            $wdStmt = $pdo->prepare("INSERT INTO withdrawals (player_id, withdrawal_amount) VALUES (?, ?)");
            $wdStmt->execute([$playerId, floatval($data['withdrawal_amount'])]);
        }

        // If bonus data is provided, store it
        if (isset($data['bonus_amount']) && floatval($data['bonus_amount']) > 0) {
            $bonStmt = $pdo->prepare("INSERT INTO bonuses (player_id, bonus_amount) VALUES (?, ?)");
            $bonStmt->execute([$playerId, floatval($data['bonus_amount'])]);
        }

        echo json_encode([
            'success' => true,
            'player_id' => $playerId,
            'player_username' => $playerUsername,
            'affiliate_id' => $affiliateId,
            'message' => 'Player created successfully'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── DELETE: Remove player ───────────────────────────────────────────
if ($method === 'DELETE') {
    try {
        $pdo = getDB();
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Player ID required']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM players WHERE player_id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Player deleted']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);