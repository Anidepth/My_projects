<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php?role=user");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fish & Accessories Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .dashboard-card h2 {
            color: #1e40af;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .welcome-message {
            color: #666;
            font-size: 18px;
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #1e40af;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1e3a8a;
        }
        .logout-btn {
            background: #d32f2f;
        }
        .logout-btn:hover {
            background: #b71c1c;
        }
        .user-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1e40af;
            margin-bottom: 20px;
        }
        .user-info p {
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <h1>🐠 Fish & Accessories Store - Customer Dashboard</h1>
        <div>
            <span style="margin-right: 20px;">Welcome, <?= $username ?>!</span>
            <a href="logout.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 4px;">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-card">
            <h2>Welcome to Your Dashboard! 👋</h2>
            <div class="user-info">
                <p><strong>Username:</strong> <?= $username ?></p>
                <p><strong>Role:</strong> Customer</p>
                <p><strong>Login Status:</strong> ✅ Logged in successfully</p>
            </div>
            <p class="welcome-message">
                You have successfully logged into Fish & Accessories Store. You can now browse and purchase quality fish and accessories from our store.
            </p>
            <a href="index.html" class="btn">Back to Home</a>
            <a href="browse.php" class="btn" style="background: #4CAF50; margin-left: 10px;">Browse Products</a>
        </div>

        <div class="dashboard-card">
            <h2>📋 Quick Links</h2>
            <p>Access your account features:</p>
            <ul style="margin-top: 15px; margin-left: 20px;">
                <li><a href="browse.php" style="color: #1e40af; text-decoration: none;">Browse Products</a></li>
                <li><a href="reset_password.php" style="color: #1e40af; text-decoration: none;">Change Password</a></li>
                <li><a href="logout.php" style="color: #1e40af; text-decoration: none;">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
