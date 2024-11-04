<?php
session_start();

// Include your database connection
include 'db.php'; // Adjust the path as needed

// Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

// Get the comment ID from the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

    // Validate input
    if ($comment_id > 0) {
        // Prepare the SQL statement to delete the comment
        $delete_query = $conn->prepare("DELETE FROM crowd_comments WHERE crowd_comments_id = ?");
        $delete_query->bind_param('i', $comment_id);

        // Execute the query
        if ($delete_query->execute()) {
            // Check if any rows were affected
            if ($delete_query->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Comment deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Comment not found or already deleted.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to delete comment.']);
        }

        // Close the statement
        $delete_query->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid comment ID.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
