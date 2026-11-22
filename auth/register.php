<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    //Validate Inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: ../index.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header("Location: ../index.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error_message'] = "Password must be at least 6 characters.";
        header("Location: ../index.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: ../index.php");
        exit();
    }

    // ✅ Check if user exists
    $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $is_verified);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if ($is_verified) {
            $_SESSION['error_message'] = "Email already registered and verified.";
            header("Location: ../index.php");
            exit();
        } else {
            //Delete unverified user before re-registering
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE email = ?");
            $deleteStmt->bind_param("s", $email);
            $deleteStmt->execute();
            $deleteStmt->close();
        }
    }
    $stmt->close();

    //Check if user is banned
    if (isUserBanned($email)) {
        $_SESSION['error_message'] = "Your account is banned.";
        header("Location: ../index.php");
        exit();
    }

    // Generate OTP & Store it temporarily
    $otp = generateOTP();
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    //Insert new user with OTP
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, otp_code, otp_expiry, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sssss", $name, $email, $hashedPassword, $otp, $otp_expiry);

    if ($stmt->execute()) {
        sendOTP($email, $otp); // Send OTP via email
        $_SESSION['email'] = $email;
        header("Location: verify_otp.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error registering user. Try again.";
        header("Location: ../index.php");
        exit();
    }
    $stmt->close();
}
?>
