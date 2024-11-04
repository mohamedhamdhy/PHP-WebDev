<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch all campaigns from the database
$query = "SELECT * FROM campaigns WHERE campaign_status = 'approved'";
$result = $conn->query($query);
// Close the database connection


// Fetch all blood requests from the database
$query1 = "SELECT * FROM blood_requests WHERE delivery_status = 'approved'"; // Adjust the condition as needed
$result1 = $conn->query($query1);

// Fetch all organization blood requests from the database
$query2 = "SELECT * FROM blood_requests_org WHERE delivery_status = 'approved'"; // Adjust the condition as needed
$result2 = $conn->query($query2);




$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaigns Display</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .container {
        margin-top: 60px;
    }

    .campaign-container {
        margin-top: 50px;
        position: relative;
        overflow: hidden;
    }

    .campaign-wrapper {
        display: flex;
        gap: 15px; /* Space between boxes */
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 10px 0;
        scroll-snap-type: x mandatory;
    }

    .campaign-box {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        flex: 1 0 23%; /* Adjust to fit 4 boxes in the container */
        max-width: 23%; /* Fixed width for each box */
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        height: 500px; /* Fixed height for each box */
        position: relative;
        opacity: 1;
        transform: scale(1);
        transition: opacity 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease-in-out;
        cursor: pointer;
    }

    /* Hover effect to zoom in */
    .campaign-box:hover {
        transform: scale(1.05); /* Slight zoom in */
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2); /* Add a bit more shadow for effect */
    }

    /* Animation for fade out on click */
    .fade-out {
        animation: fadeOut 0.5s forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: scale(0.9); /* Shrink before disappearing */
        }
    }

    .campaign-box img {
        width: 100%;
        height: 150px; /* Adjust height as needed */
        object-fit: cover;
    }

    .campaign-details {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Ensure this section takes up available space */
    }

    .campaign-category {
        font-size: 14px;
        color: #007bff;
        margin-bottom: 5px;
    }

    .campaign-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .campaign-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }

    .campaign-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: #555;
        margin-bottom: 10px;
    }

    .progress {
        height: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        background-color: #f3f3f3;
    }

    .progress-bar {
        background-color: #28a745;
    }

    .donation-info {
        font-size: 14px;
        color: #555;
    }

    .campaign-actions {
        display: flex;
        justify-content: space-between;
        margin-top: auto; /* Push actions to the bottom */
        padding: 10px 15px;
    }

    .action-button {
        flex: 1; /* Buttons take equal space */
        margin: 0 5px; /* Space between buttons */
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .like-button:hover {
        background-color: #e7f0ff;
        color: #007bff;
    }

    .share-button:hover {
        background-color: #e7f0ff;
        color: #17a2b8;
    }

    .comments-button:hover {
        background-color: #e7f0ff;
        color: #28a745;
    }

    /* Floating button styles */
    .floating-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 999;
    }

    .floating-button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        font-size: 24px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .floating-button:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    /* Slider button styles */
    .slider-button {
        position: absolute;
        top: 50%;
        background-color: rgba(0, 0, 0, 0.5);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 24px;
        z-index: 1000;
        transform: translateY(-50%);
    }

    .slider-button.left {
        left: 10px;
    }

    .slider-button.right {
        right: 10px;
    }

    .slider-button i {
        pointer-events: none; /* Prevent click events on icon */
    }

    /* Explore button styles */
    .explore-button {
        text-decoration: none;
        display: inline-block;
        margin: 15px 90px;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: orange;
        border-radius: 5px;
        border: 1px solid #007bff;
        text-align: center;
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    }

    .explore-button:hover {
        background-color: #e65e00;
        border-color: #0056b3;
        transform: translateY(-2px);
    }

    .explore-button:active {
        background-color: #003d7a;
        border-color: #003d7a;
        transform: translateY(0);
    }
    .campaign-info div {
    margin-bottom: 5px; /* Adds some space between each info item */
    font-size: 0.9rem; /* Smaller font size for additional info */
}

.campaign-category {
    font-weight: bold;
    color: #6c757d; /* Change color for better distinction */
}

.campaign-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: 5px; /* Spacing above the title */
}


    
</style>

