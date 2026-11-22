<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup | ClutchZone</title>
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Error Popup */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .popup-box {
            background: white;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }

        .popup-box h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: red;
        }

        .popup-box p {
            font-size: 16px;
            color: #333;
        }

        .popup-box .close-btn {
            background: red;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* Responsive */
        @media (max-width: 500px) {
            .popup-box {
                width: 80%;
            }
        }

        /* Popup Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

</head>
<body>

<!-- Error Popup -->
<div class="popup-overlay" id="errorPopup">
    <div class="popup-box">
        <h3>Error</h3>
        <p id="popupMessage"></p>
        <button class="close-btn" onclick="closePopup()">Close</button>
    </div>
</div>

<div class="container">
    <div class="form-box">
        <!-- Toggle Buttons -->
        <div class="button-box">
            <button type="button" class="toggle-btn active" onclick="toggleForm('login')">Login</button>
            <button type="button" class="toggle-btn" onclick="toggleForm('signup')">Signup</button>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="form active" method="POST" action="auth/login.php">
            <h2>Login</h2>
            <input type="email" id="login-email" name="email" placeholder="Email" required>

            <!-- Password with Eye Icon -->
            <div class="password-container">
                <input type="password" name="password" id="login-password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('login-password', 'login-eye')">
                    <i id="login-eye" class="fas fa-eye"></i>
                </span>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="option-field">
                <span class="remember-me">
                    <input type="checkbox" id="remember-me">
                    <label for="remember-me">Remember Me</label>
                </span>
                <a href="auth/forgot_password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Signup Form -->
        <form id="signup-form" class="form" method="POST" action="auth/register.php">
            <h2>Signup</h2>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" id="signup-email" name="email" placeholder="Email" required>

            <!-- Password with Eye Icon -->
            <div class="password-container">
                <input type="password" name="password" id="signup-password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('signup-password', 'signup-eye')">
                    <i id="signup-eye" class="fas fa-eye"></i>
                </span>
            </div>

            <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required>
            <button type="submit" class="btn">Signup</button>
        </form>

        <!-- Error Popup -->
        <div class="popup-overlay" id="errorPopup">
            <div class="popup-box">
                <h3>Error</h3>
                <p id="popupMessage"></p>
                <button class="close-btn" onclick="closePopup()">Close</button>
            </div>
        </div>
            
        <script>
            function showError(message) {
                document.getElementById("popupMessage").innerText = message;
                document.getElementById("errorPopup").style.display = "flex";
            }
        
            function closePopup() {
                document.getElementById("errorPopup").style.display = "none";
            }
        
            <?php if (isset($_SESSION['error_message'])): ?>
                showError("<?= $_SESSION['error_message']; ?>");
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        </script>

    </div>
</div>

<!-- JavaScript for Form Toggle, Password Visibility & Error Popup -->
<script>
    function toggleForm(formType) {
        if (formType === 'login') {
            document.getElementById('login-form').classList.add('active');
            document.getElementById('signup-form').classList.remove('active');
        } else {
            document.getElementById('signup-form').classList.add('active');
            document.getElementById('login-form').classList.remove('active');
        }
    }

    function togglePassword(inputId, eyeId) {
        let passwordField = document.getElementById(inputId);
        let eyeIcon = document.getElementById(eyeId);
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }

    // Show Error Popup
    function showError(message) {
        document.getElementById("popupMessage").innerText = message;
        document.getElementById("errorPopup").style.display = "flex";
    }

    // Close Error Popup
    function closePopup() {
        document.getElementById("errorPopup").style.display = "none";
    }

    // Remember Me Feature (Using LocalStorage)
    document.addEventListener("DOMContentLoaded", function () {
        const emailField = document.querySelector("#login-email");
        const passwordField = document.querySelector("#login-password");
        const rememberMe = document.querySelector("#remember-me");

        if (localStorage.getItem("rememberMe") === "true") {
            emailField.value = localStorage.getItem("savedEmail");
            passwordField.value = localStorage.getItem("savedPassword");
            rememberMe.checked = true;
        }

        document.querySelector("#login-form").addEventListener("submit", function () {
            if (rememberMe.checked) {
                localStorage.setItem("savedEmail", emailField.value);
                localStorage.setItem("savedPassword", passwordField.value);
                localStorage.setItem("rememberMe", "true");
            } else {
                localStorage.removeItem("savedEmail");
                localStorage.removeItem("savedPassword");
                localStorage.removeItem("rememberMe");
            }
        });
    });
</script>

</body>
</html>
