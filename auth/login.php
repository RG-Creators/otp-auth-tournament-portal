<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Email & Password are required.";
        header("Location: ../index.php");
        exit();
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, password_hash, is_verified, is_banned FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashedPassword, $is_verified, $is_banned);
    $stmt->fetch();

    if ($stmt->num_rows == 0) {
        $_SESSION['error_message'] = "User not found.";
        header("Location: ../index.php");
        exit();
    }

    // Check if user is banned
    if ($is_banned) {
        $_SESSION['error_message'] = "Your account has been banned.";
        header("Location: ../index.php");
        exit();
    }

    // Check if email is verified
    if (!$is_verified) {
        $_SESSION['error_message'] = "Please verify your email before logging in.";
        header("Location: ../index.php");
        exit();
    }

    //Correctly Verify Password**
    if (!password_verify($password, $hashedPassword)) { 
        $_SESSION['error_message'] = "Incorrect password.";
        header("Location: ../index.php");
        exit();
    }

    //Start session & redirect to dashboard**
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    header("Location: ../dashboard.php");
    exit();
}
?>
