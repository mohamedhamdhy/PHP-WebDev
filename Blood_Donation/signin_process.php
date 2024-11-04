<?php
// Start session
session_start();

// Include database connection file
include('db.php');

// Get POST data
$email = $conn->real_escape_string($_POST['signin_email']);
$entered_password = $_POST['signin_password'];

// Function to check credentials in both tables
function checkCredentials($conn, $email, $password) {
    // Check in normal_user table
    $sql = "SELECT * FROM normal_user WHERE normal_user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['normal_user_password'])) {
            return [
                'user_type' => 'user',
                'user_id' => $user['normal_user_id'],
                'name' => $user['normal_user_firstname'],
                'redirect' => 'index.php'
            ];
        }
    }

    // Check in organization table
    $sql = "SELECT * FROM organization WHERE organization_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $org = $result->fetch_assoc();
        if (password_verify($password, $org['organization_password'])) {
            return [
                'user_type' => 'organization',
                'user_id' => $org['organization_id'],
                'name' => $org['organization_name'],
                'redirect' => 'index.php'
            ];
        }
    }

    // Check in hospitals table
$sql = "SELECT * FROM hospitals WHERE hospital_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $hospital = $result->fetch_assoc();
    if (password_verify($password, $hospital['hospital_password'])) {
        return [
            'user_type' => 'hospital',
            'user_id' => $hospital['hospital_id'],
            'name' => $hospital['hospital_name'],
            'redirect' => 'index.php'
        ];
    }
}


    return false;
}

// Validate credentials
$credentials = checkCredentials($conn, $email, $entered_password);

if ($credentials) {
    // Set session variables
    $_SESSION['logged_in'] = true;
    $_SESSION['user_type'] = $credentials['user_type'];
    $_SESSION['user_id'] = $credentials['user_id'];
    $_SESSION['name'] = $credentials['name'];

    // Redirect based on user type
    header("Location: " . $credentials['redirect']);
    exit();
} else {
    echo "<script>alert('You cant donate blood based on your type.'); window.location.href='signup.php';</script>";
}

$conn->close();
?>
