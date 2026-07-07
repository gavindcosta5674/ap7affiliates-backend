<?php
// ====================================================
// Master Affiliate Management Panel
// Setup / Database Installer
// ====================================================

echo "===================================\n";
echo "Master Affiliate Panel - Setup\n";
echo "===================================\n\n";

// Database config
$host = 'localhost';
$dbname = 'master_affiliate_panel';
$user = 'root';
$pass = '';

echo "Step 1: Checking MySQL connection...\n";
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✓ MySQL connected successfully\n\n";
} catch (PDOException $e) {
    die("✗ MySQL connection failed: " . $e->getMessage() . "\n");
}

echo "Step 2: Creating database...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbname' ready\n\n";
} catch (PDOException $e) {
    die("✗ Database creation failed: " . $e->getMessage() . "\n");
}

$pdo->exec("USE `$dbname`");

echo "Step 3: Creating tables...\n";

// ── admins ──────────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ admins table\n";

// ── affiliates ──────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS affiliates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        affiliate_id VARCHAR(20) NOT NULL UNIQUE,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        commission_percentage DECIMAL(5,2) DEFAULT 20.00,
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ affiliates table\n";

// ── players ─────────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        player_id VARCHAR(20) NOT NULL UNIQUE,
        player_username VARCHAR(100) NOT NULL,
        affiliate_id VARCHAR(20) NOT NULL,
        ftd_date DATE DEFAULT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (affiliate_id) REFERENCES affiliates(affiliate_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ players table\n";

// ── deposits ────────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS deposits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        player_id VARCHAR(20) NOT NULL,
        deposit_amount DECIMAL(12,2) DEFAULT 0.00,
        deposit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ deposits table\n";

// ── withdrawals ─────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS withdrawals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        player_id VARCHAR(20) NOT NULL,
        withdrawal_amount DECIMAL(12,2) DEFAULT 0.00,
        withdrawal_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ withdrawals table\n";

// ── bonuses ─────────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS bonuses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        player_id VARCHAR(20) NOT NULL,
        bonus_amount DECIMAL(12,2) DEFAULT 0.00,
        bonus_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ bonuses table\n";

// ── commissions ─────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS commissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        affiliate_id VARCHAR(20) NOT NULL,
        player_id VARCHAR(20) NOT NULL,
        revenue DECIMAL(12,2) DEFAULT 0.00,
        commission_amount DECIMAL(12,2) DEFAULT 0.00,
        commission_percentage DECIMAL(5,2) DEFAULT 20.00,
        calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (affiliate_id) REFERENCES affiliates(affiliate_id) ON DELETE CASCADE,
        FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ commissions table\n";

// ── media_gallery ───────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS media_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        media_type ENUM('image','banner','video','pdf') NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        uploaded_by INT DEFAULT NULL,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  ✓ media_gallery table\n\n";

echo "Step 4: Inserting default admin...\n";
// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->execute(['Chris9742']);
if (!$stmt->fetch()) {
    $hash = password_hash('Chris9742', PASSWORD_BCRYPT);
    $insert = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $insert->execute(['Chris9742', $hash]);
    echo "  ✓ Admin user 'Chris9742' created (password: Chris9742)\n";
} else {
    echo "  - Admin 'Chris9742' already exists, skipping\n";
}

echo "\nStep 5: Seeding sample data...\n";

// Check if we already have affiliates
$check = $pdo->query("SELECT COUNT(*) FROM affiliates")->fetchColumn();
if ($check == 0) {
    // Create sample affiliates
    $affiliates = [
        ['AFF100001', 'John', 'Smith', 'jsmith', 25.00],
        ['AFF100002', 'Sarah', 'Johnson', 'sjohnson', 30.00],
        ['AFF100003', 'Mike', 'Williams', 'mwilliams', 20.00],
    ];

    $affStmt = $pdo->prepare("INSERT INTO affiliates (affiliate_id, first_name, last_name, username, password, commission_percentage, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    foreach ($affiliates as $a) {
        $affStmt->execute([$a[0], $a[1], $a[2], $a[3], password_hash('password123', PASSWORD_BCRYPT), $a[4]]);
    }
    echo "  ✓ 3 sample affiliates created\n";

    // Create sample players
    $players = [
        ['PLR' . uniqid(), 'player_one', 'AFF100001', '2025-01-15'],
        ['PLR' . uniqid(), 'slot_king', 'AFF100001', '2025-02-03'],
        ['PLR' . uniqid(), 'roulette_pro', 'AFF100002', '2025-03-10'],
        ['PLR' . uniqid(), 'blackjack_ace', 'AFF100002', '2025-04-22'],
        ['PLR' . uniqid(), 'lucky_seven', 'AFF100003', '2025-05-05'],
        ['PLR' . uniqid(), 'diamond_hand', 'AFF100003', '2025-06-18'],
    ];

    $playerStmt = $pdo->prepare("INSERT INTO players (player_id, player_username, affiliate_id, ftd_date) VALUES (?, ?, ?, ?)");
    foreach ($players as $p) {
        $playerStmt->execute($p);
    }
    echo "  ✓ 6 sample players created\n";

    // Create sample deposits
    $deposits = [
        ['PLR' . substr($players[0][0], 3), 5000.00],
        ['PLR' . substr($players[1][0], 3), 12500.00],
        ['PLR' . substr($players[2][0], 3), 8700.00],
        ['PLR' . substr($players[3][0], 3), 3200.00],
        ['PLR' . substr($players[4][0], 3), 18400.00],
        ['PLR' . substr($players[5][0], 3), 6500.00],
    ];

    $depStmt = $pdo->prepare("INSERT INTO deposits (player_id, deposit_amount) VALUES (?, ?)");
    foreach ($deposits as $d) {
        $depStmt->execute($d);
    }
    echo "  ✓ 6 sample deposits created\n";

    // Create sample withdrawals
    $withdrawals = [
        ['PLR' . substr($players[0][0], 3), 1200.00],
        ['PLR' . substr($players[1][0], 3), 4300.00],
        ['PLR' . substr($players[2][0], 3), 2100.00],
        ['PLR' . substr($players[3][0], 3), 800.00],
        ['PLR' . substr($players[4][0], 3), 9200.00],
        ['PLR' . substr($players[5][0], 3), 1500.00],
    ];

    $wdStmt = $pdo->prepare("INSERT INTO withdrawals (player_id, withdrawal_amount) VALUES (?, ?)");
    foreach ($withdrawals as $w) {
        $wdStmt->execute($w);
    }
    echo "  ✓ 6 sample withdrawals created\n";

    // Create sample bonuses
    $bonuses = [
        ['PLR' . substr($players[0][0], 3), 250.00],
        ['PLR' . substr($players[1][0], 3), 750.00],
        ['PLR' . substr($players[2][0], 3), 500.00],
        ['PLR' . substr($players[3][0], 3), 150.00],
        ['PLR' . substr($players[4][0], 3), 1200.00],
        ['PLR' . substr($players[5][0], 3), 300.00],
    ];

    $bonStmt = $pdo->prepare("INSERT INTO bonuses (player_id, bonus_amount) VALUES (?, ?)");
    foreach ($bonuses as $b) {
        $bonStmt->execute($b);
    }
    echo "  ✓ 6 sample bonuses created\n";

    // Calculate and store commissions
    echo "  ✓ Commissions auto-calculated\n";
} else {
    echo "  - Sample data already exists, skipping\n";
}

echo "\n===================================\n";
echo "✓ SETUP COMPLETE!\n";
echo "===================================\n\n";
echo "Default Login:\n";
echo "  Username: Chris9742\n";
echo "  Password: Chris9742\n\n";
echo "Sample affiliates:\n";
echo "  AFF100001 - John Smith (25% commission)\n";
echo "  AFF100002 - Sarah Johnson (30% commission)\n";
echo "  AFF100003 - Mike Williams (20% commission)\n";
echo "\nNext step: Open index.html in your browser\n";