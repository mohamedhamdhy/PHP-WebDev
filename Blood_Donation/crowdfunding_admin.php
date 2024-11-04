<?php
session_start();

// Check if the admin is logged in and is of type 'funding_admin'
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'funding_admin') {
    header("Location: admin_login.php"); // Redirect to login page if not logged in or not funding_admin
    exit();
}

// Include database connection
include 'db.php'; // Change this to your actual DB connection file

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM crowdfunding_admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Update last login timestamp
$last_login = $admin['last_login'];

// Fetch campaigns
$sql_campaigns = "SELECT * FROM campaigns";
$stmt_campaigns = $conn->prepare($sql_campaigns);
$stmt_campaigns->execute();
$result_campaigns = $stmt_campaigns->get_result();
$campaigns = $result_campaigns->fetch_all(MYSQLI_ASSOC);

// Fetch comments
$sql_comments = "SELECT * FROM crowd_comments";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
$comments = $result_comments->fetch_all(MYSQLI_ASSOC);

// Fetch donations
$sql_donations = "SELECT * FROM donations";
$stmt_donations = $conn->prepare($sql_donations);
$stmt_donations->execute();
$result_donations = $stmt_donations->get_result();
$donations = $result_donations->fetch_all(MYSQLI_ASSOC);

// Fetch total active campaigns
$sql_active_campaigns = "SELECT COUNT(*) AS total_active FROM campaigns WHERE campaign_status = 'active'";
$stmt_active_campaigns = $conn->prepare($sql_active_campaigns);
$stmt_active_campaigns->execute();
$result_active_campaigns = $stmt_active_campaigns->get_result();
$total_active_campaigns = $result_active_campaigns->fetch_assoc()['total_active'];

// Fetch total users
$sql_users = "SELECT COUNT(*) AS total_users FROM normal_user";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$total_users = $result_users->fetch_assoc()['total_users'];

// Fetch total organizations
$sql_organizations = "SELECT COUNT(*) AS total_organizations FROM organization";
$stmt_organizations = $conn->prepare($sql_organizations);
$stmt_organizations->execute();
$result_organizations = $stmt_organizations->get_result();
$total_organizations = $result_organizations->fetch_assoc()['total_organizations'];

// Fetch total donations
$total_donations = count($donations);

// Fetch total donations this month
$sql_donations_month = "SELECT SUM(amount) AS total_donations_month FROM donations WHERE MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)";
$stmt_donations_month = $conn->prepare($sql_donations_month);
$stmt_donations_month->execute();
$result_donations_month = $stmt_donations_month->get_result();
$total_donations_month = $result_donations_month->fetch_assoc()['total_donations_month'];

// Fetch total amount donated
$sql_total_amount_donated = "SELECT SUM(amount) AS total_amount FROM donations";
$stmt_total_amount_donated = $conn->prepare($sql_total_amount_donated);
$stmt_total_amount_donated->execute();
$result_total_amount_donated = $stmt_total_amount_donated->get_result();
$total_amount_donated = $result_total_amount_donated->fetch_assoc()['total_amount'];

// Fetch the most popular campaign (highest donation amount)
$sql_popular_campaign = "
    SELECT campaigns.title, campaigns.images, SUM(donations.amount) AS total_donated 
    FROM campaigns 
    LEFT JOIN donations ON campaigns.campaign_id = donations.campaign_id 
    GROUP BY campaigns.campaign_id 
    ORDER BY total_donated DESC 
    LIMIT 1
";

$stmt_popular_campaign = $conn->prepare($sql_popular_campaign);

if ($stmt_popular_campaign === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt_popular_campaign->execute();
$result_popular_campaign = $stmt_popular_campaign->get_result();

$most_popular_campaign = $result_popular_campaign->fetch_assoc();

// Fetch recent donations
$sql_recent_donations = "SELECT donations.*, normal_user.normal_user_firstname FROM donations JOIN normal_user ON donations.normal_user_id = normal_user.normal_user_id ORDER BY created_at DESC LIMIT 5";
$stmt_recent_donations = $conn->prepare($sql_recent_donations);
$stmt_recent_donations->execute();
$result_recent_donations = $stmt_recent_donations->get_result();
$recent_donations = $result_recent_donations->fetch_all(MYSQLI_ASSOC);

// Fetch recent comments
$sql_recent_comments = "SELECT crowd_comments.*, normal_user.normal_user_firstname FROM crowd_comments JOIN normal_user ON crowd_comments.normal_user_id = normal_user.normal_user_id ORDER BY created_at DESC LIMIT 5";
$stmt_recent_comments = $conn->prepare($sql_recent_comments);
$stmt_recent_comments->execute();
$result_recent_comments = $stmt_recent_comments->get_result();
$recent_comments = $result_recent_comments->fetch_all(MYSQLI_ASSOC);

// Fetch recent campaigns
$sql_recent_campaigns = "SELECT * FROM campaigns ORDER BY created_at DESC LIMIT 5";
$stmt_recent_campaigns = $conn->prepare($sql_recent_campaigns);
$stmt_recent_campaigns->execute();
$result_recent_campaigns = $stmt_recent_campaigns->get_result();
$recent_campaigns = $result_recent_campaigns->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
    <style>
 body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f4f8;
    margin: 0;
    padding: 0;
}