</head>
<body>

   <?php include("nav.php");?>

    <!-- Heading -->
    <div class="container">
        <h1 class="my-4 text-left">Our Dreams</h1>
        <p>
         Fuel Your Passion! Explore our Trending Dreams and 
         <br>
         help make dreams come true by supporting our incredible community of dreamers.
        </p>
    </div>

    <!-- Campaigns Section -->
    <div class="container campaign-container">
        <div class="slider-button left" onclick="scrollLeft()">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="slider-button right" onclick="scrollRight()">
            <i class="fas fa-chevron-right"></i>
        </div>
        <div class="campaign-wrapper">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($campaign = $result->fetch_assoc()): ?>
            <div class="campaign-box" id="campaign-<?php echo $campaign['campaign_id']; ?>" onclick="animateAndRedirect(<?php echo $campaign['campaign_id']; ?>)">
                <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Campaign Image">
                <div class="campaign-details">
                    <div class="campaign-category"><?php echo htmlspecialchars($campaign['category']); ?></div>
                    <div class="campaign-info">
                        <div>District: <?php echo htmlspecialchars($campaign['district']); ?></div>
                    </div>
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
                        <div class="donation-info">
                            Duration: <?php echo htmlspecialchars($campaign['duration']); ?> days
                        </div>
                    </div>
                    <div class="campaign-actions">
                        <button class="action-button like-button"><i class="far fa-thumbs-up"></i> Like</button>
                        <button class="action-button share-button"><i class="fas fa-share-alt"></i> Share</button>
                        <button class="action-button comments-button"><i class="far fa-comments"></i> Comments</button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No campaigns available at the moment.</p>
    <?php endif; ?>
</div>





<script>
    function animateAndRedirect(campaignId) {
        // Add fade-out animation to the clicked campaign box
        const campaignBox = document.getElementById('campaign-' + campaignId);
        campaignBox.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        campaignBox.style.opacity = "0";
        campaignBox.style.transform = "scale(0.95)";

        // After the animation is complete, redirect to the details page
        setTimeout(function() {
            window.location.href = 'campaign_details.php?id=' + campaignId;
        }, 500); // Wait for 500ms (the length of the animation) before redirecting
    }
</script>

    </div>
 <div>
 <a href="funding.php" class="explore-button">Explore Dreams</a>
 </div>


 <!-- Heading -->
<div class="container">
    <h1 class="my-4 text-left">Blood Donation Requests</h1>
    <p>
        Join the fight to save lives! Explore our urgent blood requests and 
        <br>
        help those in need by supporting our incredible community of donors.
    </p>
</div>

