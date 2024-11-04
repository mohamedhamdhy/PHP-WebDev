<?php
session_start();
include('db.php'); // Include your database connection file

// Fetch the data from the POST request
$organization_id = $_POST['organization_id'];
$organization_name = $_POST['organization_name'];
$organization_registration_number = $_POST['organization_registration_number'];
$organization_address = $_POST['organization_address'];
$organization_phone = $_POST['organization_phone'];
$organization_code = $_POST['organization_code'];

$reason = $_POST['reason'];
$blood_type = $_POST['blood_type'];
$request_date = $_POST['request_date'];
$delivery = $_POST['delivery'];
$delivery_address = $_POST['delivery_address'];
$delivery_instructions = $_POST['delivery_instructions'];
$quantity = $_POST['quantity'];

// Insert the request into the database
$sql = "INSERT INTO blood_requests_org (organization_id, organization_name, organization_registration_number, organization_address, organization_phone, reason, blood_type, request_date, delivery, delivery_address, delivery_instructions, quantity, organization_code, action, delivery_status)
        VALUES ('$organization_id','$organization_name', '$organization_registration_number', '$organization_address', '$organization_phone', '$reason', '$blood_type', '$request_date', '$delivery', '$delivery_address', '$delivery_instructions', '$quantity', '$organization_code', 'Pending', 'Pending')";

if (mysqli_query($conn, $sql)) {  // Use $sql variable for the query
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

mysqli_close($conn);
?>
