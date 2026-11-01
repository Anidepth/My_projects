<?php
echo "<h2>🔍 Fish & Accessories Store - Setup Verification</h2>";
echo "<hr>";

// 1. Check PHP version
echo "<p><strong>1. PHP Version:</strong> " . phpversion() . " ✅</p>";

// 2. Check MySQL extension
echo "<p><strong>2. MySQL Extension:</strong> ";
echo extension_loaded('mysqli') ? "✅ Enabled" : "❌ Disabled";
echo "</p>";

// 3. Test database connection
echo "<p><strong>3. Database Connection:</strong> ";
$test_conn = new mysqli("localhost", "root", "", "fish_accessories_store");
if ($test_conn->connect_error) {
    echo "❌ Failed: " . $test_conn->connect_error;
} else {
    echo "✅ Connected";
    $test_conn->close();
}
echo "</p>";

// 4. Check files
echo "<p><strong>4. Required Files:</strong></p>";
$files = ['config.php', 'login.php', 'index.html', 'logout.php', 'user-dashboard.php', 'admin-dashboard.php'];
foreach ($files as $file) {
    $exists = file_exists($file) ? "✅" : "❌";
    echo "   - $file: $exists<br>";
}

// 5. Check database tables
echo "<p><strong>5. Database Tables:</strong></p>";
include 'config.php';
$tables = $conn->query("SHOW TABLES");
if ($tables) {
    while ($row = $tables->fetch_row()) {
        echo "   - " . $row[0] . " ✅<br>";
    }
} else {
    echo "   ❌ Could not retrieve tables<br>";
}

// 6. Check users in database
echo "<p><strong>6. Test Users:</strong></p>";
$result = $conn->query("SELECT username, role FROM users");
if ($result) {
    while ($user = $result->fetch_assoc()) {
        echo "   - " . $user['username'] . " (" . $user['role'] . ") ✅<br>";
    }
} else {
    echo "   ❌ No users found<br>";
}

echo "<hr>";
echo "<p style='color: green;'><strong>✅ Setup verification complete!</strong></p>";
echo "<p><a href='index.html'>← Back to Home</a></p>";
?>
