<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

if (!isset($_SESSION['email'])) {
    die(json_encode(["status" => "error", "message" => "Session expired. Please log in again."]));
}

$email = $_SESSION['email'];

// Generate new OTP
$new_otp = generateOTP();
$otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Update OTP in database
$stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $new_otp, $otp_expiry, $email);

if ($stmt->execute()) {
    if (sendOTP($email, $new_otp)) { // Send OTP via email
        echo json_encode(["status" => "success", "message" => "A new OTP has been sent to your email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send OTP email. Please try again."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to generate a new OTP. Please try again."]);
}

$stmt->close();
?>
