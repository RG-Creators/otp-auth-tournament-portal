<?php
session_start();
include 'includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch User Info
$stmt = $conn->prepare("SELECT name, email, is_banned FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $is_banned);
$stmt->fetch();
$stmt->close();

// Fetch Team Info
$team_stmt = $conn->prepare("SELECT id, team_name FROM teams WHERE leader_id = ?");
$team_stmt->bind_param("i", $user_id);
$team_stmt->execute();
$team_stmt->bind_result($team_id, $team_name);
$team_stmt->fetch();
$team_stmt->close();

// Fetch Upcoming Tournaments
$tournament_stmt = $conn->prepare("SELECT id, tournament_name, game_name, entry_fee, prize_pool, start_date FROM tournaments WHERE start_date >= CURDATE() ORDER BY start_date ASC");
$tournament_stmt->execute();
$tournament_result = $tournament_stmt->get_result();
$tournament_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Esports Tournament Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard_style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($name ?? 'Gamer') ?>!</h2>
        <p>Email: <?= htmlspecialchars($email ?? '') ?></p>
        <p>Status: <?= $is_banned ? "<span class='banned'>Banned</span>" : "<span class='active'>Active Account</span>" ?></p>

        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert-msg alert-success"><?= htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['err'])): ?>
            <div class="alert-msg alert-error"><?= htmlspecialchars($_SESSION['err']); unset($_SESSION['err']); ?></div>
        <?php endif; ?>

        <h3>Your Roster / Team</h3>
        <?php if ($team_name): ?>
            <p><strong>Team:</strong> <?= htmlspecialchars($team_name) ?> (ID: #<?= (int)$team_id ?>)</p>
        <?php else: ?>
            <p>You have not registered a team roster yet.</p>
            <a href="create_team.php" class="btn-team">+ Create Esports Team</a>
        <?php endif; ?>

        <h3>Upcoming Tournaments</h3>
        <table>
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Game Title</th>
                    <th>Entry Fee</th>
                    <th>Prize Pool</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tournament_result && $tournament_result->num_rows > 0): ?>
                    <?php while ($row = $tournament_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['tournament_name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['game_name']) ?></td>
                            <td>$<?= htmlspecialchars($row['entry_fee']) ?></td>
                            <td>$<?= htmlspecialchars($row['prize_pool']) ?></td>
                            <td><?= htmlspecialchars($row['start_date']) ?></td>
                            <td><a href="register_tournament.php?id=<?= (int)$row['id'] ?>" class="register-btn">Register Team</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888;">No active tournaments scheduled. Check back soon!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="auth/logout.php" class="logout-link">? Sign Out</a>
    </div>
</body>
</html>
