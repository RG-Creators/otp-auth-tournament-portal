<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit();
}

$email = $_SESSION['email'] ?? null;
$entered_otp = trim($_POST['otp']);

// Ensure session exists
if (!$email) {
    echo json_encode(["status" => "error", "message" => "Session expired. Please register again."]);
    exit();
}

// Fetch OTP & Expiry from the database
$stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid request. Try again."]);
    exit();
}

$stmt->bind_result($stored_otp, $otp_expiry);
$stmt->fetch();
$stmt->close();

// Validate OTP & Expiry
if (!$stored_otp) {
    echo json_encode(["status" => "error", "message" => "No OTP found. Please request a new OTP."]);
    exit();
}

if (strtotime($otp_expiry) < time()) {
    echo json_encode(["status" => "error", "message" => "OTP has expired. Please request a new OTP."]);
    exit();
}

// Verify OTP (Plain text comparison since OTP is not hashed)
if ($entered_otp !== $stored_otp) {
    echo json_encode(["status" => "error", "message" => "Incorrect OTP. Please try again."]);
    exit();
}

// Mark user as verified & clear OTP from database
$stmt = $conn->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
$stmt->bind_param("s", $email);
$updateSuccess = $stmt->execute();
$stmt->close();

if ($updateSuccess) {
    unset($_SESSION['email']); // Remove session after verification
    echo json_encode(["status" => "success", "message" => "Email verified successfully!", "redirect" => "../index.php"]);
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Database error. Please try again."]);
    exit();
}
?>
