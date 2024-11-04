<?php
session_start();
require_once 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    die("You need to log in to manage campaigns.");
}

// Retrieve user details from session
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Fetch the campaigns based on user type
if ($user_type === 'user') {
    // Prepare the statement to select approved campaigns for the user
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE normal_user_id = ? AND campaign_status = 'approved'");
    $stmt->bind_param("i", $user_id);
} elseif ($user_type === 'organization') {
    // Prepare the statement to select approved campaigns for the organization
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE organization_id = ? AND campaign_status = 'approved'");
    $stmt->bind_param("i", $user_id);
} else {
    die("Invalid user type.");
}


// Execute the query and fetch the campaigns
$stmt->execute();
$campaigns_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Campaigns</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card img {
            height: 200px; 
            object-fit: cover; 
            width: 100%;
        }
        .card-body {
            text-align: left;
        }
        .card-title {
            font-size: 1.25rem; 
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include("nav.php") ?>
    <div class="container mt-4">
        <h2 class="mb-4">Your Campaigns</h2>

        <?php if ($campaigns_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($campaign = $campaigns_result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <!-- Display campaign image -->
                            <?php if (!empty($campaign['images'])): ?>
                                <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Campaign Image">
                            <?php else: ?>
                                <img src="placeholder.jpg" class="card-img-top" alt="No Image Available">
                            <?php endif; ?>

                            <div class="card-body">
                                <!-- Display campaign details -->
                                <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($campaign['category']); ?></p>
                                <p><strong>Goal Amount:</strong> $<?php echo number_format($campaign['goal_amount'], 2); ?></p>
                                <p><strong>Donated Amount:</strong> $<?php echo number_format($campaign['donate_amount'], 2); ?></p>
                                <p><strong>Duration:</strong> <?php echo htmlspecialchars($campaign['duration']); ?> days</p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($campaign['district']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($campaign['description']); ?></p>
                                <p><strong>Created At:</strong> <?php echo htmlspecialchars($campaign['created_at']); ?></p>
                            </div>

                            <div class="card-footer">
                                <!-- Edit Button -->
                                <a href="edit_campaign.php?campaign_id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-primary btn-block">Edit/Update</a>
                                <!-- View Analysis Button -->
                                <a href="view_analysis.php?campaign_id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-success btn-block">View Analysis</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Your Campaign Didnt Confirm by Admin Please wait for it.
            </div>
        <?php endif; ?>
    </div>

    <!-- Optional: Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
