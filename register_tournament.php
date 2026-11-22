<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$tournament_id = (int)($_GET['id'] ?? 0);

if ($tournament_id <= 0) {
    $_SESSION['err'] = "Invalid tournament selected.";
    header("Location: dashboard.php");
    exit();
}

// 1. Fetch Tournament
$stmt = $conn->prepare("SELECT id, tournament_name FROM tournaments WHERE id = ?");
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$tRes = $stmt->get_result();
$tournament = $tRes->fetch_assoc();
$stmt->close();

if (!$tournament) {
    $_SESSION['err'] = "Tournament not found.";
    header("Location: dashboard.php");
    exit();
}

// 2. Fetch User's Team
$team_stmt = $conn->prepare("SELECT id, team_name FROM teams WHERE leader_id = ?");
$team_stmt->bind_param("i", $user_id);
$team_stmt->execute();
$teamRes = $team_stmt->get_result();
$team = $teamRes->fetch_assoc();
$team_stmt->close();

if (!$team) {
    $_SESSION['err'] = "You must create or lead a team before registering for a tournament.";
    header("Location: create_team.php");
    exit();
}

// 3. Check for existing registration
$chk = $conn->prepare("SELECT id FROM tournament_registrations WHERE tournament_id = ? AND team_id = ?");
$chk->bind_param("ii", $tournament_id, $team['id']);
$chk->execute();
$chkRes = $chk->get_result();
if ($chkRes->num_rows > 0) {
    $_SESSION['err'] = "Team '" . htmlspecialchars($team['team_name']) . "' is already registered for " . htmlspecialchars($tournament['tournament_name']) . ".";
    $chk->close();
    header("Location: dashboard.php");
    exit();
}
$chk->close();

// 4. Register
$ins = $conn->prepare("INSERT INTO tournament_registrations (tournament_id, team_id, registered_at) VALUES (?, ?, NOW())");
$ins->bind_param("ii", $tournament_id, $team['id']);
if ($ins->execute()) {
    $_SESSION['msg'] = "Registration confirmed! Team '" . htmlspecialchars($team['team_name']) . "' entered into " . htmlspecialchars($tournament['tournament_name']) . ".";
} else {
    $_SESSION['err'] = "Registration failed. Please try again.";
}
$ins->close();

header("Location: dashboard.php");
exit();
?>