<!-- Blood Requests Section -->
<div class="container campaign-container">
    <div class="slider-button left" onclick="scrollLeft()">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="slider-button right" onclick="scrollRight()">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="campaign-wrapper">
        <?php if ($result1->num_rows > 0): ?>
            <?php while ($request = $result1->fetch_assoc()): ?>
                <div class="campaign-box" id="request-<?php echo $request['id']; ?>" onclick="animateAndRedirect(<?php echo $request['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($request['blood_image']); ?>" alt="Blood Request Image">
                    <div class="campaign-details">
                        <div class="campaign-category"><?php echo htmlspecialchars($request['blood_type']); ?></div>
                        <div class="campaign-title"><?php echo htmlspecialchars($request['name']); ?></div>
                        <div class="campaign-description">
                            <?php echo htmlspecialchars(substr($request['blood_description'], 0, 100)) . '...'; ?>
                        </div>
                        <div class="campaign-info">
                            <div class="info-item">
                                <span class="info-label">Location:</span>
                                <span><?php echo htmlspecialchars($request['location']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">District:</span>
                                <span><?php echo htmlspecialchars($request['blood_district']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contact:</span>
                                <span><?php echo htmlspecialchars($request['contact']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Age:</span>
                                <span><?php echo htmlspecialchars($request['age']); ?> years</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Quantity Needed:</span>
                                <span><?php echo htmlspecialchars($request['quantity']); ?> units</span>
                            </div>
                        </div>
                       
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No blood requests available at the moment.</p>
        <?php endif; ?>
    </div>
</div>


<div>
    <a href="request_blood.php" class="explore-button">Explore Blood Requests</a>
</div>

<!-- Heading -->
<div class="container">
    <h1 class="my-4 text-left">Blood Donation Requests from Organizations</h1>
    <p>
        Join the fight to save lives! Explore our urgent blood requests from various organizations and 
        <br>
        help those in need by supporting our incredible community of donors.
    </p>
</div>

<!-- Blood Requests Section -->
<div class="container campaign-container">
    <div class="slider-button left" onclick="scrollLeft()">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="slider-button right" onclick="scrollRight()">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="campaign-wrapper">
        <?php if ($result2->num_rows > 0): ?>
            <?php while ($request = $result2->fetch_assoc()): ?>
                <div class="campaign-box" id="request-<?php echo $request['id']; ?>" onclick="animateAndRedirect(<?php echo $request['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($request['org_blood_image']); ?>" alt="Blood Request Image">
                    <div class="campaign-details">
                        <div class="campaign-category"><?php echo htmlspecialchars($request['blood_type']); ?></div>
                        <div class="campaign-title"><?php echo htmlspecialchars($request['organization_name']); ?></div>
                        <div class="campaign-description">
                            <?php echo htmlspecialchars(substr($request['reason'], 0, 100)) . '...'; ?>
                        </div>
                        <div class="campaign-info">
                            <div class="info-item">
                                <span class="info-label">Address:</span>
                                <span><?php echo htmlspecialchars($request['organization_address']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">District:</span>
                                <span><?php echo htmlspecialchars($request['org_blood_district']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contact:</span>
                                <span><?php echo htmlspecialchars($request['organization_phone']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Quantity Needed:</span>
                                <span><?php echo htmlspecialchars($request['quantity']); ?> units</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Request Date:</span>
                                <span><?php echo htmlspecialchars($request['request_date']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No blood requests available from organizations at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<div>
    <a href="request_blood_org.php" class="explore-button">Explore Organization Blood Requests</a>
</div>



 
    

    <!-- Floating buttons container -->
<div class="floating-container">
    <?php if (isset($_SESSION['user_type'])): ?>
        <!-- Inbox button redirects to the inbox page -->
        <button class="floating-button" onclick="window.location.href='inbox.php?user_id=<?php echo $_SESSION['user_id']; ?>&creator_id=<?php echo $creator_id; ?>&campaign_id=<?php echo $campaign_id; ?>'">
            <i class="fas fa-inbox"></i>
        </button>
        
        <!-- Notification button (currently placeholder) -->
        <button class="floating-button">
            <i class="fas fa-bell"></i>
        </button>
    <?php endif; ?>
</div>


    <!-- ChatBot Widget Script -->
    <script type="text/javascript">
        window.__be = window.__be || {};
        window.__be.id = "66db153ac14a9a0007c61336";
        (function() {
            var be = document.createElement('script'); be.type = 'text/javascript'; be.async = true;
            be.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.chatbot.com/widget/plugin.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(be, s);
        })();
    </script>
    <noscript>You need to <a href="https://www.chatbot.com/help/chat-widget/enable-javascript-in-your-browser/" rel="noopener nofollow">enable JavaScript</a> in order to use the AI chatbot tool powered by <a href="https://www.chatbot.com/" rel="noopener nofollow" target="_blank">ChatBot</a></noscript>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        function scrollLeft() {
            document.querySelector('.campaign-wrapper').scrollBy({
                left: -300, // Adjust the scroll amount
                behavior: 'smooth'
            });
        }

        function scrollRight() {
            document.querySelector('.campaign-wrapper').scrollBy({
                left: 300, // Adjust the scroll amount
                behavior: 'smooth'
            });
        }
    </script>
    <script>
    function animateAndRedirect(requestId) {
        // Add fade-out animation to the clicked request box
        const requestBox = document.getElementById('request-' + requestId);
        requestBox.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        requestBox.style.opacity = "0";
        requestBox.style.transform = "scale(0.95)";

        // After the animation is complete, redirect to the details page
        setTimeout(function() {
            window.location.href = 'blood_request_details.php?id=' + requestId;
        }, 500); // Wait for 500ms (the length of the animation) before redirecting
    }

    function scrollLeft() {
        // Logic for scrolling left
    }

    function scrollRight() {
        // Logic for scrolling right
    }
</script>

<script>
    function animateAndRedirect(campaignId) {
        // Add fade-out animation to the clicked campaign box
        const campaignBox = document.getElementById('campaign-' + campaignId);
        campaignBox.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        campaignBox.style.opacity = "0";
        campaignBox.style.transform = "scale(0.95)";

        // After the animation is complete, redirect to the details page
        setTimeout(function() {
            window.location.href = 'campaign_details.php?id=' + campaignId;
        }, 500); // Wait for 500ms (the length of the animation) before redirecting
    }
</script>
</body>
</html>
