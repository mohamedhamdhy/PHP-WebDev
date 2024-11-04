<?php
// Database connection details
$host = "localhost";
$dbname = "lifebridge"; // replace with your database name
$username = "root"; // replace with your username
$password = ""; // replace with your password

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospitalID = $_POST['hospitalID'];
    $bloodType = $_POST['bloodType'];
    $requestedQuantity = $_POST['quantity'];

    // Begin a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Step 1: Check the available blood quantity in the BloodBank table
        $sql = "SELECT Quantity FROM BloodBank WHERE HospitalID = ? AND BloodType = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $hospitalID, $bloodType);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            // No record found for the specified hospital and blood type
            echo json_encode([
                'status' => 'error',
                'message' => 'No inventory found for the specified blood type in this hospital.'
            ]);
            $conn->rollback();
            exit();
        }

        // Debugging: Print out the available quantity
        error_log('Available quantity: ' . $row['Quantity']);
        
        if ($row['Quantity'] < $requestedQuantity) {
            // If the requested quantity is not available, throw an error
            echo json_encode([
                'status' => 'error',
                'message' => 'Requested quantity of blood type ' . $bloodType . ' is not available.'
            ]);
            $conn->rollback();
            exit();
        }

        // Step 2: Insert the blood request into the BloodRequests table
        $sql = "INSERT INTO BloodRequests (HospitalID, BloodType, RequestedQuantity, RequestDate) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $hospitalID, $bloodType, $requestedQuantity);
        $stmt->execute();

        // Step 3: Update the BloodBank table by reducing the available quantity
        $newQuantity = $row['Quantity'] - $requestedQuantity;
        $sql = "UPDATE BloodBank SET Quantity = ? WHERE HospitalID = ? AND BloodType = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $newQuantity, $hospitalID, $bloodType);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Return a success message
        echo json_encode([
            'status' => 'success',
            'message' => 'Blood request for ' . $requestedQuantity . ' units of ' . $bloodType . ' submitted successfully!'
        ]);
    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while processing your request.'
        ]);
    }
}

// Close the connection
$conn->close();
?>
