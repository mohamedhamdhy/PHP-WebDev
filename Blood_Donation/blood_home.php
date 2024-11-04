<?php 
session_start(); // Ensure session is started

include("nav.php");

// Check if the user is logged in and get the user type
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Redirect to login if not logged in
    header("Location: signup.php");
    exit();
}

// Connect to the database
include('db.php'); // assuming you have this for connection

// Initialize filter variables
$bloodTypeFilter = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$districtFilter = isset($_GET['blood_district']) ? $_GET['blood_district'] : '';
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch blood requests from the blood_requests_org table with filters
$queryOrg = "SELECT organization_name, blood_type, quantity, request_date, organization_phone, delivery_address, org_blood_image,id FROM blood_requests_org WHERE 1=1";

// Apply filters to the organization query
if ($bloodTypeFilter != '') {
    $queryOrg .= " AND blood_type = '$bloodTypeFilter'";
}
if ($districtFilter != '') {
    $queryOrg .= " AND blood_district = '$districtFilter'";
}
if ($searchFilter != '') {
    $queryOrg .= " AND (organization_name LIKE '%$searchFilter%' OR blood_type LIKE '%$searchFilter%' OR delivery_address LIKE '%$searchFilter%')";
}

$resultOrg = mysqli_query($conn, $queryOrg);

// Fetch blood requests from the blood_requests table with filters
$queryUser = "SELECT name, age, blood_type, location, contact, reason, request_date, quantity, delivery_address, blood_image,id FROM blood_requests WHERE 1=1";

// Apply filters to the user query
if ($bloodTypeFilter != '') {
    $queryUser .= " AND blood_type = '$bloodTypeFilter'";
}
if ($districtFilter != '') {
    $queryUser .= " AND blood_district = '$districtFilter'";
}
if ($searchFilter != '') {
    $queryUser .= " AND (name LIKE '%$searchFilter%' OR blood_type LIKE '%$searchFilter%' OR location LIKE '%$searchFilter%')";
}

$resultUser = mysqli_query($conn, $queryUser);
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

        /* Card Styles */
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden; /* Ensure image fits the card */
        }

        .card-body {
            text-align: center;
        }

        .card-title {
            font-size: 18px;
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

        /* Styling for the image */
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
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

        <!-- Filter Section -->
        <form method="GET" action="" class="row mt-5 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, address, etc." value="<?= $searchFilter ?>">
            </div>
            <div class="col-md-3">
                <select name="blood_type" class="form-select">
                    <option value="">Select Blood Type</option>
                    <option value="A+" <?= $bloodTypeFilter == 'A+' ? 'selected' : '' ?>>A+</option>
                    <option value="A-" <?= $bloodTypeFilter == 'A-' ? 'selected' : '' ?>>A-</option>
                    <option value="B+" <?= $bloodTypeFilter == 'B+' ? 'selected' : '' ?>>B+</option>
                    <option value="B-" <?= $bloodTypeFilter == 'B-' ? 'selected' : '' ?>>B-</option>
                    <option value="O+" <?= $bloodTypeFilter == 'O+' ? 'selected' : '' ?>>O+</option>
                    <option value="O-" <?= $bloodTypeFilter == 'O-' ? 'selected' : '' ?>>O-</option>
                    <option value="AB+" <?= $bloodTypeFilter == 'AB+' ? 'selected' : '' ?>>AB+</option>
                    <option value="AB-" <?= $bloodTypeFilter == 'AB-' ? 'selected' : '' ?>>AB-</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="blood_district" class="form-select">
                    <option value="">Select District</option>
                    <option value="District 1" <?= $districtFilter == 'District 1' ? 'selected' : '' ?>>District 1</option>
                    <option value="District 2" <?= $districtFilter == 'District 2' ? 'selected' : '' ?>>District 2</option>
                    <option value="District 3" <?= $districtFilter == 'District 3' ? 'selected' : '' ?>>District 3</option>
                    <!-- Add more district options as per your database -->
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100">Search</button>
            </div>
        </form>

        <!-- Blood Requests Grid -->
        <h2 class="mt-5">Blood Requests</h2>
        <div class="row">
            <?php 
            // Display organization requests
            while ($rowOrg = mysqli_fetch_assoc($resultOrg)) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= $rowOrg['org_blood_image'] ?>" class="card-img-top" alt="Blood Request Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= $rowOrg['organization_name'] ?></h5>
                            <p class="card-text">
                            Campaign id: <?= $rowOrg['id'] ?><br>
                                Blood Type: <?= $rowOrg['blood_type'] ?><br>
                                Quantity: <?= $rowOrg['quantity'] ?><br>
                                Date: <?= $rowOrg['request_date'] ?><br>
                                Address: <?= $rowOrg['delivery_address'] ?><br>
                                Contact: <?= $rowOrg['organization_phone'] ?>
                            </p>
                            <a href="helpnow.php?id=<?= $rowOrg['id'] ?>" class="btn btn-success">Help Now</a> <!-- Help Now Button -->
                        </div>
                    </div>
                </div>
            <?php }

            // Display user requests
            while ($rowUser = mysqli_fetch_assoc($resultUser)) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= $rowUser['blood_image'] ?>" class="card-img-top" alt="Blood Request Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= $rowUser['name'] ?> (Age: <?= $rowUser['age'] ?>)</h5>
                            <p class="card-text">
                            Campaing ID: <?= $rowUser['id'] ?><br>
                                Blood Type: <?= $rowUser['blood_type'] ?><br>
                                Quantity: <?= $rowUser['quantity'] ?><br>
                                Date: <?= $rowUser['request_date'] ?><br>
                                Address: <?= $rowUser['delivery_address'] ?><br>
                                Contact: <?= $rowUser['contact'] ?><br>
                                Reason: <?= $rowUser['reason'] ?>
                            </p>
                            <a href="helpnow.php?id=<?= $rowUser['id'] ?>" class="btn btn-success">Help Now</a> <!-- Help Now Button -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php } 
            ?>
        </div>
    </div>
</body>
</html>
