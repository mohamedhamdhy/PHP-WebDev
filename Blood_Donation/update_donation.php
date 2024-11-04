<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Get the donor ID and blood type from the URL
$donor_id = intval($_GET['donor_id']);
$blood_type = mysqli_real_escape_string($conn, $_GET['blood_type']); // Ensure safe input

// Update the last donation date to the current date and set donate_update to 'donated'
$current_date = date('Y-m-d H:i:s');
$update_donor_query = "UPDATE donors SET last_donation_date = '$current_date', donate_update = 'donated' WHERE donor_id = $donor_id";

if ($conn->query($update_donor_query) === TRUE) {
    // Determine the blood type column name
    switch ($blood_type) {
        case 'A+':
            $blood_type_column = 'A_plus';
            break;
        case 'A-':
            $blood_type_column = 'A_minus';
            break;
        case 'B+':
            $blood_type_column = 'B_plus';
            break;
        case 'B-':
            $blood_type_column = 'B_minus';
            break;
        case 'AB+':
            $blood_type_column = 'AB_plus';
            break;
        case 'AB-':
            $blood_type_column = 'AB_minus';
            break;
        case 'O+':
            $blood_type_column = 'O_plus';
            break;
        case 'O-':
            $blood_type_column = 'O_minus';
            break;
        default:
            die("Invalid blood type provided.");
    }

    // Update the blood inventory
    $update_inventory_query = "UPDATE blood_availability SET $blood_type_column = $blood_type_column + 1 WHERE hospital_id = " . intval($_SESSION['user_id']); // Use intval for security

    if ($conn->query($update_inventory_query) === TRUE) {
        echo "Donation status updated successfully!";
    } else {
        echo "Error updating inventory: " . $conn->error;
    }
} else {
    echo "Error updating donor: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
