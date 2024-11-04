<?php
session_start(); // Start the session

require 'phpqrcode-master/phpqrcode-master/qrlib.php'; // Ensure this path is correct

// Check if donor_id is set in POST request
if (isset($_POST['donor_id'])) {
    // Sanitize the donor_id input
    $donor_id = intval($_POST['donor_id']); // Convert to an integer

    // Include the database connection
    include 'db.php'; // Ensure this file connects to your database

    // Retrieve the user_id from the session
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Check if user_id is set
    if ($user_id === null) {
        echo "User is not logged in.";
        exit;
    }

    // Prepare and execute the query to fetch donor details along with user profile and hospital name
    $query = "
        SELECT d.*, n.normal_user_profile_picture, h.hospital_name, h.hospital_address, h.hospital_phone 
        FROM donors d 
        JOIN normal_user n ON d.normal_user_id = n.normal_user_id 
        JOIN hospitals h ON d.hospital = h.hospital_id 
        WHERE d.donor_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $donor = $result->fetch_assoc();

            // Create the QR code content using complete donor details including hospital details
            $content = "ID: " . $donor['donor_id'] . "\n" .
                       "Name: " . $donor['donor_name'] . "\n" .
                       "NIC: " . $donor['donor_nic'] . "\n" .
                       "Phone: " . $donor['donor_phone'] . "\n" .
                       "Email: " . $donor['donor_email'] . "\n" .
                       "Blood Type: " . $donor['blood_type'] . "\n" .
                       "Gender: " . $donor['gender'] . "\n" .
                       "Weight: " . $donor['weight'] . " kg\n" .
                       "Health Conditions: " . $donor['health_conditions'] . "\n" .
                       "Medications: " . $donor['medications'] . "\n" .
                       "Last Donation Date: " . $donor['last_donation_date'] . "\n" .
                       "Emergency Contact: " . $donor['emergency_contact'] . "\n" .
                       "Emergency Relationship: " . $donor['emergency_relationship'] . "\n" .
                       "Emergency Phone: " . $donor['emergency_phone'] . "\n" .
                       "Preferred Donation Date: " . $donor['preferred_donation_date'] . "\n" .
                       "District: " . $donor['district'] . "\n" .
                       "Hospital Name: " . $donor['hospital_name'] . "\n" . // Get hospital name
                       "Hospital Address: " . $donor['hospital_address'] . "\n" . // Get hospital address
                       "Hospital Phone: " . $donor['hospital_phone'] . "\n" . // Get hospital phone
                       "Donation Request Status: " . $donor['donation_req_status'];

            // Generate the QR code
            $tempDir = 'temp/'; // Ensure this directory is writable
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true); // Create the directory if it doesn't exist
            }
            $fileName = 'donor_qr_' . $donor['donor_id'] . '.png';
            $filePath = $tempDir . $fileName;

            // Create the QR code image
            QRcode::png($content, $filePath, QR_ECLEVEL_L, 4); // Generates the QR code and saves it

            // Set headers for downloading the QR code
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            readfile($filePath); // Output the QR code image

            // Optionally delete the file after download
            unlink($filePath); // Uncomment if you want to delete the file after download
            exit;
        } else {
            echo "Donor not found.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "No donor ID provided.";
}
?>
