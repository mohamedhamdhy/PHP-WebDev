<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch campaigns from the database
$query = "SELECT * FROM campaigns WHERE campaign_status = 'approved'";
$result = $conn->query($query);

// Fetch distinct categories for filtering
$categoryQuery = "SELECT DISTINCT category FROM campaigns";
$categories = $conn->query($categoryQuery);

// Fetch distinct districts for filtering
$districtQuery = "SELECT DISTINCT district FROM campaigns"; // Adjust table/column names as needed
$districts = $conn->query($districtQuery);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funding Campaign - LifeBridge</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4; /* Light background for contrast */
        }

        /* Header Background */
        .header-background {
            background-image: url('edit_poor.jpg'); /* Replace with your background image path */
            background-size: cover;
            background-position: center;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            position: relative;
            margin-bottom: 30px; /* Space between header and content */
        }

        .header-background h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .header-background .start-campaign-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header-background .start-campaign-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .filter-section select {
            width: 30%;
            margin-right: 10px;
        }

        /* Campaign Box */
        .campaign-box {
            background-color: #ffffff;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            overflow: hidden; /* Ensure no overflow */
        }

        .campaign-box:hover {
            transform: translateY(-5px);
        }

        .campaign-box img {
            width: 100%;
            height: 200px; /* Fixed height for uniformity */
            object-fit: cover; /* Maintain aspect ratio */
        }

        .campaign-details {
            padding: 15px;
        }

        .campaign-category {
            font-weight: bold;
            color: #007bff;
        }

        .campaign-title {
            font-size: 1.5em;
            margin: 10px 0;
            font-weight: 600; /* Stronger title */
        }

        .campaign-description {
            color: #555;
            margin: 10px 0;
            height: 60px; /* Fixed height for consistency */
            overflow: hidden; /* Hide overflow */
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 5px;
            height: 20px; /* Fixed height for progress bar */
        }

        .progress-bar {
            background-color: #007bff;
            transition: width 0.3s;
        }

        .donation-info {
            font-size: 0.9em;
            margin: 5px 0;
        }

        .campaign-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .action-button {
            background-color: #f8f9fa;
            border: 1px solid #007bff;
            color: #007bff;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #007bff;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-background h1 {
                font-size: 36px;
            }

            .campaign-title {
                font-size: 1.2em;
            }

            .filter-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-section select {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>

    <?php include("nav.php") ?>

    <!-- Header Background Section -->
    <div class="header-background">
        <div>
            <h1>Launch Your Campaign Today</h1>
            <a href="crowd_funding_campaign.php" class="start-campaign-btn">Start Campaign</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <div class="container">
            <div class="filter-section">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category['category']); ?>"><?php echo htmlspecialchars($category['category']); ?></option>
                    <?php endwhile; ?>
                </select>

                <select id="districtFilter">
                    <option value="">All Districts</option>
                    <?php while ($district = $districts->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($district['district']); ?>"><?php echo htmlspecialchars($district['district']); ?></option>
                    <?php endwhile; ?>
                </select>

                

                <select id="amountFilter">
                    <option value="">Select Amount</option>
                    <option value="high">Highest</option>
                    <option value="medium">Medium</option>
                    <option value="low">Lowest</option>
                </select>

                <select id="fundedFilter">
                    <option value="">Funding Status</option>
                    <option value="fully">Fully Funded</option>
                    <option value="partially">Partially Funded</option>
                    <option value="not">Not Funded</option>
                </select>

                <input type="text" id="searchInput" placeholder="Search campaigns..." class="form-control" style="width: 30%; margin-left: 10px;">
            </div>

            <h2>Available Campaigns</h2>
            <div class="row" id="campaignsContainer">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($campaign = $result->fetch_assoc()): ?>
                        <div class="col-md-4 campaign-item" data-category="<?php echo htmlspecialchars($campaign['category']); ?>" data-district="<?php echo htmlspecialchars($campaign['district']); ?>" data-amount="<?php echo $campaign['donate_amount']; ?>" data-goal="<?php echo $campaign['goal_amount']; ?>">
                            <div class="campaign-box" id="campaign-<?php echo $campaign['campaign_id']; ?>" onclick="animateAndRedirect(<?php echo $campaign['campaign_id']; ?>)">
                                <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Campaign Image">
                                <div class="campaign-details">
                                    <div class="campaign-category"><?php echo htmlspecialchars($campaign['category']); ?></div>
                                    <div class="campaign-title"><?php echo htmlspecialchars($campaign['title']); ?></div>
                                    <div class="campaign-description">
                                        <?php echo htmlspecialchars(substr($campaign['description'], 0, 100)) . '...'; ?>
                                    </div>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo ($campaign['donate_amount'] / $campaign['goal_amount']) * 100; ?>%;" aria-valuenow="<?php echo $campaign['donate_amount']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $campaign['goal_amount']; ?>"></div>
                                        </div>
                                        <div class="donation-info">
                                            <span class="me-3">Donated: LKR <?php echo number_format($campaign['donate_amount'], 2); ?></span>
                                            <span>Raised: <?php echo number_format(($campaign['donate_amount'] / $campaign['goal_amount']) * 100, 2); ?>%</span>
                                        </div>
                                    </div>
                                    <div class="campaign-actions">
                                        <div class="action-button" onclick="donate(<?php echo $campaign['campaign_id']; ?>)">Donate</div>
                                        <div class="action-button" onclick="viewDetails(<?php echo $campaign['campaign_id']; ?>)">View Details</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No campaigns found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
    // Filtering functionality
    $('#categoryFilter, #districtFilter, #amountFilter, #fundedFilter').on('change', filterCampaigns);
    $('#searchInput').on('keyup', filterCampaigns);

    function filterCampaigns() {
        const category = $('#categoryFilter').val().toLowerCase();
        const district = $('#districtFilter').val().toLowerCase();
        const searchTerm = $('#searchInput').val().toLowerCase();
        const amount = $('#amountFilter').val();
        const funded = $('#fundedFilter').val();

        $('.campaign-item').each(function() {
            const $this = $(this);
            const campaignCategory = $this.data('category').toLowerCase();
            const campaignDistrict = $this.data('district').toLowerCase();
            const campaignAmount = parseFloat($this.data('amount'));
            const campaignGoal = parseFloat($this.data('goal'));

            const isCategoryMatch = !category || campaignCategory.includes(category);
            const isDistrictMatch = !district || campaignDistrict.includes(district);
            const isSearchMatch = $this.find('.campaign-title').text().toLowerCase().includes(searchTerm);

            // Filter by amount (you can adjust the thresholds as needed)
            const isAmountMatch = 
                amount === 'high' ? campaignGoal > 15500 :
                amount === 'medium' ? campaignGoal <= 15000 && campaignGoal > 8000 :
                amount === 'low' ? campaignGoal <= 8000 : true;

            // Filter by funding status
            const isFundedMatch = 
                funded === 'fully' ? (campaignAmount >= campaignGoal) :
                funded === 'partially' ? (campaignAmount > 0 && campaignAmount < campaignGoal) :
                funded === 'not' ? campaignAmount === 0 : true;

            // Show or hide the campaign item based on all filters
            if (isCategoryMatch && isDistrictMatch && isSearchMatch && isAmountMatch && isFundedMatch) {
                $this.show();
            } else {
                $this.hide();
            }
        });
    }
});


// Functions to handle donation and view details
function donate(campaignId) {
            // Redirect to donation page with campaign_id
        window.location.href = 'donate.php?campaign_id=' + campaignId;
        }

        function viewDetails(campaignId) {
               // Redirect to campaign details page with id
        window.location.href = 'campaign_details.php?id=' + campaignId;
        }

        function animateAndRedirect(campaignId) {
            // Optional: Animate or transition effects before redirecting
            alert('Campaign clicked! Campaign ID: ' + campaignId);
            // Example: window.location.href = 'campaign_details.php?id=' + campaignId;
        }

        
    </script>
</body>
</html>


