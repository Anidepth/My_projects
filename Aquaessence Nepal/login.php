<?php
session_start();
include 'config.php';

$error = '';
$role = $_POST['role'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ($user['role'] === 'admin' ? "admin-dashboard.php" : "user-dashboard.php"));
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Fish & Accessories Shop</title></head>
<body>
<h2>Login</h2>
<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
<form method="POST">
  <input type="text" name="username" placeholder="Username" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <select name="role">
    <option value="user">Customer</option>
    <option value="admin">Administrator</option>
  </select><br>
  <button type="submit">Login</button>
</form>
</body>
</html>
