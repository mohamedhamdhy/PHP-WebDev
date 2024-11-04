<?php
session_start();
require 'db.php'; // Include your database connection

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    // Redirect to login page if not logged in
    echo json_encode(['error' => 'User not authenticated.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = $_POST['campaign_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
    $user_type = $_SESSION['user_type']; // Assuming user type is stored in session

    // Determine which ID to use based on user type
    if ($user_type === 'user') {
        $normal_user_id = $user_id; // Set normal user ID
        $organization_id = NULL; // Set organization ID to NULL
    } elseif ($user_type === 'organization') {
        $normal_user_id = NULL; // Set normal user ID to NULL
        $organization_id = $user_id; // Set organization ID
    } else {
        echo json_encode(['error' => 'Invalid user type.']);
        exit();
    }

    // Insert comment into the database
    $stmt = $conn->prepare("INSERT INTO crowd_comments (campaign_id, normal_user_id, organization_id, crowd_comments, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $campaign_id, $normal_user_id, $organization_id, $comment);

    if ($stmt->execute()) {
        // Fetch the newly created comment for display
        $comment_id = $stmt->insert_id; // Get the last inserted comment ID
        $stmt = $conn->prepare("SELECT * FROM crowd_comments WHERE crowd_comments_id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $new_comment = $result->fetch_assoc();

        // Prepare data for response
        $response = [
            'commenter_name' => htmlspecialchars($new_comment['crowd_comments']), // Change this if you have a commenter name
            'crowd_comments' => nl2br(htmlspecialchars($new_comment['crowd_comments'])),
            'created_at' => date("F j, Y", strtotime($new_comment['created_at']))
        ];

        echo json_encode($response); // Return the new comment data as JSON
    } else {
        echo json_encode(['error' => 'Failed to add comment.']);
    }
}
?>
