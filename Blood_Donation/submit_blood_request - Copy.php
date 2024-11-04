<?php
session_start(); // Start the session

// Include database connection
include('db.php');

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User ID is missing. Please log in again.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id']; // Retrieve user_id from session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $blood_type = $_POST['blood_type'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $reason = $_POST['reason'];
    $request_date = $_POST['request_date'];
    $quantity = $_POST['quantity'];
    $delivery_address = $_POST['delivery_address'];
    $delivery_instructions = $_POST['delivery_instructions'];
    $last_donated = $_POST['last_donated'];
    $weight = $_POST['weight'];
    $blood_pressure = $_POST['blood_pressure'];
    $medical_issues = $_POST['medical_issues'];
    $nic = $_POST['nic'];

    // Insert into the blood request table
    $query = "INSERT INTO blood_requests (user_id, name, age, blood_type, location, contact, reason, request_date, quantity, delivery_address, delivery_instructions, last_donated, weight, blood_pressure, medical_issues, nic)
              VALUES ('$user_id', '$name', '$age', '$blood_type', '$location', '$contact', '$reason', '$request_date', '$quantity', '$delivery_address', '$delivery_instructions', '$last_donated', '$weight', '$blood_pressure', '$medical_issues', '$nic')";

    if (mysqli_query($conn, $query)) {
        // Successfully saved data, display success message
        echo "<script>
                alert('Blood request submitted successfully!');
                 window.location.href = 'index.php'; // Uncomment to redirect
              </script>";
    } else {
        // Failed to save data, display error message
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
              </script>";
    }
}
?>
