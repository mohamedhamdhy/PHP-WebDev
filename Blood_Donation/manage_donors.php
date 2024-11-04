<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}
$hospitalId = $_SESSION['user_id']; // Ensure this is the correct way to access the hospital ID

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Updated query to fetch only confirmed donor requests for the specific hospital
$query = "SELECT d.*, u.normal_user_profile_picture, u.normal_user_firstname, u.normal_user_lastname 
          FROM donors d 
          JOIN normal_user u ON d.normal_user_id = u.normal_user_id 
          WHERE d.donation_req_status = 'confirm' 
          AND d.hospital = '$hospitalId'  -- Assuming there's a hospital_id field in donors table
          ORDER BY d.created_at DESC"; // Filter to show only confirmed requests, ordering by created date


if ($result = $conn->query($query)) {
    if ($result->num_rows > 0) {
        echo '
        <div class="donors-list">
            <h3>Donors List</h3>
            <table>
                <tr>
                    <th>Profile Picture</th>
                    <th>Donor Name</th>
                    <th>Blood Type</th>
                    <th>Contact Details</th>
                    <th>Health Conditions</th>
                    <th>Action</th>
                </tr>';
        
        // Loop through the results and display donor details
        while ($row = $result->fetch_assoc()) {
            echo '
                <tr>
                    <td><img src="' . htmlspecialchars($row['normal_user_profile_picture']) . '" alt="Profile Picture" class="profile-image"></td>
                    <td>' . htmlspecialchars($row['donor_name']) . '</td>
                    <td>' . htmlspecialchars($row['blood_type']) . '</td>
                    <td>
                        Phone: ' . htmlspecialchars($row['donor_phone']) . '<br>
                        Email: ' . htmlspecialchars($row['donor_email']) . '
                    </td>
                    <td>' . htmlspecialchars($row['health_conditions']) . '</td>
                    <td>
                        <button class="action-button" onclick="deleteDonor(' . $row['donor_id'] . ')">Delete</button>
                        <a href="donor_details.php?donor_id=' . $row['donor_id'] . '" class="action-button">View Details</a>
                    </td>
                </tr>';
        }
        
        echo '
            </table>
        </div>';
    } else {
        echo "<h2>No donors found.</h2>";
    }
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

<!-- Add this CSS for styling -->
<style>
/* (Insert previous CSS here, and add any additional styles you need) */
.action-button {
    background-color: #d9534f; /* Danger Color */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin: 5px; /* Add some margin to the buttons */
}

    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 20px;
    }

    .donors-list {
        width: 80%;
        margin: 0 auto;
        background-color: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 20px;
    }

    h3 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table th, table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #4CAF50;
        color: white;
        font-weight: bold;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .profile-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .action-button {
        background-color: #d9534f;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin: 5px;
        text-decoration: none;
        display: inline-block;
    }

    .action-button:hover {
        background-color: #c9302c;
    }

    .action-button.view {
        background-color: #0275d8;
    }

    .action-button.view:hover {
        background-color: #025aa5;
    }

    .donors-list h2 {
        text-align: center;
        color: #777;
    }

    /* Add responsiveness */
    @media (max-width: 768px) {
        .donors-list {
            width: 100%;
        }

        table th, table td {
            padding: 10px;
        }

        .profile-image {
            width: 40px;
            height: 40px;
        }
    }


</style>

<script>
function deleteDonor(donorId) {
    if (confirm("Are you sure you want to delete this donor?")) {
        // Make an AJAX request to delete the donor
        window.location.href = 'delete_donor.php?donor_id=' + donorId;
    }
}
</script>
