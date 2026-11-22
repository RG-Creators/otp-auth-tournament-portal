-- 1️⃣ Create Database

USE tournament;

-- 2️⃣ Users Table (Secure Authentication & Verification)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password_hash VARCHAR(191) NOT NULL,  -- Hashed passwords
    phone VARCHAR(15) UNIQUE NOT NULL,
    otp_code VARCHAR(6) DEFAULT NULL,  -- OTP for signup/forgot password
    otp_expiry DATETIME DEFAULT NULL,  -- OTP expiration time
    is_verified BOOLEAN DEFAULT FALSE,  -- Email/Phone verification
    role ENUM('player', 'admin') DEFAULT 'player',
    is_banned BOOLEAN DEFAULT FALSE,  -- Ban status
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3️⃣ Teams Table (Team Creation & Management)
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) UNIQUE NOT NULL,
    leader_id INT NOT NULL,  
    player_1_id INT NOT NULL,  
    player_2_id INT NOT NULL,  
    player_3_id INT NOT NULL,  
    player_4_id INT NOT NULL,  
    audience_id INT DEFAULT NULL,
    team_avatar VARCHAR(255) DEFAULT 'default_avatar.png',
    is_banned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4️⃣ Tournaments Table (Main Tournament Management)
CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_name VARCHAR(100) NOT NULL,
    tournament_name VARCHAR(255) NOT NULL,
    prize_pool DECIMAL(10,2) NOT NULL,
    entry_fee DECIMAL(10,2) DEFAULT 0.00,
    max_teams INT DEFAULT 16,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    start_date DATETIME NOT NULL,
    end_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5️⃣ Tournament Registrations (Track Registered Teams)
CREATE TABLE tournament_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team_id INT NOT NULL,
    payment_status ENUM('pending', 'completed') DEFAULT 'pending',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6️⃣ Matches Table (Match Scheduling & Results)
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    score_team1 INT DEFAULT 0,
    score_team2 INT DEFAULT 0,
    status ENUM('scheduled', 'ongoing', 'completed') DEFAULT 'scheduled',
    match_time DATETIME NOT NULL,
    winner_team_id INT DEFAULT NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (team1_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (team2_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7️⃣ Leaderboard Table (Fixed `rank` Column Issue)
CREATE TABLE leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_wins INT DEFAULT 0,
    total_losses INT DEFAULT 0,
    total_points INT DEFAULT 0,
    player_rank INT DEFAULT NULL,  -- Renamed from `rank` to `player_rank`
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 🔟 Banned Players & Teams (Security Enforcement)
CREATE TABLE bans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('user', 'team') NOT NULL,
    target_id INT NOT NULL,
    reason TEXT NOT NULL,
    banned_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (banned_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1️⃣1️⃣ OTP Verification Table (For Secure Login & Reset)
CREATE TABLE otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    otp_type ENUM('signup', 'reset') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ✅ Indexing for Faster Queries
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_tournament_status ON tournaments(status);
CREATE INDEX idx_match_status ON matches(status);
CREATE INDEX idx_transaction_status ON payments(status);
