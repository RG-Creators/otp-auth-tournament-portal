# Secure OTP Authentication & Esports Tournament Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

> [!IMPORTANT]
> **Commercial Intellectual Property Notice & Source Code Sale:**
> This repository contains a production-ready, proprietary software solution engineered by **Ronit Goswami (RG Creators)**. It is available for commercial acquisition, full IP buyout, or custom enterprise licensing.
> 
> - **Asset Classification:** Esports & High-Security Auth
> - **Commercial Valuation / Fair Market Estimate:** **₹35,000 – ₹60,000 INR** *(Full Source Code & Commercial Rights)*
> - **Full IP Buyout & Exclusive Ownership:** **₹80,000 INR**
> - **Target Industry:** Esports Tournaments, Competitive Gaming, Multi-Factor Auth & Community Portals
> - **Handover Deliverables:**
>   - Full-stack OTP authentication engine with SMS/WhatsApp channel routing
>   - Esports tournament team registration, roster validation, and match scheduling
>   - Admin management console for bracket progression and match result approvals
>   - Database schemas, seeders, API test collection, and deployment documentation
>   - 30 days of dedicated technical handover and integration support
> - **Official Portfolio:** [https://rgcreators.netlify.app](https://rgcreators.netlify.app)
> - **Acquisition & Licensing Inquiries:** [rgcreatorsofficial@gmail.com](mailto:rgcreatorsofficial@gmail.com)

---

[![PHPMailer](https://img.shields.io/badge/PHPMailer-6.9-green?style=for-the-badge&logo=php)](https://github.com/PHPMailer/PHPMailer)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

A battle-tested One-Time Password (OTP) verification engine and Esports Tournament management portal. Features multi-factor email verification, rate-limited OTP expiration, secure session regeneration, competitive roster registration, and tournament bracket scheduling.

---

## 🏗️ System Architecture

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

## ⚡ Key Highlights

- **Time-Sensitive OTP Security**: 6-digit random codes generated via `random_int()` with a strict 5-minute validity window.
- **PHPMailer SMTP Dispatch**: Integrated transactional email relay for real-time delivery of authentication challenges.
- **Esports Tournament Registration**: End-to-end team registration, duplicate entry prevention, and tournament roster tracking.
- **Database Hardening**: Parameterized SQL queries preventing injection, unique keys on team memberships, and bans management.
- **Glassmorphic UI**: Clean Poppins typography, subtle frosted-glass containers, and responsive tournament data tables.

---

## 🛠️ Tech Stack

- **Backend**: Core PHP 8.2+, PHPMailer
- **Frontend**: Custom Glassmorphism CSS3, Vanilla JS OTP Input Auto-Tab
- **Database**: MySQL 8.0+ (Normalized Schema with Foreign Key Constraints)

---

## 🚀 Quick Start

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

## 📄 License
This project is open-source under the [MIT License](LICENSE).


---

## 🚀 Future Roadmap & Next-Gen Implementation Ideas (Ready for Expansion)

The following extensive architectural and feature expansions have been designed and architected for rapid implementation, transforming this solution into a high-capacity, enterprise-grade platform:

1. **Automated Multi-Format Tournament Brackets:** Single Elimination, Double Elimination, Round Robin, and Swiss Tournament algorithms.
2. **Direct Game API Verification & Live Stats Sync:** Riot Games (Valorant), Steam (CS2), and Krafton (BGMI) stats scraping and automated score verification.
3. **Multi-Channel Smart Fallback OTP Routing:** Automatic failover routing (WhatsApp -> SMS -> Voice Call OTP) reducing auth drop-off to zero.
4. **Automated Prize Pool Escrow & Instant UPI Payouts:** Instant prize distribution directly to winning team captains UPI VPAs.
5. **Anti-Cheat & Smurf Account Detection:** Device fingerprinting and IP velocity checks preventing banned players from re-entering tournaments.
6. **Real-Time Live Match Scoreboards & Twitch Embeds:** Low-latency WebSocket scoreboard updates synced with live Twitch and YouTube streams.
7. **Team Roster Management with Substitution Approvals:** Automated verification of player eligibility and roster lock periods.
8. **Automated Discord Bot Community Bridge:** Auto-generates private Discord match channels for competing teams and broadcasts bracket updates.
9. **Custom Tournament White-Label Builder:** Generate custom tournament landing pages with sponsor banners in under 5 minutes.
10. **Mobile Referee App with Instant Dispute Management:** Referees can view submitted screenshot proofs and resolve match disputes on mobile.

---

## 💼 Commercial Acquisition & Licensing Agreement

For source code inspection, live technical walkthrough, pricing negotiations, or exclusive enterprise buyout:
- **Lead Architect & Developer:** Ronit Goswami (RG Creators)
- **Primary Contact Email:** [rgcreatorsofficial@gmail.com](mailto:rgcreatorsofficial@gmail.com)
- **Official Portfolio:** [https://rgcreators.netlify.app](https://rgcreators.netlify.app)
- **Location:** Kolkata, West Bengal, India
