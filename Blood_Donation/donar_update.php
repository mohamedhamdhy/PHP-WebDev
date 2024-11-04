<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Prepare the SQL query to fetch donor details
$stmt = $conn->prepare("SELECT * FROM donors WHERE normal_user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if donor details exist
if ($result->num_rows > 0) {
    $donor = $result->fetch_assoc(); // Fetch the donor details
} else {
    echo "No donor details found.";
    exit;
}
$stmt->close(); // Close the statement
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        .donor-details {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        .donor-detail-label {
            font-weight: bold;
            color: #495057;
        }

        .action-buttons {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Donor Details</h2>

        <div class="donor-details">
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Name:</span> <?php echo htmlspecialchars($donor['donor_name']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">NIC:</span> <?php echo htmlspecialchars($donor['donor_nic']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Phone:</span> <?php echo htmlspecialchars($donor['donor_phone']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Email:</span> <?php echo htmlspecialchars($donor['donor_email']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Blood Type:</span> <?php echo htmlspecialchars($donor['blood_type']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Gender:</span> <?php echo htmlspecialchars($donor['gender']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Weight:</span> <?php echo htmlspecialchars($donor['weight']); ?> kg
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Health Conditions:</span> <?php echo htmlspecialchars($donor['health_conditions']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Medications:</span> <?php echo htmlspecialchars($donor['medications']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Last Donation Date:</span> <?php echo htmlspecialchars($donor['last_donation_date']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Emergency Contact:</span> <?php echo htmlspecialchars($donor['emergency_contact']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Emergency Relationship:</span> <?php echo htmlspecialchars($donor['emergency_relationship']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Emergency Phone:</span> <?php echo htmlspecialchars($donor['emergency_phone']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Preferred Donation Date:</span> <?php echo htmlspecialchars($donor['preferred_donation_date']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">District:</span> <?php echo htmlspecialchars($donor['district']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <span class="donor-detail-label">Hospital:</span> <?php echo htmlspecialchars($donor['hospital']); ?>
                </div>
                <div class="col-md-4">
                    <span class="donor-detail-label">Donation Request Status:</span> <?php echo htmlspecialchars($donor['donation_req_status']); ?>
                </div>
            </div>
        </div>

        <div class="action-buttons">
    <form action="update_donor.php" method="POST" style="display:inline;">
        <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donor['donor_id']); ?>">
        <button type="submit" class="btn btn-warning">Edit</button>
    </form>
    <form action="delete_donor.php" method="POST" style="display:inline;">
        <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donor['donor_id']); ?>">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this donor?');">Delete</button>
    </form>

    <!-- Check if the donation request status is 'confirm' -->
    <?php if (htmlspecialchars($donor['donation_req_status']) === 'confirm'): ?>
        <form action="download_qr.php" method="POST" style="display:inline;">
            <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donor['donor_id']); ?>">
            <button type="submit" class="btn btn-success">Download QR</button>
        </form>
    <?php endif; ?>
</div>

<span class="donor-detail-label">Donation Request Status:</span> <?php echo htmlspecialchars($donor['donation_req_status']); ?>

    </div>

    <!-- Bootstrap JS (for responsive behavior) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
