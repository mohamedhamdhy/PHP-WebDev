<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}
$hospitalId = $_SESSION['user_id']; // Ensure this is the correct way to access the hospital ID
// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Query to fetch all donor requests
$query = "SELECT * FROM donors WHERE hospital = '$hospitalId' ORDER BY created_at DESC"; // Order by latest requests


if ($result = $conn->query($query)) {
    if ($result->num_rows > 0) {
        echo '
        <div class="donors-requests-list">
            <h3>Donor Requests</h3>
            <table>
                <tr>
                    <th>Donor Name</th>
                    <th>Blood Type</th>
                    <th>Contact</th>
                    <th>Preferred Donation Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>';
        
        // Loop through the results and display donor requests
        while ($row = $result->fetch_assoc()) {
            $status = htmlspecialchars($row['donation_req_status']); // Get current status
            $statusDisplay = ucfirst($status); // Capitalize status
            echo '
                <tr>
                    <td>' . htmlspecialchars($row['donor_name']) . '</td>
                    <td>' . htmlspecialchars($row['blood_type']) . '</td>
                    <td>Phone: ' . htmlspecialchars($row['donor_phone']) . '<br>Email: ' . htmlspecialchars($row['donor_email']) . '</td>
                    <td>' . htmlspecialchars($row['preferred_donation_date']) . '</td>
                    <td>' . $statusDisplay . '</td>
                    <td>
                        <a href="donor_details.php?donor_id=' . $row['donor_id'] . '" class="action-button">View Details</a>';

            // Only show "Cancel" or "Confirm" buttons if the status is not yet confirmed or canceled
            if ($status !== 'confirm') {
                echo '<button class="action-button" onclick="updateStatus(' . $row['donor_id'] . ', \'confirm\')">Confirm</button>';
            }
            if ($status !== 'cancelled') {
                echo '<button class="action-button" onclick="updateStatus(' . $row['donor_id'] . ', \'cancelled\')">Cancel</button>';
            }

            echo '</td>
                </tr>';
        }
        
        echo '
            </table>
        </div>';
    } else {
        echo "<h2>No donor requests found.</h2>";
    }
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

<!-- Add this CSS for styling -->
<style>
.donors-requests-list {
    margin: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}
h3 {
    color: #333;
    font-family: Arial, sans-serif;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
.action-button {
    background-color: #007bff; /* Primary Color */
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin: 5px;
    text-decoration: none;
}
.action-button:hover {
    background-color: #0056b3;
}
</style>

<script>
// Function to handle status updates via AJAX or redirection
function updateStatus(donorId, status) {
    if (confirm("Are you sure you want to " + status + " this donor request?")) {
        // Redirect to update donor status page with donor_id and new status
        window.location.href = 'update_donor_status.php?donor_id=' + donorId + '&status=' + status;
    }
}
</script>
