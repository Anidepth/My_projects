<?php
$servername = "localhost";
$username = "root";
$password = "";

// Connect without database first
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS fish_accessories_store");
$conn->select_db("fish_accessories_store");

// Create users table
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create products table
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert admin user (password: admin123)
$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$uname = 'admin';
$email = 'admin@fishaccessories.com';
$role = 'admin';
$stmt->bind_param("ssss", $uname, $email, $adminPass, $role);
$stmt->execute();

// Insert regular user (password: user123)
$userPass = password_hash('user123', PASSWORD_DEFAULT);
$uname = 'user';
$email = 'user@fishaccessories.com';
$role = 'user';
$stmt->bind_param("ssss", $uname, $email, $userPass, $role);
$stmt->execute();

echo "<h2>✅ Database Setup Complete!</h2>";
echo "<p><strong>Login credentials:</strong></p>";
echo "<ul>";
echo "<li>Admin: username = <code>admin</code>, password = <code>admin123</code></li>";
echo "<li>User: username = <code>user</code>, password = <code>user123</code></li>";
echo "</ul>";
echo "<p><a href='login.php'>👉 Go to Login Page</a></p>";

$conn->close();
?>
