<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

// Include the database connection
include 'db.php';

// Check if donor_id and status are set in the URL
if (!isset($_GET['donor_id']) || !isset($_GET['status'])) {
    die("Invalid request.");
}

// Sanitize inputs
$donor_id = intval($_GET['donor_id']);
$status = $_GET['status'] == 'confirm' ? 'confirm' : 'cancelled';

// Update query
$query = "UPDATE donors SET donation_req_status = ? WHERE donor_id = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("si", $status, $donor_id);
    
    if ($stmt->execute()) {
        // Redirect back to the manage donor requests page with a success message
        header("Location: manage_donor_requests.php?message=Request+Updated+Successfully");
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