.dashboard-container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
    overflow: hidden;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

header {
    background: #007BFF;
    color: #fff;
    padding: 30px 20px;
    text-align: center;
    border-radius: 8px 8px 0 0;
}

header h1 {
    margin: 0;
    font-size: 2.5em;
}

header p {
    margin: 5px 0;
}

nav {
    margin: 20px 0;
}

nav ul {
    list-style: none;
    padding: 0;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

nav ul li {
    margin: 10px;
}

nav ul li a {
    text-decoration: none;
    color: #007BFF;
    padding: 12px 25px;
    border: 2px solid #007BFF;
    border-radius: 5px;
    transition: background 0.3s, color 0.3s;
    font-weight: bold;
}

nav ul li a:hover {
    background: #007BFF;
    color: white;
}

main {
    margin-top: 20px;
}

h2 {
    margin-top: 30px;
    color: #333;
    font-size: 1.8em;
    border-bottom: 2px solid #007BFF;
    padding-bottom: 5px;
}

.overview {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 20px;
}

.overview h3 {
    margin: 10px;
    padding: 15px;
    background: #007BFF;
    color: white;
    border-radius: 5px;
    flex: 1 1 30%;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
    font-size: 1em;
}

table th {
    background: #f4f4f4;
    color: #333;
}

table tr:hover {
    background: #f1f1f1;
}

.error-message {
    color: red;
    margin-bottom: 20px;
    text-align: center;
}

    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome to the Crowdfunding Admin Dashboard</h1>
            <p>Logged in as: <?php echo htmlspecialchars($admin['full_name']); ?></p>
            <p>Last Login: <?php echo htmlspecialchars($last_login); ?></p>
            <a href="cadmin_logout.php" style="color: white; font-weight: bold;">Logout</a>
        </header>

        <nav>
            <ul>
                <li><a href="manage_campaigns.php">Manage Campaigns</a></li>
                <li><a href="manage_comments.php">Manage Comments</a></li>
                <li><a href="manage_donations.php">Manage Donations</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_organizations.php">Manage Organizations</a></li>
            </ul>
        </nav>

        <main>
            <h2>Overview</h2>
            <div class="overview">
                <h3>Total Campaigns: <?php echo count($campaigns); ?></h3>
                <h3>Total Active Campaigns: <?php echo $total_active_campaigns; ?></h3>
                <h3>Total Users: <?php echo $total_users; ?></h3>
                <h3>Total Organizations: <?php echo $total_organizations; ?></h3>
                <h3>Total Comments: <?php echo count($comments); ?></h3>
                <h3>Total Donations: <?php echo $total_donations; ?></h3>
                <h3>Total Donations This Month: <?php echo $total_donations_month ? $total_donations_month : 0; ?></h3>
                <h3>Total Amount Donated: <?php echo $total_amount_donated ? $total_amount_donated : 0; ?></h3>
            </div>

            <h2>Most Popular Campaign</h2>
<?php if ($most_popular_campaign): ?>
    <div>
        <?php if (!empty($most_popular_campaign['images'])): ?>
            <img src="<?php echo htmlspecialchars($most_popular_campaign['images']); ?>" alt="Campaign Image" style="max-width: 300px; height: auto;">
        <?php endif; ?>
        <p>
            Title: <?php echo htmlspecialchars($most_popular_campaign['title']); ?> | 
            Total Donated: <?php echo htmlspecialchars(number_format($most_popular_campaign['total_donated'], 2)); ?>
        </p>
    </div>
<?php else: ?>
    <p>No donations have been made yet.</p>
<?php endif; ?>

            <h2>Recent Donations</h2>
            <table>
                <thead>
                    <tr>
                        <th>Donor Name</th>
                        <th>Campaign</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_donations as $donation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($donation['normal_user_firstname']); ?></td>
                            <td><?php echo htmlspecialchars($donation['campaign_id']); ?></td>
                            <td><?php echo htmlspecialchars($donation['amount']); ?></td>
                            <td><?php echo htmlspecialchars($donation['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Recent Campaigns</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_campaigns as $campaign): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($campaign['title']); ?></td>
                            <td><?php echo htmlspecialchars($campaign['description']); ?></td>
                            <td><?php echo htmlspecialchars($campaign['campaign_status']); ?></td>
                            <td><?php echo htmlspecialchars($campaign['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
