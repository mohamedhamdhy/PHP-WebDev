<?php
// Include the database connection
include 'db.php';

// Fetch all campaigns with additional images and videos
$sql_campaigns = "SELECT c.*, GROUP_CONCAT(DISTINCT ca.image_url) AS images, GROUP_CONCAT(DISTINCT cv.video_url) AS videos 
                  FROM campaigns c
                  LEFT JOIN campaign_additional_images ca ON c.campaign_id = ca.campaign_id 
                  LEFT JOIN campaign_videos cv ON c.campaign_id = cv.campaign_id 
                  GROUP BY c.campaign_id";
$stmt_campaigns = $conn->prepare($sql_campaigns);
$stmt_campaigns->execute();
$result_campaigns = $stmt_campaigns->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Campaigns</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional CSS for styling -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <?php include("funding_admin_nav.php") ?>

<h1>Manage Campaigns</h1>

<table border="1">
    <thead>
        <tr>
            <th>Campaign ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Goal Amount</th>
            <th>Donated Amount</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Images</th>
            <th>Videos</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($campaign = $result_campaigns->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($campaign['campaign_id']); ?></td>
                <td>
                <a href="fetch_campaign_analysis.php?campaign_id=<?php echo htmlspecialchars($campaign['campaign_id']); ?>">
        <?php echo htmlspecialchars($campaign['title']); ?>
    </a>
</td>

                <td><?php echo htmlspecialchars($campaign['category']); ?></td>
                <td><?php echo htmlspecialchars(number_format($campaign['goal_amount'], 2)); ?></td>
                <td><?php echo htmlspecialchars(number_format($campaign['donate_amount'], 2)); ?></td>
                <td><?php echo htmlspecialchars($campaign['duration']); ?></td>
                <td>
                    <form method="POST" action="">
                        <select name="campaign_status">
                            <option value="pending" <?php if ($campaign['campaign_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if ($campaign['campaign_status'] == 'approved') echo 'selected'; ?>>Approved</option>
                            <option value="cancelled" <?php if ($campaign['campaign_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <input type="hidden" name="campaign_id" value="<?php echo htmlspecialchars($campaign['campaign_id']); ?>">
                        <input type="submit" name="update_status" value="Update Status">
                    </form>
                </td>
                <td>
                    <?php if ($campaign['images']): ?>
                        <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Campaign Images" style="max-width: 100px; height: auto;">
                    <?php else: ?>
                        No images
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($campaign['videos']): ?>
                        <a href="<?php echo htmlspecialchars($campaign['videos']); ?>" target="_blank">View Videos</a>
                    <?php else: ?>
                        No videos
                    <?php endif; ?>
                </td>
                <td>
                <form method="POST" action="delete_campaigns.php?campaign_id=<?php echo htmlspecialchars($campaign['campaign_id']); ?>" style="display:inline;">
    <input type="hidden" name="campaign_id" value="<?php echo htmlspecialchars($campaign['campaign_id']); ?>">
    <input type="submit" name="delete_campaign" value="Delete" onclick="return confirm('Are you sure you want to delete this campaign?');">
</form>




                    <form method="GET" action="edit_campaigns.php" style="display:inline;">
                        <input type="hidden" name="campaign_id" value="<?php echo htmlspecialchars($campaign['campaign_id']); ?>">
                        <input type="submit" value="Edit">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Div to display campaign details -->
<div id="campaign-details" style="display:none; border: 1px solid #ccc; padding: 20px; margin-top: 20px;">
    <!-- Campaign analysis will be displayed here -->
</div>



<?php
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $campaign_id = $_POST['campaign_id'];
    $campaign_status = $_POST['campaign_status'];

    $sql_update = "UPDATE campaigns SET campaign_status = ? WHERE campaign_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $campaign_status, $campaign_id);
    $stmt_update->execute();

    // Redirect back to manage_campaigns.php
    header("Location: manage_campaigns.php");
    exit;
}

// Handle campaign deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_campaign'])) {
    $campaign_id = $_POST['campaign_id'];

    $sql_delete = "DELETE FROM campaigns WHERE campaign_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $campaign_id);
    $stmt_delete->execute();

    // Redirect back to manage_campaigns.php
    header("Location: manage_campaigns.php");
    exit;
}
?>
<script>
function showCampaignDetails(campaignId) {
    // Create an XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    
    // Define the type of request and the URL
    xhr.open('GET', 'fetch_campaign_analysis.php?campaign_id=' + campaignId, true);
    
    // Set up the callback to handle the response
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Get the campaign details div and populate it with the response
            var campaignDetailsDiv = document.getElementById('campaign-details');
            campaignDetailsDiv.innerHTML = xhr.responseText;
            campaignDetailsDiv.style.display = 'block'; // Show the div
        } else {
            alert('Error fetching campaign details: ' + xhr.status);
        }
    };
    
    // Send the request
    xhr.send();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
