<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = trim($_POST['team_name'] ?? '');

    if (empty($team_name)) {
        $error = "Team name cannot be empty.";
    } else {
        // Check if user already has a team
        $check = $conn->prepare("SELECT id FROM teams WHERE leader_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "You already have a registered team roster.";
        } else {
            $ins = $conn->prepare("INSERT INTO teams (team_name, leader_id) VALUES (?, ?)");
            $ins->bind_param("si", $team_name, $user_id);
            if ($ins->execute()) {
                $_SESSION['msg'] = "Team '" . htmlspecialchars($team_name) . "' successfully created!";
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Failed to create team. Name might already be taken.";
            }
            $ins->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team | Esports Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard_style.css">
</head>
<body>
    <div class="dashboard-container" style="max-width: 500px;">
        <h2>Create Esports Team</h2>
        <p>Register your roster to participate in upcoming tournaments.</p>

        <?php if ($error): ?>
            <div class="alert-msg alert-error" style="margin-top: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" style="margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Team Name</label>
                <input type="text" name="team_name" required placeholder="e.g. Phoenix Vanguard" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>
            <button type="submit" class="btn-team" style="border: none; cursor: pointer; width: 100%; padding: 12px; font-size: 14px;">Register Team</button>
        </form>

        <a href="dashboard.php" class="logout-link" style="display: inline-block; margin-top: 15px;">? Back to Dashboard</a>
    </div>
</body>
</html>
