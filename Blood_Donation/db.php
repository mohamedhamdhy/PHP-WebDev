<?php
// Database credentials
$servername = "localhost";  // The hostname of the database server
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (leave empty if none)
$dbname = "lifebridge";     // The name of your database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");

// You can now use $conn to interact with your database

?>
