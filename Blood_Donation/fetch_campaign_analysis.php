<?php
// Include the database connection
include 'db.php';

if (isset($_GET['campaign_id'])) {
    $campaign_id = intval($_GET['campaign_id']);

    // Fetch the specific campaign data
    $sql = "SELECT * FROM campaigns WHERE campaign_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $campaign = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Analysis</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #007BFF;
        }
        .section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .campaign-overview p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .chart-container {
            height: 370px; 
            width: 100%; 
            position: relative; 
            margin-bottom: 20px;
        }
        .chart-container > div {
            height: 100%; 
        }
        .back-button {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            .chart-container {
                height: 250px; /* Adjust height for smaller screens */
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Back Button -->
    <a href="manage_campaigns.php" class="back-button">Back to Campaigns</a>

    <!-- Campaign Overview -->
    <div class="section campaign-overview">
        <h2><?php echo htmlspecialchars($campaign['title']); ?></h2>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($campaign['category']); ?></p>
        <p><strong>Goal Amount:</strong> $<?php echo number_format($campaign['goal_amount'], 2); ?></p>
        <p><strong>Donated Amount:</strong> $<?php echo number_format($campaign['donate_amount'], 2); ?> 
            (<?php echo round(($campaign['donate_amount'] / $campaign['goal_amount']) * 100, 2); ?>%)</p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($campaign['duration']); ?> days</p>
        <p><strong>District:</strong> <?php echo htmlspecialchars($campaign['district']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($campaign['description']); ?></p>
    </div>

    <!-- Donation Progress Pie Chart using CanvasJS -->
    <div class="chart-container">
        <div id="donationProgressChartContainer"></div>
    </div>
    <script>
        window.onload = function() {
            var donationDataPoints = [
                { label: "Donated", y: <?php echo $campaign['donate_amount']; ?> },
                { label: "Remaining", y: <?php echo max(0, $campaign['goal_amount'] - $campaign['donate_amount']); ?> }
            ];

            var donationChart = new CanvasJS.Chart("donationProgressChartContainer", {
                animationEnabled: true,
                title: { text: "Donation Progress" },
                data: [{
                    type: "pie",
                    yValueFormatString: "#,##0.00\"$\"",
                    indexLabel: "{label}: {y}",
                    dataPoints: donationDataPoints
                }]
            });

            donationChart.render();

            // Funding Breakdown Chart
            var fundingLabels = [];
            var fundingAmounts = [];

            // Fetch funding breakdown data
            <?php
                $funding_result = $conn->query("SELECT * FROM funding_breakdown WHERE campaign_id = $campaign_id");
                if ($funding_result->num_rows > 0) {
                    while ($breakdown = $funding_result->fetch_assoc()) {
                        echo "fundingLabels.push('" . addslashes($breakdown['item_description']) . "');";
                        echo "fundingAmounts.push(" . $breakdown['amount'] . ");";
                    }
                } else {
                    echo "console.warn('No funding breakdown data found for campaign ID: $campaign_id');";
                }
            ?>

            // Create data points for funding breakdown chart
            var fundingDataPoints = [];
            for (var i = 0; i < fundingLabels.length; i++) {
                fundingDataPoints.push({ label: fundingLabels[i], y: fundingAmounts[i] });
            }

            // Render funding breakdown chart
            var fundingChart = new CanvasJS.Chart("fundingBreakdownChartContainer", {
                animationEnabled: true,
                title: { text: "Funding Breakdown" },
                data: [{
                    type: "column",
                    yValueFormatString: "#,##0.00\"$\"",
                    dataPoints: fundingDataPoints
                }]
            });

            fundingChart.render();
        }
    </script>

    <!-- Comments Section -->
    <div class="section campaign-comments">
        <h3>Comments</h3>
        <?php
        $comment_result = $conn->query("SELECT * FROM crowd_comments WHERE campaign_id = $campaign_id");
        while ($comment = $comment_result->fetch_assoc()) {
            echo '<div class="comment">';
            echo '<p>' . htmlspecialchars($comment['crowd_comments']) . '</p>';
            echo '<small>Posted on ' . htmlspecialchars($comment['created_at']) . '</small>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Donations Section -->
    <div class="section campaign-donations">
        <h3>Donations</h3>
        <table>
            <thead>
                <tr>
                    <th>Donation ID</th>
                    <th>User/Organization</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $donation_result = $conn->query("SELECT * FROM donations WHERE campaign_id = $campaign_id");
                while ($donation = $donation_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($donation['donation_id']) . '</td>';
                    echo '<td>';
                    if ($donation['normal_user_id']) {
                        $user_result = $conn->query("SELECT normal_user_firstname, normal_user_lastname FROM normal_user WHERE normal_user_id = " . $donation['normal_user_id']);
                        $user = $user_result->fetch_assoc();
                        echo htmlspecialchars($user['normal_user_firstname']) . ' ' . htmlspecialchars($user['normal_user_lastname']);
                    } elseif ($donation['organization_id']) {
                        $org_result = $conn->query("SELECT organization_name FROM organization WHERE organization_id = " . $donation['organization_id']);
                        $org = $org_result->fetch_assoc();
                        echo htmlspecialchars($org['organization_name']);
                    }
                    echo '</td>';
                    echo '<td>$' . number_format($donation['amount'], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($donation['created_at']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Funding Breakdown Chart container -->
    <div class="chart-container" style="width: 100%; height: 400px;">
        <div id="fundingBreakdownChartContainer" style="height: 100%;"></div>
    </div>

</div>

</body>
</html>

<?php
    } else {
        echo "<p>Campaign not found.</p>";
    }
} else {
    echo "<p>Invalid campaign ID.</p>";
}
$conn->close();
?>
