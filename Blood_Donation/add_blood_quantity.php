<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first."); // Redirect to login page or show an error
}

// Include the database connection
include 'db.php'; // Ensure this file connects to your database

// Get hospital_id from session (assuming user_id corresponds to hospital_id)
$hospitalId = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get blood quantities from the form input
    $A_plus = $_POST['A_plus'];
    $A_minus = $_POST['A_minus'];
    $B_plus = $_POST['B_plus'];
    $B_minus = $_POST['B_minus'];
    $AB_plus = $_POST['AB_plus'];
    $AB_minus = $_POST['AB_minus'];
    $O_plus = $_POST['O_plus'];
    $O_minus = $_POST['O_minus'];

    // Prepare the SQL query to insert/update blood quantities
    $query = "INSERT INTO blood_availability (hospital_id, A_plus, A_minus, B_plus, B_minus, AB_plus, AB_minus, O_plus, O_minus) 
              VALUES ('$hospitalId', '$A_plus', '$A_minus', '$B_plus', '$B_minus', '$AB_plus', '$AB_minus', '$O_plus', '$O_minus') 
              ON DUPLICATE KEY UPDATE 
              A_plus = '$A_plus', A_minus = '$A_minus', B_plus = '$B_plus', B_minus = '$B_minus', 
              AB_plus = '$AB_plus', AB_minus = '$AB_minus', O_plus = '$O_plus', O_minus = '$O_minus'";

    if ($conn->query($query) === TRUE) {

        echo "<script>
        alert('Blood quantities added/updated successfully');
        window.location.href = 'manage_blood_campaign.php';
    </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch existing blood availability for the hospital
$availabilityQuery = "SELECT * FROM blood_availability WHERE hospital_id = '$hospitalId'";
$availabilityResult = $conn->query($availabilityQuery);
$existingAvailability = $availabilityResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blood Quantity</title>
</head>
<body>

<h2>Add Blood Quantity</h2>
<form method="post">
    <label for="A_plus">A+:</label>
    <input type="number" id="A_plus" name="A_plus" value="<?php echo isset($existingAvailability['A_plus']) ? $existingAvailability['A_plus'] : 0; ?>" required><br>

    <label for="A_minus">A-:</label>
    <input type="number" id="A_minus" name="A_minus" value="<?php echo isset($existingAvailability['A_minus']) ? $existingAvailability['A_minus'] : 0; ?>" required><br>

    <label for="B_plus">B+:</label>
    <input type="number" id="B_plus" name="B_plus" value="<?php echo isset($existingAvailability['B_plus']) ? $existingAvailability['B_plus'] : 0; ?>" required><br>

    <label for="B_minus">B-:</label>
    <input type="number" id="B_minus" name="B_minus" value="<?php echo isset($existingAvailability['B_minus']) ? $existingAvailability['B_minus'] : 0; ?>" required><br>

    <label for="AB_plus">AB+:</label>
    <input type="number" id="AB_plus" name="AB_plus" value="<?php echo isset($existingAvailability['AB_plus']) ? $existingAvailability['AB_plus'] : 0; ?>" required><br>

    <label for="AB_minus">AB-:</label>
    <input type="number" id="AB_minus" name="AB_minus" value="<?php echo isset($existingAvailability['AB_minus']) ? $existingAvailability['AB_minus'] : 0; ?>" required><br>

    <label for="O_plus">O+:</label>
    <input type="number" id="O_plus" name="O_plus" value="<?php echo isset($existingAvailability['O_plus']) ? $existingAvailability['O_plus'] : 0; ?>" required><br>

    <label for="O_minus">O-:</label>
    <input type="number" id="O_minus" name="O_minus" value="<?php echo isset($existingAvailability['O_minus']) ? $existingAvailability['O_minus'] : 0; ?>" required><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
