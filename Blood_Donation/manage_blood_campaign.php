<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Assuming the hospital_id is stored in the session
$hospital_id = $_SESSION['user_id']; // Adjust if the user_id is not the same as hospital_id

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Function to display the blood availability
function displayBloodAvailability($blood_availability) {
    echo '
    <div class="blood-inventory">
        <h3>Available Blood Quantity</h3>
        <table>
            <tr>
                <th>Blood Type</th>
                <th>Quantity Available</th>
            </tr>';
            
    // Check for available blood types and display them
    if ($blood_availability['A_plus'] > 0) {
        echo '
            <tr>
                <td>A+</td>
                <td>' . $blood_availability['A_plus'] . '</td>
            </tr>';
    }
    if ($blood_availability['A_minus'] > 0) {
        echo '
            <tr>
                <td>A-</td>
                <td>' . $blood_availability['A_minus'] . '</td>
            </tr>';
    }
    if ($blood_availability['B_plus'] > 0) {
        echo '
            <tr>
                <td>B+</td>
                <td>' . $blood_availability['B_plus'] . '</td>
            </tr>';
    }
    if ($blood_availability['B_minus'] > 0) {
        echo '
            <tr>
                <td>B-</td>
                <td>' . $blood_availability['B_minus'] . '</td>
            </tr>';
    }
    if ($blood_availability['AB_plus'] > 0) {
        echo '
            <tr>
                <td>AB+</td>
                <td>' . $blood_availability['AB_plus'] . '</td>
            </tr>';
    }
    if ($blood_availability['AB_minus'] > 0) {
        echo '
            <tr>
                <td>AB-</td>
                <td>' . $blood_availability['AB_minus'] . '</td>
            </tr>';
    }
    if ($blood_availability['O_plus'] > 0) {
        echo '
            <tr>
                <td>O+</td>
                <td>' . $blood_availability['O_plus'] . '</td>
            </tr>';
    }
    if ($blood_availability['O_minus'] > 0) {
        echo '
            <tr>
                <td>O-</td>
                <td>' . $blood_availability['O_minus'] . '</td>
            </tr>';
    }

    echo '
        </table>
        <div class="manage-buttons">
            <button class="manage-button" onclick="location.href=\'manage_donors.php\'">Manage Donors</button>
            <button class="manage-button" onclick="location.href=\'manage_donor_requests.php\'">Manage Requests</button>
            <button class="manage-button" onclick="location.href=\'manage_inventory.php\'">Manage Blood Inventory</button>
            <button class="manage-button" onclick="location.href=\'manage_donation_status.php\'">Manage Donation Status</button> 
        </div>
    </div>';
}

// Query to check blood availability
$query = "SELECT * FROM blood_availability WHERE hospital_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Blood availability record found
        $blood_availability = $result->fetch_assoc();
        // Display available blood quantity
        displayBloodAvailability($blood_availability);
    } else {
    
        echo "<script>
        alert('No blood availability records found for this hospital need to add your Inventory');
        window.location.href = 'add_blood_quantity.php';
    </script>";
   
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

<!-- Add this CSS for styling -->
<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f9f9f9;
}

h3 {
    color: #d9534f; /* Bootstrap Danger Color */
    margin-bottom: 15px;
}

.blood-inventory {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    max-width: 600px; /* Limit max width for better alignment */
    margin: 10px auto; /* Center the inventory section */
}

.blood-inventory table {
    width: 100%; /* Full width for the table */
    border-collapse: collapse;
    margin-top: 15px; /* Margin to separate from heading */
}

.blood-inventory table, .blood-inventory th, .blood-inventory td {
    border: 1px solid #ddd;
}

.blood-inventory th, .blood-inventory td {
    padding: 10px;
    text-align: center;
}

.blood-inventory th {
    background-color: #d9534f; /* Bootstrap Danger Color */
    color: white;
}

.manage-buttons {
    margin-top: 15px; /* Margin above the buttons */
    display: flex;
    justify-content: space-between; /* Space buttons evenly */
}

.blood-inventory .manage-button {
    background-color: #5bc0de; /* Bootstrap Info Color */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    flex: 1; /* Allow buttons to grow */
    margin: 0 5px; /* Space between buttons */
    transition: background-color 0.3s; /* Smooth background change */
}

.blood-inventory .manage-button:hover {
    background-color: #31b0d5; /* Darker blue */
}
</style>
