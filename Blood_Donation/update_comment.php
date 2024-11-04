<?php
// Include the database connection
include 'db.php';

// Check if the request method is POST and if the required fields are present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_POST['crowd_comments'])) {
    // Get the posted values
    $comment_id = $_POST['comment_id'];
    $updated_comment = $_POST['crowd_comments'];

    // Prepare the SQL update statement
    $sql = "UPDATE crowd_comments SET crowd_comments = ?, created_at = NOW() WHERE crowd_comments_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters to prevent SQL injection
    $stmt->bind_param('si', $updated_comment, $comment_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Return a success response
        echo "Comment updated successfully!";
    } else {
        // Return an error response if something went wrong
        http_response_code(500);
        echo "Error updating comment.";
    }

    // Close the statement
    $stmt->close();
} else {
    // Return an error response if the required fields are not present
    http_response_code(400);
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
