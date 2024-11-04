<?php
// Include the database connection
include 'db.php';

// Check if the campaign_id is present in the URL
if (isset($_GET['campaign_id'])) {
    $campaign_id = $_GET['campaign_id'];

    // Prepare the SQL delete statement
    $sql_delete = "DELETE FROM campaigns WHERE campaign_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);

    if ($stmt_delete === false) {
        // Error preparing the statement
        die("Error preparing statement: " . htmlspecialchars($conn->error));
    }

    // Bind the campaign_id parameter
    $stmt_delete->bind_param("i", $campaign_id);

    // Execute the delete statement
    if ($stmt_delete->execute()) {
        // Check if any rows were affected (deleted)
        if ($stmt_delete->affected_rows > 0) {
            // Redirect back to manage_campaigns.php after successful deletion
            header("Location: manage_campaigns.php?message=Campaign+deleted+successfully");
            exit;
        } else {
            // No rows affected (campaign not found or already deleted)
            header("Location: manage_campaigns.php?error=Campaign+not+found+or+already+deleted");
            exit;
        }
    } else {
        // Redirect back with an error message if execution fails
        header("Location: manage_campaigns.php?error=Error+deleting+campaign: " . urlencode($stmt_delete->error));
        exit;
    }
} else {
    // Redirect back to manage_campaigns.php if campaign_id is not set
    header("Location: manage_campaigns.php");
    exit;
}
