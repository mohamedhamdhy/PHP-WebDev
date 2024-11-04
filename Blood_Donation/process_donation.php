<?php 
session_start(); // Ensure session is started

include("nav.php"); // Include navigation

// Check if the user is logged in and get the user type
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Check if the user is a donor
if ($userType === 'user') {
    // Get the donor ID and hospital ID from POST data
    if (isset($_POST['donor_id']) && isset($_POST['hospital_id'])) {
        $donorId = $_POST['donor_id'];
        $hospitalId = $_POST['hospital_id'];

        // Database connection
        $servername = "localhost"; // Change to your server name
        $username = "root"; // Change to your database username
        $password = ""; // Change to your database password
        $dbname = "lifebridge"; // Change to your database name

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert the donation record into the donations_blood table
        $sql = "INSERT INTO donations_blood (donor_id, hospital_id, donation_date) VALUES (?, ?, NOW())"; // Using NOW() for the current date
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $donorId, $hospitalId); // Bind parameters

            if ($stmt->execute()) {
                // Get the last inserted donation ID
                $donationId = $stmt->insert_id; // Get the auto-incremented donation ID

                // Donation successful, display a message and the donor ID
                echo "<div class='container mt-5'>";
                echo "<h2>Donation Successful!</h2>";
                echo "<p>Thank you for your contribution.</p>";
                echo "<p>Your Donation ID: <strong>" . htmlspecialchars($donationId) . "</strong></p>"; // Display donation ID
                echo "<p>Your Donor ID: <strong>" . htmlspecialchars($donorId) . "</strong></p>"; // Display donor ID
                echo "</div>";
                
                // Optionally, you can redirect the user to a thank you page
                // header("Location: thank_you.php");
                // exit();
            } else {
                echo "Error: " . $stmt->error; // Display error message
            }

            $stmt->close(); // Close statement
        } else {
            echo "Error preparing statement: " . $conn->error; // Display prepare error
        }

        $conn->close(); // Close the database connection
    } else {
        // Handle missing POST data
        echo "<div class='container mt-5'>";
        echo "<h2>Error</h2>";
        echo "<p>Donor ID or Hospital ID is missing. Please fill in all required fields.</p>";
        echo "</div>";
    }
} else {
    // Show a message if the user is not a donor instead of redirecting
    echo "<div class='container mt-5'>";
    echo "<h2>Access Denied</h2>";
    echo "<p>You do not have permission to access this page. Please log in as a donor to donate.</p>";
    echo "<a href='login.php'>Login</a>"; // Optionally provide a link to log in
    echo "</div>";
}
?>
