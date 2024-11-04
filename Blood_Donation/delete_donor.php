<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

// Check if donor_id is provided in the URL
if (!isset($_GET['donor_id']) || empty($_GET['donor_id'])) {
    die("Invalid request.");
}

// Include the database connection
include 'db.php'; // Ensure this connects to your database

// Sanitize donor_id
$donor_id = intval($_GET['donor_id']); // Ensure it's an integer

// Prepare and execute the delete query
if ($stmt = $conn->prepare("DELETE FROM donors WHERE donor_id = ?")) {
    $stmt->bind_param("i", $donor_id); // Bind the donor_id as an integer

    if ($stmt->execute()) {
        // Redirect back to the donors list page after deletion
        header("Location: manage_donors.php?message=Donor+Deleted+Successfully");
        exit(); // Ensure the script stops after redirect
    } else {
        // If deletion fails, display an error
        echo "Error deleting donor: " . $conn->error;
    }

    $stmt->close(); // Close the prepared statement
} else {
    // If preparation of the statement fails, display an error
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
