<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>User Dashboard</title></head>
<body>
<h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<p>Your orders and cart will appear here.</p>
<a href="checkout.php">Go to Checkout</a><br>
<a href="logout.php">Logout</a>
</body>
</html>
