<?php
session_start();
include('db.php'); // Database connection

// Check if the user is logged in and get the user type
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}


if($userType == 'user'){

// Get user profile data from normal_user table
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM normal_user WHERE normal_user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('You are not eligible for a donor'); window.location.href = 'blood_home.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $donor_name = $_POST['donor_name'];
    $donor_phone = $_POST['donor_phone'];
    $donor_email = $_POST['donor_email'];
    $blood_type = $_POST['blood_type'];
    $donor_nic = $_POST['donor_nic'];
    $gender = $_POST['gender']; // Get gender from form
    $weight = $_POST['weight']; // Get weight from form
    $health_conditions = $_POST['health_conditions']; // Get health conditions from form
    $medications = $_POST['medications']; // Get medications from form
    $last_donation_date = $_POST['last_donation_date']; // Get last donation date
    $emergency_contact = $_POST['emergency_contact']; // Get emergency contact name
    $emergency_relationship = $_POST['emergency_relationship']; // Get emergency contact relationship
    $emergency_phone = $_POST['emergency_phone']; // Get emergency contact phone
    $preferred_donation_date = $_POST['preferred_donation_date']; // Get preferred donation date
    $district = isset($_POST['district']) ? $_POST['district'] : null;
    $hospital = isset($_POST['hospital']) ? $_POST['hospital'] : null;
    $donation_req_status = 'pending'; // Set default donation request status
    $registration_date = date('Y-m-d H:i:s'); // Capture registration date and time

    // Insert into donors table
    $query = "INSERT INTO donors (normal_user_id, donor_name, donor_phone, donor_email, blood_type, gender, weight, health_conditions, medications, last_donation_date, emergency_contact, emergency_relationship, emergency_phone, preferred_donation_date, district, hospital, donation_req_status, created_at, donor_nic) 
              VALUES ('$user_id', '$donor_name', '$donor_phone', '$donor_email', '$blood_type', '$gender', '$weight', '$health_conditions', '$medications', '$last_donation_date', '$emergency_contact', '$emergency_relationship', '$emergency_phone', '$preferred_donation_date', '$district', '$hospital', '$donation_req_status', '$registration_date', '$donor_nic')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('We will notify you when your request is confirmed. Thank you!'); window.location.href = 'blood_home.php';</script>";

    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    exit;
}

}

else{
    echo "<script>
    alert('You are not eligible for as a Donor');
    window.location.href = 'blood_home.php';
</script>";
    exit();
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
    max-width: 300px; /* Further reduce the max width */
    padding: 15px; /* Adjust the padding to be smaller */
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    margin: 0 auto;
    max-height: 600px; /* Reduce max height if needed */
    overflow: auto; /* Add this to handle overflow if content exceeds the height */
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
        .form-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    textarea,
    select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    textarea {
        height: 80px;
    }

    input[type="submit"] {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }

    .profile-picture {
        display: block;
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin: 0 auto 20px;
        border-radius: 50%;
        border: 2px solid #ccc;
    }
    #agreementModal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000; /* Ensures it appears on top of other content */
}

    </style>
</head>
<body>

