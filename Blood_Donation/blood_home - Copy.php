<?php 
session_start(); // Ensure session is started

include("nav.php");

// Check if the user is logged in and get the user type
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation</title>
    <!-- Bootstrap CDN for quick styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for the page */
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .navbar-nav .nav-link {
            color: white !important;
        }

        .hero-section {
            position: relative;
            background-image: url('https://example.com/blood-donation-bg.jpg'); /* Replace with your image URL */
            height: 400px;
            background-size: cover;
            background-position: center;
            text-align: center;
            color: white;
        }

        .hero-section .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .hero-section .content {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }

        .hero-section .content h1 {
            font-size: 48px;
            font-weight: bold;
        }

        .hero-section .btn {
            margin-top: 20px;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .card-body {
            text-align: center;
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
        }

        h2 {
            margin-top: 30px;
            text-align: center;
            color: #333;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        .container {
            margin-top: 20px;
        }

        .cta-section {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="overlay"></div>
        <div class="content">
            <h1>Donate Blood, Save Lives</h1>
            <a href="donation.php" class="btn btn-success btn-lg">Give Donation</a>
            <a href="request.php" class="btn btn-success btn-lg">Request Donation</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="container">
        <div class="cta-section">
            <h2>Become a Donor Today!</h2>
            <p>Ready to make a difference? Sign up today and help save lives in your community.</p>
            <a href="donor_reg.php" class="btn btn-success btn-lg">Register as a Donor</a>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Why Donate Blood?</h5>
                        <p class="card-text">Your donation can save up to three lives. Become a hero today by giving blood.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Blood Types Needed</h5>
                        <p class="card-text">We urgently need donors with O-, O+, and B+ blood types. Check your blood type and donate now.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Find a Donation Center</h5>
                        <p class="card-text">Locate the nearest blood donation center and book an appointment at your convenience.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Sections -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2>FAQs About Blood Donation</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">1. Who can donate blood?</h5>
                        <p class="card-text">Anyone between the ages of 18 and 65, in good health, and weighing at least 50 kg can donate blood.</p>
                        
                        <h5 class="card-title">2. How often can I donate?</h5>
                        <p class="card-text">You can donate whole blood every 56 days, or about every 2 months.</p>
                        
                        <h5 class="card-title">3. Is blood donation safe?</h5>
                        <p class="card-text">Yes, blood donation is a safe procedure. Sterile equipment is used, and the risk of infection is very low.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Statistics</h2>
                <div class="card">
                    <div class="card-body">
                        <ul>
                            <li>1 donation can save up to 3 lives.</li>
                            <li>Every 2 seconds, someone in the U.S. needs blood.</li>
                            <li>Approximately 36,000 units of red blood cells are needed every day.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Success Stories</h2>
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">“I donated blood for the first time last year, and I felt amazing knowing that I could help someone in need. I encourage everyone to consider donating.” - John D.</p>
                        <p class="card-text">“Thanks to blood donors, I received the transfusion I needed to survive a complicated surgery. I am forever grateful!” - Sarah K.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Tips for Donors</h2>
                <div class="card">
                    <div class="card-body">
                        <ul>
                            <li>Stay hydrated before and after donating.</li>
                            <li>Eat a healthy meal before donating.</li>
                            <li>Wear a shirt with sleeves that can be easily rolled up.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (for responsive navbar) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
