<?php
include("db.php");
session_start(); // Ensure session is started

// Function to handle file upload
function uploadFile($file, $target_dir = "uploads/") {
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "File is not an image.";
    }

    if (file_exists($target_file)) {
        return "Sorry, file already exists.";
    }

    if ($file["size"] > 500000) {
        return "Sorry, your file is too large.";
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST['user_type'];
    $profilePicture = "uploads/default.jpg";

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadResult = uploadFile($_FILES['profile_picture']);
        if (strpos($uploadResult, "uploads/") !== false) {
            $profilePicture = $uploadResult;
        } else {
            echo "<script>alert('$uploadResult'); window.location.href='signup.php';</script>";
            exit();
        }
    }

    if ($userType === 'user') {
        $firstname = $conn->real_escape_string($_POST['first_name']);
        $lastname = $conn->real_escape_string($_POST['last_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT);
        $dob = $conn->real_escape_string($_POST['date_of_birth']);
        $location = $conn->real_escape_string($_POST['location']);
        $nic = $conn->real_escape_string($_POST['nic']); // Fetch NIC field
        $bloodtype = $conn->real_escape_string($_POST['blood_type']);
    
        // Add NIC to the insert query
        $stmt = $conn->prepare("INSERT INTO normal_user (normal_user_profile_picture, normal_user_firstname, normal_user_lastname, normal_user_email, normal_user_password, normal_user_DOB, normal_user_location, normal_user_bloodtype, NIC, normal_user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $profilePicture, $firstname, $lastname, $email, $password, $dob, $location, $bloodtype, $nic, $userType);
    
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id; // Set session variable for user ID
            $_SESSION['user_type'] = $userType; // Set session variable for user type
            echo "<script>alert('Signup successful!'); window.location.href='index.php?signup=success';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='signup.php';</script>";
        }
    }
     elseif ($userType === 'organization') {
        $orgName = $conn->real_escape_string($_POST['org_name']);
        $orgEmail = $conn->real_escape_string($_POST['org_email']);
        $orgPassword = password_hash($conn->real_escape_string($_POST['org_password']), PASSWORD_BCRYPT);
        $orgRegistrationNumber = $conn->real_escape_string($_POST['org_registration_number']);
        $orgPhone = $conn->real_escape_string($_POST['org_phone']);
        $orgAddress = $conn->real_escape_string($_POST['org_address']);
        $orgWebsite = $conn->real_escape_string($_POST['org_website']);

        $stmt = $conn->prepare("INSERT INTO organization (organization_name, organization_email, organization_password, organization_registration_number, organization_phone, organization_address, organization_website, organization_profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $orgName, $orgEmail, $orgPassword, $orgRegistrationNumber, $orgPhone, $orgAddress, $orgWebsite, $profilePicture);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id; // Set session variable for user ID
            $_SESSION['user_type'] = $userType; // Set session variable for user type
            echo "<script>alert('Signup successful!'); window.location.href='index.php?signup=success';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='signup.php';</script>";
        }
    }

    elseif ($userType === 'hospital') {
        $hospitalName = $conn->real_escape_string($_POST['hospital_name']);
        $hospitalAddress = $conn->real_escape_string($_POST['hospital_address']);
        $hospitalPhone = $conn->real_escape_string($_POST['hospital_phone']);
        $hospitalEmail = $conn->real_escape_string($_POST['hospital_email']);
        $hospitalWebsite = $conn->real_escape_string($_POST['hospital_website']);
        $hospitalDistrict = $conn->real_escape_string($_POST['hospital_district']);
        $hospitalType = $conn->real_escape_string($_POST['hospital_type']);
        $hospitalPassword = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT);
    
        // Handle image upload
        $hospitalImage = '';
        if (isset($_FILES['hospital_image'])) {
            $hospitalImage = uploadFile($_FILES['hospital_image']);
            if (strpos($hospitalImage, "Sorry") !== false) {
                echo "<script>alert('$hospitalImage'); window.location.href='signup.php';</script>";
                exit();
            }
        }
    
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO hospitals (hospital_name, hospital_address, hospital_phone, hospital_email, hospital_website, hospital_district, hospital_type, hospital_password, hospital_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        // Check if prepare failed
        if ($stmt === false) {
            die("MySQL error: " . $conn->error);  // Output the error if prepare failed
        }
    
        $stmt->bind_param("sssssssss", $hospitalName, $hospitalAddress, $hospitalPhone, $hospitalEmail, $hospitalWebsite, $hospitalDistrict, $hospitalType, $hospitalPassword, $hospitalImage);
    
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id; // Set session variable for user ID
            $_SESSION['user_type'] = $userType; // Set session variable for user type
            echo "<script>alert('Signup successful!'); window.location.href='index.php?signup=success';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='signup.php';</script>";
        }
    }
    
    


    $stmt->close();
    $conn->close();
}
