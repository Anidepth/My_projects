<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fish_accessories_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF8
$conn->set_charset("utf8");
?>
