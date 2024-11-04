<?php
session_start(); // Start the session
include('db.php'); // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    // If the organization is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

// Retrieve organization ID from session
$organization_id = $_SESSION['user_id'];

// Fetch organization details from the database
$query = "SELECT * FROM organization WHERE organization_id = '$organization_id'";
$result = mysqli_query($conn, $query);
$organization_data = mysqli_fetch_assoc($result);

if ($organization_data) {
    // Store organization details in variables
    $organization_name = $organization_data['organization_name'];
    $organization_registration_number = $organization_data['organization_registration_number'];
    $organization_phone = $organization_data['organization_phone'];
    $organization_address = $organization_data['organization_address'];
    $organization_code = $organization_data['organization_registration_number']; // Reused for code
} else {
    echo "Organization details not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Request Form</title>
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
        textarea {
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
    <form id="bloodRequestForm" action="submit_organization_blood_request.php" method="POST">
        <h2>Organization Details</h2>

        <!-- Step 1: Display Organization Details -->
        <div class="form-step active">
            <label>Organization Name:</label>
            <input type="text" name="organization_name" value="<?php echo htmlspecialchars($organization_name); ?>" readonly><br>

            <label>Organization Registration Number:</label>
            <input type="text" name="organization_registration_number" value="<?php echo htmlspecialchars($organization_registration_number); ?>" readonly><br>

            <label>Organization Address:</label>
            <input type="text" name="organization_address" value="<?php echo htmlspecialchars($organization_address); ?>" readonly><br>

            <label>Contact Number:</label>
            <input type="text" name="organization_phone" value="<?php echo htmlspecialchars($organization_phone); ?>" readonly><br>

            <label>Organization Code:</label>
            <input type="text" name="organization_code" value="<?php echo htmlspecialchars($organization_code); ?>" readonly><br>

            <!-- Hidden field for organization_id -->
            <input type="hidden" name="organization_id" value="<?php echo htmlspecialchars($organization_id); ?>">

            <div class="btn-container">
                <button type="button" class="next-btn" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 2: Blood Request Details -->
        <div class="form-step">
            <label>Reason for Request:</label>
            <textarea name="reason" placeholder="Explain the reason for the blood request" required></textarea><br>

            <label>Blood Type:</label>
            <input type="text" name="blood_type" placeholder="Enter required blood type" required><br>

            <label>Quantity (in units):</label>
            <input type="number" name="quantity" placeholder="Enter the required units of blood" required><br>

            <label>Request Date:</label>
            <input type="date" name="request_date" required><br>

            <div class="btn-container">
                <button type="button" class="prev-btn" onclick="prevStep()">Previous</button>
                <button type="button" class="next-btn" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 3: Delivery Details -->
        <div class="form-step">
            <label>Delivery:</label>
            <select name="delivery">
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select><br>

            <label>Delivery Address:</label>
            <input type="text" name="delivery_address" placeholder="Enter delivery address" required><br>

            <label>Delivery Instructions:</label>
            <textarea name="delivery_instructions" placeholder="Enter any delivery instructions"></textarea><br>

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
</script>

</body>
</html>
