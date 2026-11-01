<?php
include 'config.php';

$newHash = password_hash('password', PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $newHash);
$stmt->execute();

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'user'");
$stmt->bind_param("s", $newHash);
$stmt->execute();

echo "Passwords reset successfully!<br>";
echo "Admin: admin / password<br>";
echo "User: user / password<br>";
echo "<a href='login.php'>Go to Login</a>";

$stmt->close();
$conn->close();
?>
