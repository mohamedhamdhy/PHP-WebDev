<?php 
session_start(); // Ensure session is started

include("nav.php");
include 'db.php'; // Include your database connection

// Check if the user is logged in and get the user type
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Correctly retrieve the user ID
} else {
    echo "User ID not set in session.";
    exit();
}

// Fetch donor details using the user ID
$donorResult = $conn->query("SELECT * FROM donors WHERE user_id = '$userId'"); // Changed $user_id to $userId

if ($donorResult && $donorResult->num_rows > 0) {
    $donor = $donorResult->fetch_assoc();
    $donorId = $donor['donor_id']; // Assuming 'donor_id' is the field in your database
    $donorName = $donor['donor_name']; // Assuming 'name' is the field for donor's name
    $bloodType = $donor['blood_type']; // Assuming 'blood_type' is the field for blood type
    $contactInfo = $donor['donor_phone']; // Assuming 'contact_info' is the field for contact details
} else {
    echo "Donor not found.";
    exit();
}

// Fetch hospitals that accept donations for the donor's blood type
$hospitalResult = $conn->query("SELECT * FROM hospitals WHERE blood_type_accepted LIKE '%$bloodType%'");

if (!$hospitalResult) {
    echo "Error fetching hospitals: " . $conn->error;
    exit();
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Make a Donation</h2>
        
        <!-- Display Donor Details -->
        <p>Your Donor ID: <strong><?php echo htmlspecialchars($donorId); ?></strong></p>
        <p>Donor Name: <strong><?php echo htmlspecialchars($donorName); ?></strong></p>
        <p>Blood Type: <strong><?php echo htmlspecialchars($bloodType); ?></strong></p>
        <p>Contact Info: <strong><?php echo htmlspecialchars($contactInfo); ?></strong></p>

        <p>Select a hospital to donate blood:</p>
        <form action="process_donation.php" method="POST">
            <div class="mb-3">
                <label for="hospital" class="form-label">Select Hospital</label>
                <select name="hospital_id" id="hospital" class="form-select" required>
                    <option value="" disabled selected>Select a hospital</option>
                    <?php while($hospital = $hospitalResult->fetch_assoc()): ?>
                        <option value="<?php echo $hospital['hospital_id']; ?>">
                            <?php echo htmlspecialchars($hospital['hospital_name']); // Use htmlspecialchars for security ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donorId); ?>"> <!-- Updated variable name -->
            <button type="submit" class="btn btn-success">Submit Donation</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
