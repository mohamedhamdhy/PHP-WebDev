<?php
session_start();
include('db.php'); // Include your database connection file

// Handle deletion of a blood request (if needed)
if (isset($_POST['delete_request'])) {
    $request_id = $_POST['request_id'];
    $delete_query = "DELETE FROM blood_requests WHERE id = '$request_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Blood request deleted successfully!');</script>";
        echo "<script>window.location.href = 'blood_admin.php';</script>"; // Refresh page
    } else {
        echo "<script>alert('Error deleting blood request: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all blood requests from the database
$query_user_requests = "SELECT * FROM blood_requests ORDER BY blood_district ASC"; // Order by blood_district
$result_user_requests = $conn->query($query_user_requests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
    <style>
        /* Basic styles for the dashboard */
        .dashboard-container {
            padding: 20px;
        }
        .dashboard-button {
            padding: 10px 15px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .dashboard-button:hover {
            background-color: #0056b3;
        }
        .section {
            margin-top: 20px;
        }
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
    </style>
</head>
<body>

<div class="dashboard-container">
    <h1 class="my-4 text-left">Admin Dashboard</h1>
    <p>Manage blood requests and organizations effectively.</p>

    <!-- Dashboard Buttons -->
    <div>
        <a href="manage_user_requests.php" class="dashboard-button">Manage User Requests</a>
        <a href="manage_organization_requests.php" class="dashboard-button">Manage Organization Requests</a>
        <a href="manage_users.php" class="dashboard-button">Manage Users</a>
        <!-- Add other management buttons as needed -->
    </div>

   
    </div>
</div>

</body>
</html>

<?php
mysqli_close($conn);
?>
