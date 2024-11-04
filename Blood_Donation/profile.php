<?php
session_start(); // Ensure session is started
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Initialize variables to store user information
$user_details = [];

// Fetch user details based on user type
if ($user_type === 'user') {
    $stmt = $conn->prepare("SELECT * FROM normal_user WHERE normal_user_id = ?");
} elseif ($user_type === 'organization') {
    $stmt = $conn->prepare("SELECT * FROM organization WHERE organization_id = ?");
} else {
    die("Invalid user type.");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Store user details in an associative array
if ($result->num_rows > 0) {
    $user_details = $result->fetch_assoc();
}
$stmt->close(); // Close the statement

// Update user information if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size (e.g., limit to 2MB)
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            die("Sorry, your file is too large.");
        }

        // Allow only certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update user profile picture path in the database
            $stmt = $conn->prepare("UPDATE normal_user SET normal_user_profile_picture = ? WHERE normal_user_id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Sorry, there was an error uploading your file.");
        }
    }

// Prepare update statement based on user type
if ($user_type === 'user') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Hash the password before updating
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE normal_user SET 
        normal_user_firstname = ?, 
        normal_user_lastname = ?, 
        normal_user_email = ?, 
        normal_user_password = ?, 
        normal_user_DOB = ?, 
        normal_user_location = ?, 
        NIC = ?, 
        normal_user_bloodtype = ? 
        WHERE normal_user_id = ?");
    
    $stmt->bind_param("ssssssssi", $_POST['firstname'], $_POST['lastname'], $_POST['email'], $hashed_password, $_POST['dob'], $_POST['location'], $_POST['nic'], $_POST['bloodtype'], $user_id);
}
}
 elseif ($user_type === 'organization') {

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Hash the password before updating
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE organization SET 
        organization_name = ?, 
        organization_email = ?, 
        organization_password = ?, 
        organization_registration_number = ?, 
        organization_phone = ?, 
        organization_address = ?, 
        organization_website = ? 
        WHERE organization_id = ?");
    
    $stmt->bind_param("sssssssi", $_POST['name'], $_POST['email'], $hashed_password, $_POST['registration_number'], $_POST['phone'], $_POST['address'], $_POST['website'], $user_id);
}
 

// Execute the statement
if ($stmt->execute()) {
   echo'<script>alert("Profile Update Succesfully!")</script>';
} else {
    echo "Error updating profile: " . $stmt->error;
}

// Close the statement
$stmt->close();

}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2><?php echo ($user_type === 'user') ? 'User Profile' : 'Organization Profile'; ?></h2>
    <form method="POST" enctype="multipart/form-data">
    <?php if ($user_type === 'user'): ?>
    <div class="mb-3">
        <label for="profile_picture" class="form-label">Profile Picture</label>
        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
        <img src="<?php echo htmlspecialchars($user_details['normal_user_profile_picture']); ?>" alt="Profile Picture" width="100" height="100" class="mt-2">
    </div>
    <div class="mb-3">
        <label for="firstname" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user_details['normal_user_firstname']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="lastname" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user_details['normal_user_lastname']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_details['normal_user_email']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($user_details['normal_user_password']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user_details['normal_user_DOB']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($user_details['normal_user_location']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="nic" class="form-label">NIC</label>
        <input type="text" class="form-control" id="nic" name="nic" value="<?php echo htmlspecialchars($user_details['NIC']); ?>" required>
    </div>
    <div class="form-group">
        <label for="bloodtype">Blood Type:</label>
        <select id="bloodtype" name="bloodtype" class="form-control" required>
            <option value="">Select Blood Type</option>
            <option value="A+" <?php if ($user_details['normal_user_bloodtype'] == 'A+') echo 'selected'; ?>>A+</option>
            <option value="A-" <?php if ($user_details['normal_user_bloodtype'] == 'A-') echo 'selected'; ?>>A-</option>
            <option value="B+" <?php if ($user_details['normal_user_bloodtype'] == 'B+') echo 'selected'; ?>>B+</option>
            <option value="B-" <?php if ($user_details['normal_user_bloodtype'] == 'B-') echo 'selected'; ?>>B-</option>
            <option value="O+" <?php if ($user_details['normal_user_bloodtype'] == 'O+') echo 'selected'; ?>>O+</option>
            <option value="O-" <?php if ($user_details['normal_user_bloodtype'] == 'O-') echo 'selected'; ?>>O-</option>
            <option value="AB+" <?php if ($user_details['normal_user_bloodtype'] == 'AB+') echo 'selected'; ?>>AB+</option>
            <option value="AB-" <?php if ($user_details['normal_user_bloodtype'] == 'AB-') echo 'selected'; ?>>AB-</option>
        </select>
    </div>

        <?php elseif ($user_type === 'organization'): ?>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                <img src="<?php echo htmlspecialchars($user_details['organization_profile_picture']); ?>" alt="Profile Picture" width="100" height="100" class="mt-2">
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Organization Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_details['organization_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_details['organization_email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($user_details['organization_password']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?php echo htmlspecialchars($user_details['organization_registration_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['organization_phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user_details['organization_address']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="text" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($user_details['organization_website']); ?>" required>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
