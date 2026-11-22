<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['reset_email'])) {
    $_SESSION['error_message'] = "Session expired. Please request a new OTP.";
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    // Fetch OTP from Database
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_otp, $otp_expiry);
    $stmt->fetch();
    $stmt->close();

    // Check if OTP exists
    if (!$stored_otp) {
        $_SESSION['error_message'] = "Invalid request. Please try again.";
        header("Location: verify_reset_otp.php");
        exit();
    }

    // Check OTP Expiry
    if (strtotime($otp_expiry) < time()) {
        $_SESSION['error_message'] = "OTP expired. Request a new one.";
        header("Location: forgot_password.php");
        exit();
    }

    // ✅ **Directly Compare OTP Since It’s Not Hashed**
    if ($entered_otp !== $stored_otp) {
        $_SESSION['error_message'] = "Incorrect OTP. Try again.";
        header("Location: verify_reset_otp.php");
        exit();
    }

    // OTP Verified - Allow password reset
    $_SESSION['reset_verified'] = true;
    $_SESSION['success_message'] = "OTP Verified! Now set your new password.";

    // Redirect to Reset Password Page
    header("Location: reset_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | ClutchZone</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }   

    body {
        background: linear-gradient(120deg, #eef2f3, #f5f5f5);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        padding: 20px;
    }   

    /* Container */
    .container {
        position: relative;
        width: 400px;
        background: rgba(255, 255, 255, 0.8);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        text-align: center;
    }   

    /* Form Box */
    .form-box {
        width: 100%;
        padding: 20px;
    }   

    /* Title */
    h2 {
        color: #333;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 15px;
    }   

    /* OTP Input */
    .otp-input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: none;
        outline: none;
        background: rgba(0, 0, 0, 0.1);
        color: #333;
        border-radius: 5px;
        font-size: 16px;
        letter-spacing: 5px;
        text-align: center;
    }   

    .otp-input:focus {
        background: rgba(0, 0, 0, 0.2);
        transform: scale(1.03);
    }   

    /* Buttons */
    button {
        width: 100%;
        padding: 12px;
        background: #ffcc00;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        transition: all 0.3s ease-in-out;
    }   

    button:hover {
        transform: scale(1.05);
    }   

    /* Resend OTP */
    .btn-link {
        background: none;
        border: none;
        color: #ffcc00;
        cursor: pointer;
        text-decoration: underline;
    }   

    .btn-link:hover {
        color: red;
    }   

    /* Alerts */
    .alert {
        padding: 10px;
        border-radius: 5px;
        font-size: 14px;
        margin-bottom: 15px;
    }   

    .alert.success {
        background-color: #d4edda;
        color: #155724;
    }   

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
    }   

    .otp-expired-message {
        font-size: 14px;
        color: #555;
        margin-top: 15px;
        text-align: center;
    }

    .otp-expired-message a {
        color: #ffcc00;
        font-weight: bold;
        text-decoration: none;
        transition: color 0.3s ease-in-out;
    }

    .otp-expired-message a:hover {
        color: #e6b800;
        text-decoration: underline;
    }


    /* Responsive */
    @media (max-width: 500px) {
        .container {
            width: 90%;
            padding: 15px;
        }
    }
    </style>
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2>Verify OTP</h2>
        <p>Enter the <strong>6-digit OTP</strong> sent to <strong><?= htmlspecialchars($email) ?></strong></p>

        <!-- ✅ Success & Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error"><?= $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST">
            <input type="tel" name="otp" maxlength="6" minlength="6" class="otp-input" placeholder="Enter OTP" required>
            <small>Request OTP in <span id="timer">1:00</span></small>
            <button type="submit">Verify OTP</button>
            <p class="otp-expired-message">
                If OTP expires, go back to 
                <a href="forgot_password.php">Forgot Password</a> 
                and request a new OTP.
            </p>

        </form>
    </div>
</div>

<script>
    let countdown = 60;
    let timerDisplay = document.getElementById("timer");
    let resendBtn = document.getElementById("resendOtp");

    function updateTimer() {
        let minutes = Math.floor(countdown / 60);
        let seconds = countdown % 60;
        timerDisplay.innerText = `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
        countdown--;

        if (countdown < 0) {
            clearInterval(timer);
            resendBtn.removeAttribute("disabled");
            timerDisplay.innerText = "00:00";
        }
    }
    let timer = setInterval(updateTimer, 1000);
</script>

</body>
</html>
