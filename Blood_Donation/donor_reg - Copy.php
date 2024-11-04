<?php
session_start();
include('db.php'); // Database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user profile data from normal_user table
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM normal_user WHERE normal_user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Error: User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $donor_name = $_POST['donor_name'];
    $donor_phone = $_POST['donor_phone'];
    $donor_email = $_POST['donor_email'];
    $blood_type = $_POST['blood_type'];
    $donor_nic = $_POST['donor_nic'];
    $donation_method = $_POST['donation_method'];
    $district = isset($_POST['district']) ? $_POST['district'] : null;
    $hospital = isset($_POST['hospital']) ? $_POST['hospital'] : null;
    $donation_address = $_POST['donation_address'];
    $registration_date = date('Y-m-d');

    // Insert into donors table
    $query = "INSERT INTO donors (user_id,donor_name, donor_phone, donor_email, blood_type, donation_method, district, hospital, donation_address, registration_date, donor_nic) 
              VALUES ('$user_id','$donor_name', '$donor_phone', '$donor_email', '$blood_type', '$donation_method', '$district', '$hospital', '$donation_address', '$registration_date', '$donor_nic')";

    if (mysqli_query($conn, $query)) {
        echo "Donor registered successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="number"], select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .profile-picture {
            display: block;
            margin: 0 auto 15px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="form-container">
    <form id="donorForm" action="" method="POST">
        <h2>Register as a Donor</h2>
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />

        <!-- Profile Picture -->
        <img src="<?php echo $user['normal_user_profile_picture']; ?>" alt="Profile Picture" class="profile-picture">

        <!-- Donor Name -->
        <label for="donor_name">Donor Name:</label>
        <input type="text" id="donor_name" name="donor_name" value="<?php echo $user['normal_user_firstname'] . ' ' . $user['normal_user_lastname']; ?>" readonly>

        <label for="donor_nic">Donor NIC No:</label>
        <input type="text" id="donor_nic" name="donor_nic" value="<?php echo $user['NIC']; ?>" readonly>
        <!-- Phone Number -->
        <label for="donor_phone">Phone Number:</label>
        <input type="text" id="donor_phone" name="donor_phone" placeholder="Enter your number for donation" ?>
        <!-- Email -->
        <label for="donor_email">Email:</label>
        <input type="email" id="donor_email" name="donor_email" value="<?php echo $user['normal_user_email']; ?>" readonly>

        <!-- Blood Type -->
        <label for="blood_type">Blood Type:</label>
        <input type="text" id="blood_type" name="blood_type" value="<?php echo $user['normal_user_bloodtype']; ?>" readonly>

        <!-- Donation Method -->
        <label for="donation_method">Do you want to donate blood from home or hospital?</label>
        <select id="donation_method" name="donation_method" onchange="toggleHospitalFields()" required>
            <option value="Home">Home</option>
            <option value="Hospital">Hospital</option>
        </select>

        <!-- Additional fields for hospital/district -->
        <div id="hospitalFields" style="display: none;">
            <label for="district">Select District:</label>
            <select id="district" name="district" onchange="loadHospitals()">
                <option value="Colombo">Colombo</option>
                <option value="Gampaha">Gampaha</option>
                <option value="Kandy">Kandy</option>
            </select>

            <label for="hospital">Select Hospital:</label>
            <select id="hospital" name="hospital">
                <!-- Hospitals will be dynamically loaded -->
            </select>
        </div>

        <!-- Donation Address -->
        <label for="donation_address">Donation Address:</label>
        <input type="text" id="donation_address" name="donation_address" placeholder="Enter your address for donation" required>

        <!-- Submit Button -->
        <input type="submit" value="Register">
    </form>
</div>

<script>
    function toggleHospitalFields() {
        const donationMethod = document.getElementById('donation_method').value;
        const hospitalFields = document.getElementById('hospitalFields');
        if (donationMethod === 'Hospital') {
            hospitalFields.style.display = 'block';
        } else {
            hospitalFields.style.display = 'none';
        }
    }

    function loadHospitals() {
        const district = document.getElementById('district').value;
        const hospitalSelect = document.getElementById('hospital');
        hospitalSelect.innerHTML = '';

        // Example hospitals (you can fetch this dynamically from DB)
        const hospitals = {
            'Colombo': ['Colombo National Hospital', 'Asiri Hospital'],
            'Gampaha': ['Gampaha General Hospital'],
            'Kandy': ['Kandy General Hospital']
        };

        if (hospitals[district]) {
            hospitals[district].forEach(hospital => {
                const option = document.createElement('option');
                option.value = hospital;
                option.textContent = hospital;
                hospitalSelect.appendChild(option);
            });
        }
    }
</script>

</body>
</html>
