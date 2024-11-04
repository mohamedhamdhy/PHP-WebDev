<?php
// Your database connection
include('db.php');

// Check if the admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle organization search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch organizations based on search query
$query = "SELECT * FROM organization 
          WHERE organization_name LIKE ? 
          OR organization_email LIKE ? 
          OR organization_registration_number LIKE ? 
          OR organization_address LIKE ?";
$stmt = $conn->prepare($query);
$search_param = "%$search_query%";
$stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    echo "SQL Error: " . $conn->error;
    exit();
}

// Fetch organization history if specific organization is selected
$organization_id = null;
$donations_result = $comments_result = $campaigns_result = null;

if (isset($_GET['organization_id'])) {
    $organization_id = $_GET['organization_id'];
    
    // Fetch donation history
    $donations_query = "SELECT * FROM donations WHERE organization_id = ?";
    $donations_stmt = $conn->prepare($donations_query);
    $donations_stmt->bind_param("i", $organization_id);
    $donations_stmt->execute();
    $donations_result = $donations_stmt->get_result();

    // Fetch comment history
    $comments_query = "SELECT * FROM crowd_comments WHERE organization_id = ?";
    $comments_stmt = $conn->prepare($comments_query);
    $comments_stmt->bind_param("i", $organization_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();

    // Fetch campaign history
    $campaigns_query = "SELECT * FROM campaigns WHERE organization_id = ?";
    $campaigns_stmt = $conn->prepare($campaigns_query);
    $campaigns_stmt->bind_param("i", $organization_id);
    $campaigns_stmt->execute();
    $campaigns_result = $campaigns_stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Organizations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #f8f8f8;
            color: #333;
        }
        .actions a {
            padding: 10px 15px;
            margin: 5px;
            display: inline-block;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
        }
        .view-history {
            background-color: #4caf50;
        }
        .delete {
            background-color: #e74c3c;
        }
        .view-history:hover, .delete:hover {
            opacity: 0.8;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<?php include("funding_admin_nav.php") ?>
<div class="container">
    <h1>Manage Organizations</h1>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="manage_organizations.php" method="GET">
            <input type="text" name="search" placeholder="Search by Name, Email, or Registration Number" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['organization_id']; ?></td>
                    <td>
                        <a href="?organization_id=<?php echo $row['organization_id']; ?>">
                            <?php echo htmlspecialchars($row['organization_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['organization_email']); ?></td>
                    <td class="actions">
                        <a href="?organization_id=<?php echo $row['organization_id']; ?>" class="view-history">View History</a>
                        <a href="delete_organization.php?id=<?php echo $row['organization_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this organization?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No organizations found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Organization Details -->
    <?php if ($organization_id): ?>
        <h2>Organization Details</h2>
        <p><strong>ID:</strong> <?php echo $organization_id; ?></p>

        <h3>Donation History</h3>
        <?php if ($donations_result && $donations_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Campaign ID</th>
                        <th>Amount</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($donation = $donations_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $donation['donation_id']; ?></td>
                            <td><?php echo $donation['campaign_id']; ?></td>
                            <td><?php echo $donation['amount']; ?></td>
                            <td><?php echo $donation['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No donation history available.</p>
        <?php endif; ?>

        <h3>Comment History</h3>
        <?php if ($comments_result && $comments_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Comment ID</th>
                        <th>Campaign ID</th>
                        <th>User ID</th>
                        <th>Comment</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $comment['crowd_comments_id']; ?></td>
                            <td><?php echo $comment['campaign_id']; ?></td>
                            <td><?php echo $comment['normal_user_id']; ?></td>
                            <td><?php echo htmlspecialchars($comment['crowd_comments']); ?></td>
                            <td><?php echo $comment['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No comment history available.</p>
        <?php endif; ?>

        <h3>Campaign History</h3>
        <?php if ($campaigns_result && $campaigns_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Campaign ID</th>
                        <th>Title</th>
                        <th>Goal Amount</th>
                        <th>Donate Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($campaign = $campaigns_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $campaign['campaign_id']; ?></td>
                            <td><?php echo htmlspecialchars($campaign['title']); ?></td>
                            <td><?php echo $campaign['goal_amount']; ?></td>
                            <td><?php echo $campaign['donate_amount']; ?></td>
                            <td><?php echo htmlspecialchars($campaign['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No campaign history available.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
