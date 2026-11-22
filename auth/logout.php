<?php
session_start();
if (isset($_GET['confirm']) && $_GET['confirm'] === "yes") {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout | ClutchZone</title>
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Import Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Full-screen Modal Background */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            justify-content: center;
            align-items: center;
        }

        /* Modal Content */
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
            width: 350px;
        }

        /* Title */
        .modal-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        /* Buttons */
        .modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
            transition: 0.3s;
            font-size: 16px;
        }

        .btn-danger {
            background: #ff4d4d;
            color: white;
        }

        .btn-danger:hover {
            background: #cc0000;
        }

        .btn-secondary {
            background: #ddd;
            color: black;
        }

        .btn-secondary:hover {
            background: #bbb;
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logout Button */
        .logout-btn {
            display: block;
            margin: 100px auto;
            padding: 12px 25px;
            background: #ffcc00;
            color: black;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #e6b800;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .modal-content {
                width: 90%;
                padding: 20px;
            }
            .btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<!-- Logout Button -->
<button class="logout-btn" onclick="showLogoutModal()">Logout</button>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h3>Are you sure you want to logout?</h3>
        <p>Your session will be ended.</p>
        <div class="modal-buttons">
            <a href="logout.php?confirm=yes" class="btn btn-danger">Yes, Logout</a>
            <button onclick="closeLogoutModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

<!-- JavaScript for Modal -->
<script>
    // Show Logout Modal
    function showLogoutModal() {
        document.getElementById("logoutModal").style.display = "flex";
    }

    // Close Logout Modal
    function closeLogoutModal() {
        document.getElementById("logoutModal").style.display = "none";
    }
</script>

</body>
</html>
