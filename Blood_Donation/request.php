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
    <title>Blood Donation Request</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff5f5;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #c0392b;
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }

        .hero-section {
            position: relative;
            background-color: #e74c3c;
            color: white;
            text-align: center;
            padding: 50px 0;
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 20px;
        }

        /* Video Section */
        .video-section {
            position: relative;
            padding: 60px;
            background-color: #fff5f5;
        }

        .video-section iframe {
            width: 100%;
            height: 450px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .video-section h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 30px;
        }

        .button-section {
            text-align: center;
            margin-top: 40px;
        }

        .btn-request {
            background-color: #e74c3c;
            color: white;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 18px;
            margin: 10px;
            transition: transform 0.3s;
        }

        .btn-request:hover {
            transform: scale(1.1);
        }

        /* Cards and Testimonials */
        .info-section {
            padding: 60px;
            background-color: #ffebee;
        }

        .info-card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .testimonial-section {
            padding: 60px;
            background-color: #fdf1f0;
        }

        .testimonial {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* FAQ */
        .faq-section {
            padding: 40px 60px;
        }

        .faq-item h5 {
            font-size: 20px;
            font-weight: bold;
        }

        .faq-item p {
            color: #7f8c8d;
        }

        /* Footer */
        .footer {
            background-color: #c0392b;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .footer a {
            color: #ffdfdf;
        }

    </style>
</head>

<body>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Request Blood Donation</h1>
        <p>Submit your request and help save lives by finding a blood match.</p>
    </div>

    <!-- Video Section -->
    <div class="video-section">
        <h2>Why Blood Donation Matters</h2>
        <iframe src="https://www.youtube.com/embed/someVideoURL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>

    <!-- Button Section -->
    <div class="button-section">
        <?php if ($userType === 'user'): ?>
            <a href="blood.php" class="btn btn-request">Make a  Request </a>
        <?php elseif ($userType === 'organization'): ?>
            <a href="blood_org.php" class="btn btn-request">Make a  Request </a>
        <?php else: ?>
            <p class="text-danger">Invalid user type.</p>
        <?php endif; ?>
    </div>
    
    <div class="button-section">
        <?php if ($userType === 'organization'): ?>
            <a href="manage_organization_requests.php" class="btn btn-request">Manage Organization Requests</a>
        <?php elseif ($userType === 'user'): ?>
            <a href="manage_requests.php" class="btn btn-request">Manage Your Requests</a>
        <?php else: ?>
            <p class="text-danger">Invalid user type.</p>
        <?php endif; ?>
    </div>

    <!-- Information Section -->
    <div class="info-section">
        <div class="row">
            <div class="col-md-4">
                <div class="info-card">
                    <h3>Why Donate Blood?</h3>
                    <p>Every two seconds, someone needs blood. Your donation can save lives and create a lasting impact.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <h3>How to Donate?</h3>
                    <p>Register, find a donation center near you, and make an appointment to donate blood easily.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <h3>Who Can Donate?</h3>
                    <p>Healthy individuals aged 18-65 and weighing over 50kg are eligible to donate blood.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section -->
    <div class="testimonial-section">
        <h2>What People Are Saying</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial">
                    <p>"Donating blood is fulfilling. Knowing I helped save lives is priceless."</p>
                    <div class="name">- Sarah L.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial">
                    <p>"Blood donors saved my life. I am forever thankful to those who donate regularly."</p>
                    <div class="name">- Michael R.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial">
                    <p>"A simple blood donation can make a huge impact. Everyone should consider it."</p>
                    <div class="name">- David S.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
            <h5>How often can I donate blood?</h5>
            <p>Men can donate every 12 weeks, while women can donate every 16 weeks.</p>
        </div>
        <div class="faq-item">
            <h5>Will donating blood hurt?</h5>
            <p>You may feel a slight pinch, but the process is generally painless.</p>
        </div>
        <div class="faq-item">
            <h5>What should I do after donating?</h5>
            <p>Rest, drink water, and avoid strenuous activities for a few hours after donating.</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Blood Donation Platform. All rights reserved. <a href="#">Privacy Policy</a></p>
    </div>

</body>

</html>
