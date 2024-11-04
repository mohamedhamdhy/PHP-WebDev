<?php

include 'db.php'; // Include your database connection

$is_logged_in = isset($_SESSION['user_id']);
$is_logged_in = isset($_SESSION['user_type']);
$show_manage_campaign_link = false; // Flag to display Manage Campaign link
$show_blood_campaign_link = false; // Flag to display Blood Campaign link
$show_donate_button = false; // Flag to display Donate button

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];

    // Prepare the SQL query to check for campaigns based on user type
    if ($user_type == 'user') {
        // For normal users, check campaigns linked by normal_user_id
        $stmt = $conn->prepare("SELECT COUNT(*) FROM campaigns WHERE normal_user_id = ?");
    } elseif ($user_type == 'organization') {
        // For organizations, check campaigns linked by organization_id
        $stmt = $conn->prepare("SELECT COUNT(*) FROM campaigns WHERE organization_id = ?");
    } 
    elseif ($user_type == 'hospital') {
        // For hospitals, check campaigns linked by hospital_id
        $stmt = $conn->prepare("SELECT COUNT(*) FROM hospitals WHERE hospital_id = ?");
        $stmt->bind_param("i", $user_id); // Assuming $user_id is the hospital ID you obtained during login
        $show_blood_campaign_link = true; // Set flag to show blood campaign link
    }
    
    else {
        die("Invalid user type.");
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($campaign_count); // Bind the result to get the count
    $stmt->fetch();
    $stmt->close(); // Close the statement

    // Check if there are any campaigns
    if ($campaign_count > 0) {
        $show_manage_campaign_link = true; // Set flag to true if campaigns exist
    }

     // Check if the user is a donor by querying the donors table
     if ($user_type == 'user') {
        $donor_stmt = $conn->prepare("SELECT COUNT(*) FROM donors WHERE normal_user_id = ?");
        $donor_stmt->bind_param("i", $user_id);
        $donor_stmt->execute();
        $donor_stmt->bind_result($donor_count); // Bind the result to get the count
        $donor_stmt->fetch();
        $donor_stmt->close(); // Close the donor statement

        // If the user is a donor, set the flag to display the donate button
        if ($donor_count > 0) {
            $show_donate_button = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Bridge - Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Navbar background image */
        .navbar {
            background: url('your-background-image.jpg') no-repeat center center;
            background-size: cover;
        }

        /* Customize the brand name */
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
        }

        /* Customize the navbar links */
        .nav-link {
            color: #fff;
            font-size: 1.2rem;
        }

        /* Darken the navbar background for better text visibility */
        .navbar-dark {
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Dropdown Menu */
        .dropdown-menu {
            background-color: rgba(0, 0, 0, 0.9);
            border: none;
        }

        .dropdown-menu a {
            color: #fff;
        }

        /* Show the dropdown on hover */
        .nav-item:hover .dropdown-menu {
            display: block;
        }

        /* Adjust dropdown positioning */
        .dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0; /* remove gap */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Life Bridge</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <!-- About Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item" href="about_us.html">About Us</a></li>
                        <li><a class="dropdown-item" href="how_it_works.html">How It Works</a></li>
                        <li><a class="dropdown-item" href="benefits_features.html">Benefits & Features</a></li>
                        <li><a class="dropdown-item" href="faqs.html">FAQs</a></li>
                        <li><a class="dropdown-item" href="contact_us.html">Contact Us</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="funding.php">Funding</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blood_home.php">Blood</a>
                </li>
                <!-- Conditional Signup/Logout Button -->
                <li class="nav-item">
                    <?php if ($is_logged_in): ?>
                       <li>
                       <a class="nav-link" href="logout.php">Logout</a>
                </li>
                <li>
                <a class="nav-link" href="profile.php">Profile</a>
                </li>
                    <?php else: ?>
                        <a class="nav-link" href="signup.php">Signup</a>
                    <?php endif; ?>
                </li>
                <!-- Manage Campaign Link -->
                <?php if ($show_manage_campaign_link && $user_type!='hospital'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_campaign.php">Your Campaign</a>
                    </li>
                <?php endif; ?>

                    <!-- Donate Button -->
                    <?php if ($show_donate_button): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="donar_update.php">Donor Updates</a>
                    </li>
                <?php endif; ?>


                     <!-- Manage Campaign Link -->
                     <?php if ($show_blood_campaign_link): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_blood_campaign.php">Blood Campaign</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS (for responsive behavior) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
