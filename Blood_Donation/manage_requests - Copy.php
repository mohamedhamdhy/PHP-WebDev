<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Include your database connection

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Initialize variables for edit functionality
$edit_mode = false;
$edit_request_id = '';
$blood_type = '';
$location = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];

    // Delete the request
    $delete_query = "DELETE FROM blood_requests WHERE id = '$request_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>
                alert('Blood request deleted successfully!');
                window.location.href = 'manage_requests.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'manage_requests.php';
              </script>";
    }
}

// Handle edit mode
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_request_id = $_GET['edit'];

    // Fetch the request details to populate the form
    $edit_query = "SELECT * FROM blood_requests WHERE id = '$edit_request_id' AND user_id = '$user_id'";
    $edit_result = mysqli_query($conn, $edit_query);
    
    if ($edit_result) {
        if ($row = mysqli_fetch_assoc($edit_result)) {
            $blood_type = $row['blood_type'];
            $location = $row['location'];
        }
    } else {
        echo "Error fetching request for edit: " . mysqli_error($conn);
    }
}

// Handle the form submission for edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_request'])) {
    $new_blood_type = $_POST['blood_type'];
    $new_location = $_POST['location'];
    $edit_request_id = $_POST['request_id'];

    // Update the request in the database
    $update_query = "UPDATE blood_requests SET blood_type = '$new_blood_type', location = '$new_location' WHERE id = '$edit_request_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>
                alert('Blood request updated successfully!');
                window.location.href = 'manage_requests.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
              </script>";
    }
}

// Fetch all blood requests for the logged-in user
$query = "SELECT * FROM blood_requests WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

// Check if the query execution was successful
if (!$result) {
    die("Error fetching blood requests: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Requests</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9; /* Light background */
    color: #333; /* Dark text */
    margin: 0;
    padding: 20px;
}

h2 {
    color: #b30000; /* Blood red color for headings */
    margin-bottom: 20px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden; /* Round the edges of the table */
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0; /* Light gray bottom border */
}

th {
    background-color: #ffcccc; /* Light blood color for header */
    color: #900; /* Darker text for header */
}

tr:hover {
    background-color: #ffe6e6; /* Light hover effect for rows */
}

tr:nth-child(even) {
    background-color: #f2f2f2; /* Light gray for even rows */
}

/* Button Styles */
.action-btn {
    display: inline-block;
    padding: 10px 15px;
    margin: 5px;
    text-decoration: none;
    background-color: #b30000; /* Blood red */
    color: white;
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
}

.action-btn:hover {
    background-color: #900; /* Darker blood red on hover */
    transform: scale(1.05); /* Slightly enlarge button on hover */
}

.delete-btn {
    background-color: #f44336; /* Red for delete action */
}

.delete-btn:hover {
    background-color: #d32f2f; /* Darker red on hover */
}

/* Form Styles */
form {
    background-color: #ffffff; /* White background for form */
    padding: 20px;
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Shadow for depth */
    margin-top: 20px;
}

label {
    display: block;
    margin: 10px 0 5px;
}

input[type="text"],
input[type="number"],
select {
    width: calc(100% - 20px); /* Full width input */
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc; /* Light border */
    border-radius: 5px; /* Rounded corners */
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Inner shadow */
}

input[type="submit"] {
    background-color: #4CAF50; /* Green for submit button */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
}

input[type="submit"]:hover {
    background-color: #45a049; /* Darker green on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    table, th, td {
        display: block; /* Stack elements for smaller screens */
        width: 100%; /* Full width */
    }

    th, td {
        box-sizing: border-box; /* Adjust box model */
        border: none; /* Remove border */
    }

    td {
        text-align: right; /* Align text to the right */
        padding-left: 50%; /* Indent to create space */
        position: relative; /* Position for pseudo-element */
    }

    td::before {
        content: attr(data-label); /* Use data-label for header */
        position: absolute;
        left: 10px;
        text-align: left; /* Align left */
        font-weight: bold; /* Bold header */
    }

    /* Adjust button styles for small screens */
    .action-btn {
        width: 100%; /* Full width for buttons */
        padding: 12px;
    }
}


    </style>
</head>
<body>

<h2>Your Blood Requests</h2>

<!-- Display all blood requests -->
<table>
    <tr>
        <th>Request ID</th>
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
        <th>NIC</th>
        <th>Delivery Status</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['age']; ?></td>
        <td><?php echo $row['blood_type']; ?></td>
        <td><?php echo $row['location']; ?></td>
        <td><?php echo $row['contact']; ?></td>
        <td><?php echo $row['reason']; ?></td>
        <td><?php echo $row['request_date']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['delivery_address']; ?></td>
        <td><?php echo $row['delivery_instructions']; ?></td>
        <td><?php echo $row['last_donated']; ?></td>
        <td><?php echo $row['weight']; ?></td>
        <td><?php echo $row['blood_pressure']; ?></td>
        <td><?php echo $row['medical_issues']; ?></td>
        <td><?php echo $row['nic']; ?></td>
        <td><?php echo $row['delivery_status']; ?></td>
        <td>
            <a href="manage_requests.php?edit=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
            <a href="manage_requests.php?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this request?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Display the edit form if in edit mode -->
<?php if ($edit_mode): ?>
<h2>Edit Blood Request</h2>

<form method="post" action="manage_requests.php">
    <input type="hidden" name="request_id" value="<?php echo $edit_request_id; ?>">
    <label for="blood_type">Blood Type:</label>
    <input type="text" id="blood_type" name="blood_type" value="<?php echo $blood_type; ?>" required><br><br>

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" value="<?php echo $location; ?>" required><br><br>

    <button type="submit" name="update_request">Update Request</button>
</form>

<?php endif; ?>

</body>
</html>
