<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format!";
        header("Location: forgot_password.php");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $_SESSION['error_message'] = "Email not found!";
        header("Location: forgot_password.php");
        exit();
    }
    $stmt->close();

    // Generate & Store OTP
    $otp = generateOTP();
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $otp, $otp_expiry, $email);
    $stmt->execute();
    $stmt->close();

    // Send OTP via Email
    sendOTP($email, $otp);

    // Store Email in Session
    $_SESSION['reset_email'] = $email;
    $_SESSION['success_message'] = "OTP sent! Check your email.";

    // Redirect to OTP Verification Page
    header("Location: verify_reset_otp.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | ClutchZone</title>
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

        /* Input Fields */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            outline: none;
            background: rgba(0, 0, 0, 0.1);
            color: #333;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus {
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
            background: #e6b800;
            transform: scale(1.05);
        }

        /* Alert Messages */
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

        /* Responsive */
        @media (max-width: 500px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            h2 {
                font-size: 20px;
            }

            input {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2>Forgot Password</h2>
        <p>Enter your email to reset your password.</p>

        <!-- ✅ Success Message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- ❌ Error Message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error"><?= $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send OTP</button>
        </form>
    </div>
</div>

</body>
</html>
