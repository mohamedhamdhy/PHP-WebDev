<?php
session_start(); // Always start the session

if (!isset($_SESSION['user_id'])) {
    // If the session key is not set, redirect to the login page or show an error
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Proceed with the rest of your code if the session is set
$user_id = $_SESSION['user_id'];

// Retrieve user details from the database
include('db.php');
$query = "SELECT * FROM normal_user WHERE normal_user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

if ($user_data) {
    $user_id = $user_data['normal_user_id'];
    $name = $user_data['normal_user_firstname'] . " " . $user_data['normal_user_lastname'];
    $age = ""; // Ask the user to fill this in
    $blood_type = $user_data['normal_user_bloodtype'];
    $location = $user_data['normal_user_location'];
    $contact = ""; // Ask the user to fill this in
    $nic = $user_data['NIC']; // Ask the user to fill this in
} else {
    echo "User not found.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Blood Request Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(to bottom right, #ffcccc, #ffe6e6);
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #ff4d4d;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            transition: all 0.3s ease;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #b30000;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ff9999;
            border-radius: 5px;
            background-color: #ffefef;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
        }

        input[type="submit"],
        .next-btn,
        .prev-btn {
            background-color: #ff4d4d;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover,
        .next-btn:hover,
        .prev-btn:hover {
            background-color: #cc0000;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        .btn-container .prev-btn {
            background-color: #aaa;
            width: 48%;
        }

        .btn-container .next-btn {
            width: 48%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-container {
                width: 90%;
            }

            input[type="submit"],
            .next-btn,
            .prev-btn {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="form-container">
    <form id="bloodRequestForm" action="submit_blood_request.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <h2>Blood Request Form</h2>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" /> <!-- Hidden input for user ID -->
            <!-- Other form fields -->
            <!-- Step 1 -->
            <div class="form-step active">
                <label>Name:</label>

        
               
                <input type="text" name="name" value="<?php echo $name; ?>" readonly>
<br>
                <div>
                            <!-- New Image Upload Field -->
            <label>Upload Image</label>
            <input type="file" name="blood_request_image" accept="image/*">
                </div>
                <br>

                <div>
                    <!-- Blood Description -->
            <label>Additional Description (optional):</label>
            <textarea name="blood_description" placeholder="Enter details about the blood request"></textarea>
                </div>
       

                <label>Age:</label>
                <input type="number" name="age" placeholder="Enter your age" required>

                <label>Blood Type:</label>
                <select name="blood_type" required>
                    <option value="" disabled selected>Select your blood type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>

                <label>Location:</label>
                <input type="text" name="location" value="<?php echo $location; ?>" required>

         <!-- Blood District -->
         <label>Blood District:</label>
            <select name="blood_district" required>
                <option value="" disabled selected>Select your district</option>
                <option value="Colombo">Colombo</option>
                <option value="Ampara">Ampara</option>
                <option value="Batticola">Batticola</option>
                <!-- Add more district options as needed -->
            </select>

                <label>Contact:</label>
                <input type="text" name="contact" placeholder="Enter your contact number" required pattern="^\d{10}$" title="Please enter a valid 10-digit contact number">

                <label>NIC:</label>
                <input type="text" name="nic" value="<?php echo $nic; ?>" readonly>

                <div class="btn-container">
                    <button type="button" class="next-btn" onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="form-step">
                <label>Reason for Request:</label>
                <textarea name="reason" placeholder="Explain the reason for blood request" required></textarea>

                <label>Request Date:</label>
                <input type="date" name="request_date" id="request_date" required>

                <label>Quantity (in units):</label>
                <input type="number" name="quantity" placeholder="Enter the required units of blood" required min="1" max="10">

                <div class="btn-container">
                    <button type="button" class="prev-btn" onclick="prevStep()">Previous</button>
                    <button type="button" class="next-btn" onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="form-step">
                <label>Delivery Method:</label>
                <select name="delivery_method" id="delivery_method" onchange="toggleDeliveryOptions()" required>
                    <option value="" disabled selected>Select delivery method</option>
                    <option value="pickup">Pick Up</option>
                    <option value="delivery">Delivery</option>
                </select>

                <div id="pickup_options" style="display: none;">
                    <label>Available Pick-Up Locations:</label>
                    <select name="pickup_location">
                        <option value="Location 1">Location 1</option>
                        <option value="Location 2">Location 2</option>
                        <option value="Location 3">Location 3</option>
                    </select>
                </div>

                <div id="delivery_options" style="display: none;">
                    <label>Delivery Address:</label>
                    <input type="text" name="delivery_address" placeholder="Enter delivery address" required>

                    <label>Delivery Instructions:</label>
                    <textarea name="delivery_instructions" placeholder="Enter any delivery instructions"></textarea>
                </div>

                <label>Last Donated:</label>
                <input type="date" name="last_donated" id="last_donated">

                <label>Weight:</label>
                <input type="number" name="weight" placeholder="Enter your weight" required min="50">

                <label>Blood Pressure:</label>
                <input type="text" name="blood_pressure" placeholder="Enter your blood pressure" required>

                <label>Medical Issues:</label>
                <textarea name="medical_issues" placeholder="Any medical conditions?"></textarea>

                <div class="btn-container">
                    <button type="button" class="prev-btn" onclick="prevStep()">Previous</button>
                    <input type="submit" value="Submit Blood Request">
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 0;
        const formSteps = document.querySelectorAll('.form-step');

        function nextStep() {
            formSteps[currentStep].classList.remove('active');
            currentStep = (currentStep + 1) % formSteps.length;
            formSteps[currentStep].classList.add('active');
        }

        function prevStep() {
            formSteps[currentStep].classList.remove('active');
            currentStep = (currentStep - 1 + formSteps.length) % formSteps.length;
            formSteps[currentStep].classList.add('active');
        }

        function toggleDeliveryOptions() {
            const deliveryMethod = document.getElementById('delivery_method').value;
            document.getElementById('pickup_options').style.display = deliveryMethod === 'pickup' ? 'block' : 'none';
            document.getElementById('delivery_options').style.display = deliveryMethod === 'delivery' ? 'block' : 'none';
        }

        function validateForm() {
            const contactInput = document.querySelector('input[name="contact"]');
            const nicInput = document.querySelector('input[name="nic"]');
            const lastDonatedInput = document.querySelector('input[name="last_donated"]');
            const weightInput = document.querySelector('input[name="weight"]');
            const bloodPressureInput = document.querySelector('input[name="blood_pressure"]');
            const ageInput = document.querySelector('input[name="age"]');
            const requestDateInput = document.querySelector('input[name="request_date"]');

            // Validate contact number
            if (!contactInput.checkValidity()) {
                alert(contactInput.title);
                return false;
            }

            // Validate NIC format (9 digits followed by 'V' or 12 digits)
            const nicPattern = /^\d{9}[V]$|^\d{12}$/;
            if (!nicPattern.test(nicInput.value)) {
                alert('NIC must be 9 digits followed by "V" or 12 digits.');
                return false;
            }

            // Validate Last Donated Date (must be at least 90 days ago)
            if (lastDonatedInput.value) {
                const lastDonatedDate = new Date(lastDonatedInput.value);
                const today = new Date();
                const daysSinceDonation = Math.floor((today - lastDonatedDate) / (1000 * 60 * 60 * 24));
                if (daysSinceDonation < 90) {
                    alert('You can only donate blood if it has been at least 90 days since your last donation.');
                    return false;
                }
            }

            // Validate Weight (must be at least 50 kg)
            if (weightInput.value < 50) {
                alert('Weight must be at least 50 kg.');
                return false;
            }

            // Validate Blood Pressure format (e.g., 120/80)
            const bloodPressurePattern = /^\d{2,3}\/\d{2,3}$/;
            if (!bloodPressurePattern.test(bloodPressureInput.value)) {
                alert('Blood pressure must be in the format "systolic/diastolic" (e.g., 120/80).');
                return false;
            }

            // Validate Age (must be at least 18)
            const age = parseInt(ageInput.value);
            if (age < 18) {
                alert('You must be at least 18 years old to request blood.');
                return false;
            }

            // Validate Request Date (cannot be today or in the past)
            const requestDate = new Date(requestDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Set the time to midnight to compare only the date
            if (requestDate <= today) {
                alert('The request date cannot be today or in the past.');
                return false;
            }

            return true; // If all validations pass
        }
    </script>


</body>

</html>