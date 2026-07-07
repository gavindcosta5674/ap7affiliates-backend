-- ====================================================
-- Master Affiliate Management Panel
-- Database Schema
-- ====================================================

-- ====================================================
-- 1. admins table
-- ====================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 2. affiliates table
-- ====================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 3. players table
-- ====================================================
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id VARCHAR(20) NOT NULL UNIQUE,
    player_username VARCHAR(100) NOT NULL,
    affiliate_id VARCHAR(20) NOT NULL,
    ftd_date DATE DEFAULT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates(affiliate_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 4. deposits table
-- ====================================================
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id VARCHAR(20) NOT NULL,
    deposit_amount DECIMAL(12,2) DEFAULT 0.00,
    deposit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 5. withdrawals table
-- ====================================================
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id VARCHAR(20) NOT NULL,
    withdrawal_amount DECIMAL(12,2) DEFAULT 0.00,
    withdrawal_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 6. bonuses table
-- ====================================================
CREATE TABLE IF NOT EXISTS bonuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id VARCHAR(20) NOT NULL,
    bonus_amount DECIMAL(12,2) DEFAULT 0.00,
    bonus_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 7. commissions table (auto-calculated)
-- ====================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- 8. media_gallery table
-- ====================================================
CREATE TABLE IF NOT EXISTS media_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    media_type ENUM('image','banner','video','pdf') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_by INT DEFAULT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Insert default admin
-- ====================================================
INSERT INTO admins (username, password) VALUES ('Chris9742', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: The password hash above is 'password' - we need to create the correct one.
-- Run this in PHP to generate the correct hash for 'Chris9742':
-- password_hash('Chris9742', PASSWORD_BCRYPT)
-- We'll set it properly via the setup script.