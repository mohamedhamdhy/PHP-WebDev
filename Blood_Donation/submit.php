<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO user_details (fullName, address, mobile1, mobile2, email, nic, year, month, day, gender, blood_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Update the bind_param to include blood group
$stmt->bind_param("ssssssiiiss", $fullName, $address, $mobile1, $mobile2, $email, $nic, $year, $month, $day, $gender, $bloodGroup);

// Set parameters and execute
$fullName = $_POST['editFullName'];
$address = $_POST['editAddress'];
$mobile1 = $_POST['editMobile1'];
$mobile2 = $_POST['editMobile2'];
$email = $_POST['editEmail'];
$nic = $_POST['nic'];
$bloodGroup = $_POST['bloodGroup']; // Get blood group from the form

// Extract NIC details
$nicDetails = $_POST['nicDetails'];
list($year, $month, $day, $gender) = explode('|', $nicDetails);

// Ensure gender is either 'Male' or 'Female'
$gender = strtolower(trim($gender)) === 'female' ? 'Female' : 'Male';

// Sanitize extracted details
$year = $conn->real_escape_string($year);
$month = (int)$month;
$day = (int)$day;
$gender = $conn->real_escape_string($gender);
$bloodGroup = $conn->real_escape_string($bloodGroup); // Sanitize blood group

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
