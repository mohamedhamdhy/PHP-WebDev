<?php 
session_start(); // Start the session

// Include your database connection
include('db.php');

// Check if user is logged in and get user details
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];
    
    // Get the campaign ID from the URL
    if (isset($_GET['id'])) {
        $campaignId = $_GET['id'];
    } else {
        // If no campaign ID is found, redirect or show an error
        echo "No campaign ID specified.";
        exit();
    }

    // Allow both hospitals and users to initiate a donation
    // However, check if the user is a donor and their donation_req_status
    if ($userType === 'user' || $userType === 'hospital') {
        // Check if the user is registered as a donor and if their donation status is confirmed
        $query = "SELECT * FROM donors WHERE normal_user_id = '$userId' AND donation_req_status = 'confirm'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            // User is a registered donor with confirmed status, process the help request
            
            // Fetch donor information if needed
            $donor = mysqli_fetch_assoc($result);
            
            // Example: Sending a message to the user (this could be a database insert or email)
            $message = "Thank you for your willingness to help! Your request for campaign ID $campaignId has been noted.";
            
            // Insert the message into a messages table or handle it as per your requirements
            // Assuming you have a messages table
            $insertQuery = "INSERT INTO messages (user_id, campaign_id, message) VALUES ('$userId', '$campaignId', '$message')";
            mysqli_query($conn, $insertQuery);
            
            // Display a success message
            echo "<script>alert('$message');</script>";
            echo "<script>window.location.href = 'index.php';</script>"; // Redirect back to the main page or wherever appropriate
            exit();
        } else {
            // User is not a registered donor or their status is not confirmed, alert them
            echo "<script>alert('You need to register as a donor with confirmed status first.');</script>";
            echo "<script>window.location.href = 'donor_registration.php';</script>"; // Redirect to the donor registration page
            exit();
        }
    } else {
        // If user type is neither hospital nor user, alert and redirect
        echo "<script>alert('Unauthorized access.');</script>";
        echo "<script>window.location.href = 'index.php';</script>"; // Redirect to an appropriate page
        exit();
    }
} else {
    // If not logged in, redirect to login page
    header("Location: signup.php");
    exit();
}
?>
