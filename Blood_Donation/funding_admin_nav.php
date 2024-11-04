<?php



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
    <style>
        /* Basic styles for the navigation bar */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar .active {
            background-color: #4CAF50;
            color: white;
        }
        .navbar .right {
            float: right;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="crowdfunding_admin.php" class="active">Dashboard</a>
    <a href="manage_campaigns.php">Manage Campaigns</a>
    <a href="view_donations.php">View Donations</a>
    <a href="view_users.php">View Users</a>
    <a href="admin_profile.php">Profile</a>
    <a href="change_password.php">Change Password</a>
    <a href="logout.php" class="right">Logout</a>
</div>

</body>
</html>
