<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | ClutchZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            transition: all 0.5s ease-in-out;
        }

        .container {
            width: 400px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease-in-out;
            position: relative;
        }

        .container:hover {
            transform: scale(1.02);
        }

        h2 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .input-box {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            outline: none;
            background: #f9f9f9;
            color: #333;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus {
            border-color: #ffcc00;
            box-shadow: 0 0 5px rgba(255, 204, 0, 0.5);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }

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

        .alert {
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 15px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert.success {
            background-color: #28a745;
            color: #fff;
        }

        .alert.error {
            background-color: #dc3545;
            color: #fff;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

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
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All fields are required!";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error_message'] = "Password must be at least 6 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            $_SESSION['success_message'] = "Password reset successfully! Please log in.";
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating password. Try again.";
        }
    }
}
?>

<div class="container">
    <i class="fas fa-lock" style="font-size: 40px; color: #ffcc00; margin-bottom: 10px;"></i>
    <h2>Reset Password</h2>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-box">
            <input type="password" name="password" placeholder="New Password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePassword(this, 'password')"></i>
        </div>
        <div class="input-box">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePassword(this, 'confirm_password')"></i>
        </div>
        <button type="submit">Reset Password</button>
    </form>
</div>

<script>
function togglePassword(element, fieldName) {
    let inputField = document.getElementsByName(fieldName)[0];
    if (inputField.type === "password") {
        inputField.type = "text";
        element.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        inputField.type = "password";
        element.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>
