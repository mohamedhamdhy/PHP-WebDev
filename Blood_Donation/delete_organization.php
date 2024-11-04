<?php
// Your database connection
include('db.php');

// Check if the admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if an organization ID is provided
if (isset($_GET['id'])) {
    $organization_id = $_GET['id'];

    // Prepare a statement to delete the organization
    $delete_query = "DELETE FROM organization WHERE organization_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $organization_id);

    // Execute the statement
    if ($delete_stmt->execute()) {
        // Redirect to manage organizations page with a success message
        header("Location: manage_organizations.php?message=Organization deleted successfully");
        exit();
    } else {
        // Redirect to manage organizations page with an error message
        header("Location: manage_organizations.php?error=Failed to delete organization");
        exit();
    }
} else {
    // Redirect to manage organizations page if no ID is provided
    header("Location: manage_organizations.php");
    exit();
}
?>
