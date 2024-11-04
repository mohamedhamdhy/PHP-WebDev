<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Check if donor_id is set in the URL
if (isset($_GET['donor_id'])) {
    $donor_id = intval($_GET['donor_id']); // Sanitize input to prevent SQL injection
    
    // Query to fetch donor details
    $query = "SELECT d.*, u.normal_user_firstname, u.normal_user_lastname, u.normal_user_profile_picture 
              FROM donors d 
              JOIN normal_user u ON d.normal_user_id = u.normal_user_id 
              WHERE d.donor_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $donor = $result->fetch_assoc();
            // Display donor details
            echo '
            <div class="donor-details">
                <h2>Donor Details</h2>
                <img src="' . htmlspecialchars($donor['normal_user_profile_picture']) . '" alt="Profile Picture" class="profile-image">
                <p><strong>Name:</strong> ' . htmlspecialchars($donor['donor_name']) . '</p>
                <p><strong>NIC:</strong> ' . htmlspecialchars($donor['donor_nic']) . '</p>
                <p><strong>Phone:</strong> ' . htmlspecialchars($donor['donor_phone']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($donor['donor_email']) . '</p>
                <p><strong>Blood Type:</strong> ' . htmlspecialchars($donor['blood_type']) . '</p>
                <p><strong>Gender:</strong> ' . htmlspecialchars($donor['gender']) . '</p>
                <p><strong>Weight:</strong> ' . htmlspecialchars($donor['weight']) . ' kg</p>
                <p><strong>Health Conditions:</strong> ' . htmlspecialchars($donor['health_conditions']) . '</p>
                <p><strong>Medications:</strong> ' . htmlspecialchars($donor['medications']) . '</p>
                <p><strong>Last Donation Date:</strong> ' . htmlspecialchars($donor['last_donation_date']) . '</p>
                <p><strong>Emergency Contact:</strong> ' . htmlspecialchars($donor['emergency_contact']) . '</p>
                <p><strong>Emergency Relationship:</strong> ' . htmlspecialchars($donor['emergency_relationship']) . '</p>
                <p><strong>Emergency Phone:</strong> ' . htmlspecialchars($donor['emergency_phone']) . '</p>
                <p><strong>Preferred Donation Date:</strong> ' . htmlspecialchars($donor['preferred_donation_date']) . '</p>
                <p><strong>District:</strong> ' . htmlspecialchars($donor['district']) . '</p>
                <p><strong>Hospital:</strong> ' . htmlspecialchars($donor['hospital']) . '</p>
                <p><strong>Donation Request Status:</strong> ' . htmlspecialchars($donor['donation_req_status']) . '</p>
                <p><strong>Created At:</strong> ' . htmlspecialchars($donor['created_at']) . '</p>
                <a href="manage_donors.php" class="back-button">Back to Donors List</a>
            </div>';
        } else {
            echo "<h2>No donor found with this ID.</h2>";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No donor ID provided.";
}

// Close the database connection
$conn->close();
?>

<!-- Add this CSS for styling -->
<style>
.donor-details {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    max-width: 800px; /* Limit max width for better alignment */
    margin: 20px auto; /* Center the details section */
}

.profile-image {
    width: 100px; /* Adjust size as necessary */
    height: 100px; /* Adjust size as necessary */
    border-radius: 50%; /* Circular images */
}

.back-button {
    display: inline-block;
    background-color: #d9534f; /* Danger Color */
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.back-button:hover {
    background-color: #c9302c; /* Darker red */
}
</style>
