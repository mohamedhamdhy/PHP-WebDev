<?php
include("db.php");
session_start(); // Ensure session is started

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user's NIC from session or database
$userId = $_SESSION['user_id'];
$nic = ""; // Initialize NIC variable

// Fetch user NIC based on the user type
if ($_SESSION['user_type'] === 'user') {
    $stmt = $conn->prepare("SELECT NIC FROM normal_user WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($nic);
    $stmt->fetch();
    $stmt->close();
} elseif ($_SESSION['user_type'] === 'organization') {
    // If the user is an organization, handle accordingly
    echo "Organizations do not have requests.";
    exit();
}

// Query the requests table based on the NIC
$requestQuery = "SELECT * FROM requests WHERE nic = ?";
$requestStmt = $conn->prepare($requestQuery);
$requestStmt->bind_param("s", $nic);
$requestStmt->execute();
$result = $requestStmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Requests</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Your Blood Donation Requests</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
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
                        <th>Last Donated</th>
                        <th>Weight</th>
                        <th>Blood Pressure</th>
                        <th>Medical Issues</th>
                        <th>Action</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['blood_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['delivery_instructions']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_donated']); ?></td>
                            <td><?php echo htmlspecialchars($row['weight']); ?></td>
                            <td><?php echo htmlspecialchars($row['blood_pressure']); ?></td>
                            <td><?php echo htmlspecialchars($row['medical_issues']); ?></td>
                            <td>
                                <a href="edit_request.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                                <a href="delete_request.php?id=<?php echo $row['id']; ?>">Delete</a>
                            </td>
                            <td><?php echo htmlspecialchars($row['delivery_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No requests found for your NIC number.</p>
        <?php endif; ?>

        <a href="index.php">Go back to Home</a>
    </div>
</body>
</html>

<?php
$requestStmt->close();
$conn->close();
?>
