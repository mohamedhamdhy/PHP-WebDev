<?php
session_start();
include('db.php'); // Include your database connection file

// Handle status update
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['delivery_status'];

    $update_query = "UPDATE blood_requests_org SET delivery_status = '$new_status' WHERE id = '$request_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Status updated successfully!');</script>";
        echo "<script>window.location.href = 'manage_organization_requests.php';</script>"; // Refresh page
    } else {
        echo "<script>alert('Error updating status: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle deletion of an organization request
if (isset($_POST['delete_request'])) {
    $request_id = $_POST['request_id'];
    $delete_query = "DELETE FROM blood_requests_org WHERE id = '$request_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Organization request deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_organization_requests.php';</script>"; // Refresh page
    } else {
        echo "<script>alert('Error deleting organization request: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all organization requests from the database
$query_org_requests = "SELECT * FROM blood_requests_org ORDER BY org_blood_district ASC"; // Order by org_blood_district
$result_org_requests = $conn->query($query_org_requests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Organization Blood Requests</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
    <style>
        /* Basic styles for the table and actions */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-update {
            background-color: #28a745;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .status-select {
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Organization Blood Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Organization Name</th>
                <th>Registration Number</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Reason</th>
                <th>Blood Type</th>
                <th>Quantity</th>
                <th>Request Date</th>
                <th>Delivery</th>
                <th>Delivery Address</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_org_requests->num_rows > 0): ?>
                <?php while ($request = $result_org_requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['organization_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['organization_registration_number']); ?></td>
                        <td><?php echo htmlspecialchars($request['organization_address']); ?></td>
                        <td><?php echo htmlspecialchars($request['organization_phone']); ?></td>
                        <td><?php echo htmlspecialchars($request['reason']); ?></td>
                        <td><?php echo htmlspecialchars($request['blood_type']); ?></td>
                        <td><?php echo htmlspecialchars($request['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                        <td><?php echo htmlspecialchars($request['delivery']); ?></td>
                        <td><?php echo htmlspecialchars($request['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($request['delivery_status']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <!-- Update Status Form -->
                                <form method="post" action="">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <select name="delivery_status" class="status-select">
                                        <option value="Pending" <?php echo ($request['delivery_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Approved" <?php echo ($request['delivery_status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Cancelled" <?php echo ($request['delivery_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-update">Update</button>
                                </form>
                                <!-- Delete Form -->
                                <form method="post" action="">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="delete_request" class="btn btn-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12">No organization requests available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
mysqli_close($conn);
?>
