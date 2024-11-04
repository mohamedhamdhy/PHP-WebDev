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

// Fetch new fields from POST request
$org_blood_district = $_POST['org_blood_district']; // District selection

    // Handle image upload
    $blood_image = ""; // Initialize blood_image variable

    if (isset($_FILES['blood_request_image']) && $_FILES['blood_request_image']['error'] == 0) {
        $target_dir = "uploads/";  // Ensure this directory exists
        $blood_image = basename($_FILES["blood_request_image"]["name"]);
        $target_file = $target_dir . $blood_image;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES["blood_request_image"]["tmp_name"], $target_file)) {
            $blood_image = $target_file;  // Store file path
        } else {
            echo "<script>alert('Error uploading image.');</script>";
        }
    }

// Insert the request into the database
$sql = "INSERT INTO blood_requests_org (organization_id, organization_name, organization_registration_number, organization_address, organization_phone, reason, blood_type, request_date, delivery, delivery_address, delivery_instructions, quantity, organization_code, action, delivery_status, org_blood_district, org_blood_image)
        VALUES ('$organization_id', '$organization_name', '$organization_registration_number', '$organization_address', '$organization_phone', '$reason', '$blood_type', '$request_date', '$delivery', '$delivery_address', '$delivery_instructions', '$quantity', '$organization_code', 'Pending', 'Pending', '$org_blood_district', '$blood_image')";

// Execute the query
if (mysqli_query($conn, $sql)) {
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

// Close the database connection
mysqli_close($conn);
?>
