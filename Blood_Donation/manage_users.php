<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle user search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch users based on search query
$query = "SELECT * FROM normal_user 
          WHERE normal_user_firstname LIKE '%$search_query%' 
          OR normal_user_lastname LIKE '%$search_query%' 
          OR normal_user_email LIKE '%$search_query%' 
          OR normal_user_location LIKE '%$search_query%' 
          OR normal_user_bloodtype LIKE '%$search_query%'
          OR NIC LIKE '%$search_query%'";
$result = $conn->query($query);

// Check if a delete request was made
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    // Delete user donations, comments, and campaigns (optional)
    $conn->query("DELETE FROM donations WHERE normal_user_id = $user_id");
    $conn->query("DELETE FROM crowd_comments WHERE normal_user_id = $user_id");
    $conn->query("DELETE FROM campaigns WHERE normal_user_id = $user_id");
    
    // Delete the user account
    $conn->query("DELETE FROM normal_user WHERE normal_user_id = $user_id");
    
    echo "<script>alert('User account deleted successfully!');</script>";
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        td {
            background-color: #fff;
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
        img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }
        .user-history h3 {
            margin: 20px 0 10px;
        }
        .user-history table {
            margin-bottom: 20px;
        }
        .empty-message {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin: 20px 0;
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
    <h1>Manage Normal Users</h1>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="manage_users.php" method="GET">
            <input type="text" name="search" placeholder="Search by Name, Email, Location, Blood Type, or NIC" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Profile Picture</th>
            <th>Name</th>
            <th>Email</th>
            <th>Location</th>
            <th>Blood Type</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><img src="<?php echo $row['normal_user_profile_picture']; ?>" alt="Profile Picture"></td>
                    <td><?php echo $row['normal_user_firstname'] . " " . $row['normal_user_lastname']; ?></td>
                    <td><?php echo $row['normal_user_email']; ?></td>
                    <td><?php echo $row['normal_user_location']; ?></td>
                    <td><?php echo $row['normal_user_bloodtype']; ?></td>
                    <td class="actions">
                        <a href="manage_users.php?user_id=<?php echo $row['normal_user_id']; ?>" class="view-history">View History</a>
                        <a href="manage_users.php?delete_user=<?php echo $row['normal_user_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6">No users found matching your search criteria.</td>
            </tr>
        <?php } ?>
    </table>

    <?php
    // If a specific user is selected for viewing details
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        // Fetch donation history
        $donations_query = "SELECT * FROM donations WHERE normal_user_id = $user_id";
        $donations_result = $conn->query($donations_query);

        // Fetch comment history
        $comments_query = "SELECT * FROM crowd_comments WHERE normal_user_id = $user_id";
        $comments_result = $conn->query($comments_query);

        // Fetch campaign history
        $campaigns_query = "SELECT * FROM campaigns WHERE normal_user_id = $user_id";
        $campaigns_result = $conn->query($campaigns_query);
    ?>

    <div class="user-history">
        <h2>User History</h2>

        <h3>Donation History</h3>
        <?php if ($donations_result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Donation ID</th>
                    <th>Campaign ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                <?php while ($donation = $donations_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $donation['donation_id']; ?></td>
                        <td><?php echo $donation['campaign_id']; ?></td>
                        <td><?php echo $donation['amount']; ?></td>
                        <td><?php echo $donation['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="empty-message">User doesn't have any donation history yet.</p>
        <?php } ?>

        <h3>Comment History</h3>
        <?php if ($comments_result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Comment ID</th>
                    <th>Campaign ID</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
                <?php while ($comment = $comments_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $comment['crowd_comments_id']; ?></td>
                        <td><?php echo $comment['campaign_id']; ?></td>
                        <td><?php echo $comment['crowd_comments']; ?></td>
                        <td><?php echo $comment['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="empty-message">User doesn't have any comment history yet.</p>
        <?php } ?>

        <h3>Campaign History</h3>
        <?php if ($campaigns_result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Campaign ID</th>
                    <th>Title</th>
                    <th>Goal Amount</th>
                    <th>Donate Amount</th>
                    <th>Created At</th>
                </tr>
                <?php while ($campaign = $campaigns_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $campaign['campaign_id']; ?></td>
                        <td><?php echo $campaign['title']; ?></td>
                        <td><?php echo $campaign['goal_amount']; ?></td>
                        <td><?php echo $campaign['donate_amount']; ?></td>
                        <td><?php echo $campaign['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="empty-message">User doesn't have any campaign history yet.</p>
        <?php } ?>
    </div>

    <?php } ?>

</div>

</body>
</html>
