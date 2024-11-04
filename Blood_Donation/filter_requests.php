<?php
include("db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Requests</title>
</head>
<body>

<h2>Your Blood Requests</h2>

<?php
// Check if NIC is provided via GET parameter
if (isset($_GET['nic'])) {
    $nic = $_GET['nic'];  // Get NIC from URL parameter

    // Query to fetch requests based on NIC number
    $sql = "SELECT * FROM blood_requests2 WHERE nic = ?";
    
    // Check if the SQL statement was prepared successfully
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters and execute
        $stmt->bind_param("s", $nic);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h3>Requests for NIC: " . htmlspecialchars($nic) . "</h3>";
            echo "<table border='1'>";
            echo "<tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Blood Type</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Reason</th>
                    <th>Request Date</th>
                    <th>Quantity</th>
                    <th>Delivery Address</th>
                    <th>Delivery Instructions</th>
                    <th>Delivery</th>
                    <th>Last Donated</th>
                    <th>Weight</th>
                    <th>Blood Pressure</th>
                    <th>Medical Issues</th>
                    <th>Action</th>
                    <th>Delivery Status</th>
                  </tr>";

            // Fetch and display the results
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['blood_type'] . "</td>";
                echo "<td>" . $row['location'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "<td>" . $row['reason'] . "</td>";
                echo "<td>" . $row['request_date'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . $row['delivery_address'] . "</td>";
                echo "<td>" . $row['delivery_instructions'] . "</td>";
                echo "<td>" . $row['delivery'] . "</td>";
                echo "<td>" . $row['last_donated'] . "</td>";
                echo "<td>" . $row['weight'] . "</td>";
                echo "<td>" . $row['blood_pressure'] . "</td>";
                echo "<td>" . $row['medical_issues'] . "</td>";
                echo "<td>" . $row['action'] . "</td>";
                echo "<td>" . $row['delivery_status'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No requests found for this NIC.</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        // SQL query failed to prepare, display error
        echo "<p>Error preparing query: " . $conn->error . "</p>";
    }
} else {
    // If NIC is not set in URL, prompt user to provide NIC
    echo "<p>No NIC provided. Please go back to the profile page and try again.</p>";
}
?>

</body>
</html>