<div class="form-container">
    <form id="donorForm" action="" method="POST">
        <h2>Register as a Donor</h2>
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />

        <!-- Profile Picture -->
        <div class="form-group">
            <img src="<?php echo $user['normal_user_profile_picture']; ?>" alt="Profile Picture" class="profile-picture">
        </div>

        <!-- Donor Name -->
        <div class="form-group">
            <label for="donor_name">Donor Name:</label>
            <input type="text" id="donor_name" name="donor_name" value="<?php echo $user['normal_user_firstname'] . ' ' . $user['normal_user_lastname']; ?>" readonly>
        </div>

        <!-- Donor NIC No -->
        <div class="form-group">
            <label for="donor_nic">Donor NIC No:</label>
            <input type="text" id="donor_nic" name="donor_nic" value="<?php echo $user['NIC']; ?>" readonly>
        </div>

        <!-- Phone Number -->
        <div class="form-group">
            <label for="donor_phone">Phone Number:</label>
            <input type="text" id="donor_phone" name="donor_phone" placeholder="Enter your phone number for donation" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="donor_email">Email:</label>
            <input type="email" id="donor_email" name="donor_email" value="<?php echo $user['normal_user_email']; ?>" readonly>
        </div>

        <!-- Blood Type -->
        <div class="form-group">
            <label for="blood_type">Blood Type:</label>
            <input type="text" id="blood_type" name="blood_type" value="<?php echo $user['normal_user_bloodtype']; ?>" readonly>
        </div>

        <!-- Gender -->
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <!-- Weight -->
        <div class="form-group">
            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" placeholder="Enter your weight" required>
        </div>

        <!-- Health Conditions -->
        <div class="form-group">
            <label for="health_conditions">Any Health Conditions? (e.g., diabetes, hypertension):</label>
            <textarea id="health_conditions" name="health_conditions" placeholder="Enter any known health conditions" required></textarea>
        </div>

        <!-- Medications -->
        <div class="form-group">
            <label for="medications">Are you currently taking any medications?</label>
            <textarea id="medications" name="medications" placeholder="Enter medications you are taking" required></textarea>
        </div>

        <!-- Last Blood Donation Date -->
        <div class="form-group">
            <label for="last_donation_date">Last Blood Donation Date (if any):</label>
            <input type="date" id="last_donation_date" name="last_donation_date">
        </div>

        <!-- Emergency Contact Name -->
        <div class="form-group">
            <label for="emergency_contact">Emergency Contact Name:</label>
            <input type="text" id="emergency_contact" name="emergency_contact" placeholder="Enter emergency contact name" required>
        </div>

        <!-- Emergency Contact Relationship -->
        <div class="form-group">
            <label for="emergency_relationship">Emergency Contact Relationship:</label>
            <input type="text" id="emergency_relationship" name="emergency_relationship" placeholder="Enter emergency contact relationship" required>
        </div>

        <!-- Emergency Contact Phone -->
        <div class="form-group">
            <label for="emergency_phone">Emergency Contact Phone:</label>
            <input type="text" id="emergency_phone" name="emergency_phone" placeholder="Enter emergency contact phone" required>
        </div>

        <!-- Preferred Donation Date -->
        <div class="form-group">
            <label for="preferred_donation_date">Preferred Donation Date:</label>
            <input type="date" id="preferred_donation_date" name="preferred_donation_date" required>
        </div>



        <!-- Select District -->
        <div class="form-group">
            <label for="district">Select District:</label>
            <select id="district" name="district" onchange="loadHospitals(this.value)" required>
                <option value="">Select District</option>
                <option value="Colombo">Colombo</option>
                <option value="Gampaha">Gampaha</option>
                <option value="Ampara">Ampara</option>
                <option value="Batticaloa">Batticaloa</option>
            </select>
        </div>

        <!-- Select Hospital -->
        <div class="form-group">
            <label for="hospital">Select Hospital:</label>
            <select id="hospital" name="hospital" required>
                <option value="">Select a hospital</option>
            </select>
        </div>

        <!-- Agreement Checkbox -->
        <div class="form-group">
            <input type="checkbox" id="agreement" name="agreement" required>
            <label for="agreement">I agree to the <a href="#" onclick="showAgreement()">terms and conditions</a>.</label>
        </div>

        <!-- Agreement Text Modal -->
        <div id="agreementModal" style="display:none;">
            <h3>Terms and Conditions</h3>
            <p>By registering as a blood donor, you agree to the following terms:</p>
            <ul>
                <li>You must be at least 18 years old to donate blood.</li>
                <li>You should not have any serious medical conditions.</li>
                <li>Ensure to stay hydrated before the donation.</li>
                <li>Notify the staff of any medications you are currently taking.</li>
            </ul>
            <button onclick="closeAgreement()">Close</button>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <input type="submit" value="Register">
        </div>
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

    
</script>
<script>
    function showAgreement() {
        document.getElementById('agreementModal').style.display = 'block';
    }

    function closeAgreement() {
        document.getElementById('agreementModal').style.display = 'none';
    }
</script>

<script>
function loadHospitals(district) {
    // Clear existing hospitals
    var hospitalSelect = document.getElementById("hospital");
    hospitalSelect.innerHTML = '<option value="">Select a hospital</option>';

    if (district) {
        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "get_hospitals.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Handle the response
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var hospitals = JSON.parse(xhr.responseText);
                // Populate the hospital dropdown with options
                hospitals.forEach(function (hospital) {
                    var option = document.createElement("option");
                    option.value = hospital.hospital_id;
                    option.text = hospital.hospital_name;
                    hospitalSelect.appendChild(option);
                });
            }
        };

        // Send the district to the server
        xhr.send("district=" + district);
    }
}

// Collapsible functionality
document.addEventListener("DOMContentLoaded", function() {
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
});

</script>

</body>
</html>
