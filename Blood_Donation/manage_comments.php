<?php
// Include database connection
include 'db.php';

// Fetch comments along with campaign details and user/organization information
$sql = "SELECT cc.crowd_comments_id, cc.crowd_comments, cc.created_at, 
               c.title AS campaign_title, c.category, c.goal_amount, c.donate_amount, c.images,
               nu.normal_user_firstname, nu.normal_user_lastname, nu.normal_user_profile_picture,
               org.organization_name, org.organization_profile_picture
        FROM crowd_comments cc
        LEFT JOIN campaigns c ON cc.campaign_id = c.campaign_id
        LEFT JOIN normal_user nu ON cc.normal_user_id = nu.normal_user_id
        LEFT JOIN organization org ON cc.organization_id = org.organization_id
        ORDER BY cc.created_at DESC";

$result = $conn->query($sql);

// Check for delete request
if (isset($_GET['delete'])) {
    $comment_id = $_GET['delete'];
    $delete_sql = "DELETE FROM crowd_comments WHERE crowd_comments_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        header("Location: manage_comments.php");
        exit;
    } else {
        echo "Error deleting comment.";
    }
}

// Check for update request
if (isset($_POST['update_comment'])) {
    $comment_id = $_POST['comment_id'];
    $new_comment = $_POST['new_comment'];

    $update_sql = "UPDATE crowd_comments SET crowd_comments = ? WHERE crowd_comments_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_comment, $comment_id);
    if ($stmt->execute()) {
        header("Location: manage_comments.php");
        exit;
    } else {
        echo "Error updating comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .comments-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .comment-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 15px;
            width: 90%;
            transition: transform 0.2s;
        }

        .comment-card:hover {
            transform: scale(1.02);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .campaign-info {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            background-color: #fafafa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .campaign-info img {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            margin-right: 15px;
        }

        .campaign-details {
            flex-grow: 1;
        }

        .campaign-details h3 {
            margin: 0 0 10px 0;
        }

        .campaign-details p {
            margin: 5px 0;
        }

        .campaign-details .goal-progress {
            background-color: #eaeaea;
            border-radius: 4px;
            position: relative;
            height: 10px;
            margin-top: 5px;
            overflow: hidden;
        }

        .campaign-details .goal-progress .progress {
            background-color: #5cb85c;
            height: 100%;
            width: 0;
            border-radius: 4px;
        }

        .comment-actions {
            margin-top: 15px;
        }

        .update-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .update-form input[type="text"] {
            flex: 1;
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .update-form button {
            padding: 5px 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .update-form button:hover {
            background-color: #4cae4c;
        }

        .delete-button {
            color: red;
            margin-left: 10px;
            cursor: pointer;
        }

        .delete-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include("funding_admin_nav.php") ?>

<h1>Manage Comments</h1>

<div class="comments-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="comment-card">
                <!-- User or Organization Info -->
                <div class="user-info">
                    <?php if (!empty($row['normal_user_firstname'])): ?>
                        <!-- Display normal user info -->
                        <img src="<?php echo htmlspecialchars($row['normal_user_profile_picture']) ?: 'default-avatar.png'; ?>" alt="User Profile Picture">
                        <div>
                            <strong><?php echo htmlspecialchars($row['normal_user_firstname'] . ' ' . $row['normal_user_lastname']); ?></strong><br>
                            <small>Commented on: <?php echo htmlspecialchars($row['created_at']); ?></small>
                        </div>
                    <?php elseif (!empty($row['organization_name'])): ?>
                        <!-- Display organization info -->
                        <img src="<?php echo htmlspecialchars($row['organization_profile_picture']) ?: 'default-organization.png'; ?>" alt="Organization Profile Picture">
                        <div>
                            <strong><?php echo htmlspecialchars($row['organization_name']); ?></strong><br>
                            <small>Commented on: <?php echo htmlspecialchars($row['created_at']); ?></small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Campaign Info -->
                <div class="campaign-info">
                    <img src="<?php echo htmlspecialchars($row['images']) ?: 'default-campaign.jpg'; ?>" alt="Campaign Image">
                    <div class="campaign-details">
                        <h3><?php echo htmlspecialchars($row['campaign_title']); ?></h3>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                        <p><strong>Goal Amount:</strong> $<?php echo number_format($row['goal_amount'], 2); ?></p>
                        <p><strong>Donated Amount:</strong> $<?php echo number_format($row['donate_amount'], 2); ?></p>

                        <!-- Progress Bar for Donations -->
                        <div class="goal-progress">
                            <div class="progress" style="width: <?php echo ($row['donate_amount'] / $row['goal_amount']) * 100; ?>%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Comment Text -->
                <p><?php echo htmlspecialchars($row['crowd_comments']); ?></p>

                <!-- Update and Delete Actions -->
                <div class="comment-actions">
                    <form action="" method="post" class="update-form">
                        <input type="hidden" name="comment_id" value="<?php echo $row['crowd_comments_id']; ?>">
                        <input type="text" name="new_comment" value="<?php echo htmlspecialchars($row['crowd_comments']); ?>" required>
                        <button type="submit" name="update_comment">Update</button>
                        <a class="delete-button" href="?delete=<?php echo $row['crowd_comments_id']; ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
