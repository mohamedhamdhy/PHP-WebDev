<?php
// Include the database connection
include 'db.php';

// Initialize search variable
$search = '';

// Check if the search form has been submitted
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Fetch donations with campaign, user, and organization details
$sql = "
    SELECT d.donation_id, d.amount, d.created_at, 
           c.title AS campaign_title, 
           IFNULL(nu.normal_user_firstname, org.organization_name) AS donor_name, 
           IFNULL(nu.normal_user_profile_picture, org.organization_profile_picture) AS donor_profile_picture,
           c.category AS campaign_category
    FROM donations d
    LEFT JOIN campaigns c ON d.campaign_id = c.campaign_id
    LEFT JOIN normal_user nu ON d.normal_user_id = nu.normal_user_id
    LEFT JOIN organization org ON d.organization_id = org.organization_id
    WHERE (nu.normal_user_firstname LIKE ? OR org.organization_name LIKE ? OR c.title LIKE ?)
    ORDER BY d.created_at DESC
";

// Prepare the statement
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%"; // Prepare search term for wildcard search
$stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm); // Bind parameters
$stmt->execute(); // Execute the statement
$result = $stmt->get_result(); // Get the result set
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donations</title>
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

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .donations-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .donation-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 15px;
            width: 90%;
            transition: transform 0.2s;
        }

        .donation-card:hover {
            transform: scale(1.02);
        }

        .donor-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .donor-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .donation-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .donation-note {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 15px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Manage Donations</h1>

    <div class="search-container">
        <form method="POST">
            <input type="text" name="search" placeholder="Search by donor name or campaign title" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="donations-container">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="donation-card">
                <div class="donor-info">
                    <img src="<?php echo $row['donor_profile_picture']; ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($row['donor_name']); ?> (Campaign: <?php echo htmlspecialchars($row['campaign_title']); ?>)</h3>
                </div>
                <div class="donation-info">
                    <p>Amount: $<?php echo htmlspecialchars($row['amount']); ?></p>
                    <p>Date: <?php echo htmlspecialchars($row['created_at']); ?></p>
                </div>
            </div>
        <?php } ?>
        <div class="donation-note">
            <p>If you need to adjust your donation, please contact our support team.</p>
        </div>
    </div>

</body>
</html>
