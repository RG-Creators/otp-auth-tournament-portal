<?php
include 'db_connect.php';

// Securely Generate OTP (6 Digits)
function generateOTP() {
    return random_int(100000, 999999);
}

// Encrypt OTP Before Storing
function encryptOTP($otp) {
    return password_hash($otp, PASSWORD_BCRYPT);
}

// Verify OTP (Returns True if OTP is Correct)
function verifyOTP($enteredOTP, $storedOTP) {
    return password_verify($enteredOTP, $storedOTP);
}

// Hash Password Securely
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify Password Against Hashed Password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Send OTP Email
function sendOTP($email, $otp) {
    require 'mailer.php';

    $subject = "Verify Your Email - ClutchZone";

    // Email Body with ClutchZone Watermark
    $body = '
    <div style="max-width: 600px; margin: auto; padding: 20px; font-family: Arial, sans-serif; background: #f4f4f4; border-radius: 10px; border: 1px solid #ddd;">
        
        <!-- ClutchZone Watermark -->
        <div style="text-align: center; font-size: 28px; font-weight: bold; color: #ccc; letter-spacing: 3px; margin-bottom: 20px;">
            CLUTCHZONE
        </div>

        <!-- Email Header -->
        <div style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center;">
            <h2 style="color: #333;">Email Verification</h2>
            <p style="font-size: 16px; color: #555;">Use the OTP below to verify your email. This OTP is valid for <strong>10 minutes</strong>.</p>

            <!-- OTP Box -->
            <div style="margin: 20px auto; padding: 15px; background: #eafaf1; border: 2px dashed #28a745; color: #28a745; font-size: 24px; font-weight: bold; text-align: center; width: fit-content; border-radius: 5px;">
                ' . $otp . '
            </div>

            <p style="font-size: 14px; color: #777;">If you did not request this, please ignore this email.</p>
        </div>

        <!-- Privacy & Security Notice -->
        <div style="margin-top: 20px; padding: 15px; font-size: 13px; text-align: center; color: #555;">
            <p>For security reasons, do not share this OTP with anyone.</p>
            <p>By verifying your email, you agree to our <a href="../index.html" style="color: #007bff; text-decoration: none;">Privacy Policy</a>.</p>
        </div>

        <!-- Footer -->
        <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #888;">
            <p>Best Regards,</p>
            <p><strong>ClutchZone Team</strong></p>
            <p>© ' . date("Y") . ' ClutchZone. All Rights Reserved.</p>
        </div>
    </div>';

    return sendMail($email, $subject, $body);
}


// Check if User is Banned
function isUserBanned($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT is_banned FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($is_banned);
    $stmt->fetch();
    $stmt->close();

    return $is_banned ? true : false;
}

?>
