<?php
include 'config.php';

// Generate new password hash for "password"
$newHash = password_hash('password', PASSWORD_DEFAULT);

// Update admin password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $newHash);
$stmt->execute();

// Update user password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'user'");
$stmt->bind_param("s", $newHash);
$stmt->execute();

echo "Passwords reset successfully!<br>";
echo "Username: admin | Password: password<br>";
echo "Username: user | Password: password<br>";
echo "<br><a href='login.php'>Go to Login</a>";

$stmt->close();
$conn->close();
?>
