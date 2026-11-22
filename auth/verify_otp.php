<?php
session_start();
include '../includes/db_connect.php';

// Redirect if no email session exists
if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];

// Check if the user is already verified
$stmt = $conn->prepare("SELECT is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($is_verified);
$stmt->fetch();
$stmt->close();

// If already verified, redirect with a session message
if ($is_verified == 1) {
    $_SESSION['alert_message'] = "Your account is already verified!";
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | ClutchZone</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>

    <style>
        body {
            background: linear-gradient(120deg, #eef2f3, #f5f5f5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .otp-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 450px;
            animation: fadeIn 1s ease-in-out;
            backdrop-filter: blur(10px);
        }

        h2 { color: #333; font-weight: 600; }

        .otp-box p { font-size: 16px; color: #555; }

        .otp-input {
            width: 45px;
            height: 50px;
            margin: 5px;
            font-size: 24px;
            text-align: center;
            border-radius: 8px;
            border: 2px solid #ccc;
            outline: none;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .otp-input:focus { border-color: #ffcc00; transform: scale(1.1); }

        .btn-primary {
            background-color: #ffcc00;
            border: none;
            font-weight: bold;
            margin-top: 15px;
        }

        .btn-primary:hover { background-color: #e6b800; }

        .btn-link {
            color: #ffcc00;
            text-decoration: none;
        }

        .btn-link:hover { color: #e6b800; text-decoration: underline; }

        .alert {
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 15px;
            display: none;
        }

        .alert.success { background-color: #d4edda; color: #155724; }

        .alert.error { background-color: #f8d7da; color: #721c24; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            .otp-container { width: 90%; padding: 20px; }
            .otp-input { width: 40px; height: 45px; font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Email Verification</h2>
    <p>We have sent a <strong>6-digit OTP</strong> to <strong><?= htmlspecialchars($email) ?></strong></p>

    <div id="successMessage" class="alert success"></div>
    <div id="errorMessage" class="alert error"></div>

    <form id="otpForm">
        <div class="d-flex justify-content-center">
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
        </div>
        <input type="hidden" name="otp" id="fullOtp">

        <small class="text-muted">Request OTP in <span id="timer">1:00</span></small>
        <button type="submit" class="btn btn-primary mt-3">Verify OTP</button>
        <button type="button" id="resendOtp" class="btn btn-link mt-2" disabled>Resend OTP</button>
    </form>
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

    $("#otpForm").submit(function (e) {
        e.preventDefault();
        let fullOtp = "";
        $(".otp-input").each(function () { fullOtp += $(this).val(); });

        if (fullOtp.length !== 6) {
            $("#errorMessage").text("Please enter a 6-digit OTP.").show();
            return;
        }

        $("#fullOtp").val(fullOtp);

        $.post("verify_otp_action.php", { otp: fullOtp }, function (response) {
            let data = JSON.parse(response);
            if (data.status === "success") {
                $("#successMessage").text(data.message).show();
                setTimeout(() => { window.location.href = data.redirect; }, 2000);
            } else {
                $("#errorMessage").text(data.message).show();
            }
        });
    });

    $("#resendOtp").click(function () {
        $("#successMessage").text("").hide();
        $("#errorMessage").text("").hide();

        $.post("resend_otp.php", function (response) {
            let data = JSON.parse(response);
            if (data.status === "success") {
                $("#successMessage").text("New OTP sent! Check your email.").show();
                resendBtn.setAttribute("disabled", true);
                countdown = 60;
                timer = setInterval(updateTimer, 1000);
            } else {
                $("#errorMessage").text(data.message).show();
            }
        });
    });

    $(".otp-input").on("input", function () {
        let currentInput = $(this);
        let nextInput = currentInput.next(".otp-input");

        if (currentInput.val().length === 1 && nextInput.length) {
            nextInput.focus();
        }
    });

    $(".otp-input").on("keydown", function (e) {
        let currentInput = $(this);
        let prevInput = currentInput.prev(".otp-input");

        if (e.key === "Backspace" && currentInput.val() === "" && prevInput.length) {
            prevInput.focus();
        }
    });
</script>

</body>
</html>
