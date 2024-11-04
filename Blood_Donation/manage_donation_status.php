<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Fetch upcoming donations from donors, filtering out those who have already donated
// Assuming you have already started the session and included the database connection
$hospitalId = $_SESSION['user_id']; // Assuming user_id corresponds to hospital_id

// Fetch upcoming donations
$query_upcoming = "SELECT d.*, u.normal_user_profile_picture 
                   FROM donors d 
                   JOIN normal_user u ON d.normal_user_id = u.normal_user_id 
                   WHERE d.preferred_donation_date >= CURDATE() 
                   AND (d.donate_update IS NULL OR d.donate_update <> 'donated') 
                   AND d.hospital = '$hospitalId' 
                   ORDER BY d.preferred_donation_date ASC"; // Adjust the condition based on your needs


// Fetch past donations from donors
// Fetch past donations for the specific hospital
$query_past = "SELECT d.*, u.normal_user_profile_picture 
               FROM donors d 
               JOIN normal_user u ON d.normal_user_id = u.normal_user_id 
               WHERE (d.donate_update IS NULL OR d.donate_update = 'donated') 
               AND d.hospital = '$hospitalId' 
               ORDER BY d.preferred_donation_date DESC"; // Adjust the condition based on your needs

// Fetch upcoming donations
if ($result_upcoming = $conn->query($query_upcoming)) {
    if ($result_upcoming->num_rows > 0) {
        echo '
        <div class="donation-status-list">
            <h3>Upcoming Donations</h3>
            <table>
                <tr>
                    <th>Profile Picture</th>
                    <th>Donor Name</th>
                    <th>Blood Type</th>
                    <th>Preferred Donation Date</th>
                    <th>Last Donation Date</th>
                    <th>Donation Status</th> <!-- Add header for donate_update -->
                    <th>Action</th>
                </tr>';
        
        // Loop through the results and display donor details for upcoming donations
        while ($row = $result_upcoming->fetch_assoc()) {
            echo '
                <tr>
                    <td><img src="' . htmlspecialchars($row['normal_user_profile_picture']) . '" alt="Profile Picture" class="profile-image"></td>
                    <td>' . htmlspecialchars($row['donor_name']) . '</td>
                    <td>' . htmlspecialchars($row['blood_type']) . '</td>
                    <td>' . htmlspecialchars($row['preferred_donation_date']) . '</td>
                    <td>' . htmlspecialchars($row['last_donation_date']) . '</td>
                    <td>' . htmlspecialchars($row['donate_update']) . '</td> <!-- Display donate_update status -->
                    <td>
                        <button class="action-button" onclick="updateDonationStatus(' . $row['donor_id'] . ', \'' . $row['blood_type'] . '\')">Mark as Donated</button>
                    </td>
                </tr>';
        }
        
        echo '
            </table>
        </div>';
    } 
    
    else {
        echo "<h2>No Upcoming donations found.</h2>";
    }
}

// Fetch past donations
if ($result_past = $conn->query($query_past)) {
    if ($result_past->num_rows > 0) {
        echo '
        <div class="donation-status-list">
            <h3>Past Donations</h3>
            <table>
                <tr>
                    <th>Profile Picture</th>
                    <th>Donor Name</th>
                    <th>Blood Type</th>
                    <th>Preferred Donation Date</th>
                    <th>Last Donation Date</th>
                    <th>Donation Status</th>
                </tr>';
        
        // Loop through the results and display donor details for past donations
        while ($row = $result_past->fetch_assoc()) {
            echo '
                <tr>
                    <td><img src="' . htmlspecialchars($row['normal_user_profile_picture']) . '" alt="Profile Picture" class="profile-image"></td>
                    <td>' . htmlspecialchars($row['donor_name']) . '</td>
                    <td>' . htmlspecialchars($row['blood_type']) . '</td>
                    <td>' . htmlspecialchars($row['preferred_donation_date']) . '</td>
                    <td>' . htmlspecialchars($row['last_donation_date']) . '</td>
                    <td>' . htmlspecialchars($row['donate_update']) . '</td> <!-- Display donate_update status -->
                </tr>';
        }
        
        echo '
            </table>
        </div>';
    } else {
        echo "<h2>No past donations found.</h2>";
    }
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

<!-- Add this CSS for styling -->
<style>
.donation-status-list {
    margin: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}

.profile-image {
    width: 50px; /* Adjust size as needed */
    height: 50px; /* Adjust size as needed */
    border-radius: 50%; /* Make it circular */
}

.action-button {
    background-color: #4CAF50; /* Green */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.action-button:hover {
    background-color: #45a049; /* Darker green on hover */
}
</style>

<script>
function updateDonationStatus(donorId, bloodType) {
    if (confirm("Are you sure you want to mark this donation as donated?")) {
        // Make an AJAX request to update the donation status
        window.location.href = 'update_donation.php?donor_id=' + donorId + '&blood_type=' + bloodType;
    }
}
</script>
