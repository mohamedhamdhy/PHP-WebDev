<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Assuming the hospital_id is stored in the session
$hospital_id = $_SESSION['user_id']; // Adjust if user_id represents hospital_id

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// If the form is submitted, update the blood inventory
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated values from the form
    $A_plus = intval($_POST['A_plus']);
    $A_minus = intval($_POST['A_minus']);
    $B_plus = intval($_POST['B_plus']);
    $B_minus = intval($_POST['B_minus']);
    $AB_plus = intval($_POST['AB_plus']);
    $AB_minus = intval($_POST['AB_minus']);
    $O_plus = intval($_POST['O_plus']);
    $O_minus = intval($_POST['O_minus']);

    // Query to update the inventory for this hospital
    $query = "UPDATE blood_availability SET 
                A_plus = ?, A_minus = ?, B_plus = ?, B_minus = ?, 
                AB_plus = ?, AB_minus = ?, O_plus = ?, O_minus = ?
              WHERE hospital_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('iiiiiiiii', $A_plus, $A_minus, $B_plus, $B_minus, $AB_plus, $AB_minus, $O_plus, $O_minus, $hospital_id);
        if ($stmt->execute()) {
            echo "<p class='success-message'>Inventory updated successfully!</p>";
        } else {
            echo "<p class='error-message'>Error updating inventory: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Query to get the current blood inventory for the hospital
$query = "SELECT * FROM blood_availability WHERE hospital_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the blood inventory data
        $inventory = $result->fetch_assoc();
    } else {
        echo "<h2>No inventory record found for this hospital.</h2>";
        exit;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }

        h2 {
            color: #d9534f;
        }

        .inventory-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .inventory-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .inventory-form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .inventory-form button {
            background-color: #5bc0de;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .inventory-form button:hover {
            background-color: #31b0d5;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="inventory-form">
        <h2>Manage Blood Inventory</h2>
        <form action="manage_inventory.php" method="post">
            <label for="A_plus">A+ Quantity</label>
            <input type="number" id="A_plus" name="A_plus" value="<?php echo htmlspecialchars($inventory['A_plus']); ?>" min="0">

            <label for="A_minus">A- Quantity</label>
            <input type="number" id="A_minus" name="A_minus" value="<?php echo htmlspecialchars($inventory['A_minus']); ?>" min="0">

            <label for="B_plus">B+ Quantity</label>
            <input type="number" id="B_plus" name="B_plus" value="<?php echo htmlspecialchars($inventory['B_plus']); ?>" min="0">

            <label for="B_minus">B- Quantity</label>
            <input type="number" id="B_minus" name="B_minus" value="<?php echo htmlspecialchars($inventory['B_minus']); ?>" min="0">

            <label for="AB_plus">AB+ Quantity</label>
            <input type="number" id="AB_plus" name="AB_plus" value="<?php echo htmlspecialchars($inventory['AB_plus']); ?>" min="0">

            <label for="AB_minus">AB- Quantity</label>
            <input type="number" id="AB_minus" name="AB_minus" value="<?php echo htmlspecialchars($inventory['AB_minus']); ?>" min="0">

            <label for="O_plus">O+ Quantity</label>
            <input type="number" id="O_plus" name="O_plus" value="<?php echo htmlspecialchars($inventory['O_plus']); ?>" min="0">

            <label for="O_minus">O- Quantity</label>
            <input type="number" id="O_minus" name="O_minus" value="<?php echo htmlspecialchars($inventory['O_minus']); ?>" min="0">

            <button type="submit">Update Inventory</button>
        </form>
    </div>
</body>
</html>