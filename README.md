# Secure OTP Authentication & Esports Tournament Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![PHPMailer](https://img.shields.io/badge/PHPMailer-6.9-green?style=for-the-badge&logo=php)](https://github.com/PHPMailer/PHPMailer)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

A battle-tested One-Time Password (OTP) verification engine and Esports Tournament management portal. Features multi-factor email verification, rate-limited OTP expiration, secure session regeneration, competitive roster registration, and tournament bracket scheduling.

---

## ?? System Architecture

```
+--------------------------------------------------------------------------+
|                            USER AUTHENTICATION                           |
|   Registration / Login  -->  6-Digit Cryptographic OTP Dispatch (Email)  |
|                                       |                                  |
|                             User Enters OTP                              |
+---------------------------------------|----------------------------------+
                                        v
+--------------------------------------------------------------------------+
|                             SECURITY ENGINE                              |
|   - Expiration Check (`expires_at < NOW()`)                              |
|   - Replay Prevention (Single-Use Token Flag)                            |
|   - Session Re-generation & Cookie Hardening                             |
+---------------------------------------|----------------------------------+
                                        v
+--------------------------------------------------------------------------+
|                         ESPORTS TOURNAMENT HUB                           |
|   - Team Roster Creation (`create_team.php`)                             |
|   - Live Tournament Calendar & Prize Pool Ledger                         |
|   - Atomic Tournament Enrollment (`register_tournament.php`)             |
+--------------------------------------------------------------------------+
```

---

## ? Key Highlights

- **Time-Sensitive OTP Security**: 6-digit random codes generated via `random_int()` with a strict 5-minute validity window.
- **PHPMailer SMTP Dispatch**: Integrated transactional email relay for real-time delivery of authentication challenges.
- **Esports Tournament Registration**: End-to-end team registration, duplicate entry prevention, and tournament roster tracking.
- **Database Hardening**: Parameterized SQL queries preventing injection, unique keys on team memberships, and bans management.
- **Glassmorphic UI**: Clean Poppins typography, subtle frosted-glass containers, and responsive tournament data tables.

---

## ?? Tech Stack

- **Backend**: Core PHP 8.2+, PHPMailer
- **Frontend**: Custom Glassmorphism CSS3, Vanilla JS OTP Input Auto-Tab
- **Database**: MySQL 8.0+ (Normalized Schema with Foreign Key Constraints)

---

## ?? Quick Start

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/RG-Creators/otp-auth-tournament-portal.git
   cd otp-auth-tournament-portal
   ```

2. **Import Database**:
   - Create database `tournament`:
     ```sql
     CREATE DATABASE tournament CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
     ```
   - Import `database_files/tournament.sql`.

3. **Configure Database & Mailer**:
   - Update `includes/db_connect.php` with your database credentials.
   - Configure SMTP credentials in `includes/mail_config.php`.

4. **Launch Server**:
   ```bash
   php -S localhost:8000
   ```
   - Access Portal: `http://localhost:8000/index.php`

---

## ?? License
This project is open-source under the [MIT License](LICENSE).
