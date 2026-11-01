<?php
session_start();
include 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?role=admin");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);

// Get statistics
$result = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE role='user'");
$user_count = $result->fetch_assoc()['total_users'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fish & Accessories Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        header {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
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
            color: #1565c0;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-box {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box h3 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 14px;
            opacity: 0.9;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #1565c0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0d47a1;
        }
        .logout-btn {
            background: #d32f2f;
        }
        .logout-btn:hover {
            background: #b71c1c;
        }
        .admin-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1565c0;
            margin-bottom: 20px;
        }
        .admin-info p {
            color: #666;
        }
        .welcome-message {
            color: #666;
            font-size: 18px;
            line-height: 1.8;
        }
        .link-list {
            margin-top: 15px;
            margin-left: 20px;
        }
        .link-list li {
            margin: 8px 0;
        }
        .link-list a {
            color: #1565c0;
            text-decoration: none;
        }
        .link-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>🔐 Fish & Accessories Store - Admin Dashboard</h1>
        <div>
            <span style="margin-right: 20px;">Welcome, <?= $username ?>!</span>
            <a href="logout.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 4px;">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-card">
            <h2>Welcome to Admin Dashboard! 👑</h2>
            <div class="admin-info">
                <p><strong>Username:</strong> <?= $username ?></p>
                <p><strong>Role:</strong> Administrator</p>
                <p><strong>Login Status:</strong> ✅ Logged in successfully</p>
            </div>
            <p class="welcome-message">
                You have administrator access to Fish & Accessories Store. Manage users, products, orders, and system settings from this dashboard.
            </p>
            <a href="index.html" class="btn">Back to Home</a>
        </div>

        <div class="dashboard-card">
            <h2>📊 System Statistics</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <h3><?= $user_count ?></h3>
                    <p>Total Customers</p>
                </div>
                <div class="stat-box">
                    <h3>✅</h3>
                    <p>System Status: Healthy</p>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <h2>⚙️ Admin Functions</h2>
            <p>Manage system resources:</p>
            <ul class="link-list">
                <li><a href="install.php">Database & System Setup</a></li>
                <li><a href="reset_password.php">Change Admin Password</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
