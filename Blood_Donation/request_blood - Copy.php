<?php
// Database configuration
$host = 'localhost'; // your host
$db = 'lifebridge'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        exit;
    }

    // Prepare and bind
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO user_requests (name, age, blood_type, location, contact, nic, reason, delivery, delivery_address, delivery_instructions, quantity, last_donated, weight, blood_pressure, medical_issues) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Make sure the bind_param matches the columns in the table
    $stmt->bind_param(
        "sisssssssssssss",
        $data['name'],
        $data['age'],
        $data['bloodType'],
        $data['location'],
        $data['contact'],
        $data['nic'],
        $data['reason'],
        $data['delivery'],
        $data['deliveryAddress'],
        $data['delivery_instructions'],
        $data['quantity'],
        $data['lastDonated'],
        $data['weight'],
        $data['bloodPressure'],
        $data['medicalIssues']
    );


    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Blood request submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit; // End the script after processing the form
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Blood</title>
    <style>
       body {
    font-family: 'Poppins', Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
    transition: background-color 0.3s ease-in-out;
}

.container {
    width: 50%;
    margin: 40px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-top: 10px solid #e74c3c;
    animation: fadeIn 0.8s ease-in-out;
}

h1 {
    text-align: center;
    color: #e74c3c;
    font-size: 28px;
    margin-bottom: 20px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    animation: slideIn 1s ease-in-out;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #333;
}

input,
select,
textarea {
    width: 100%;
    padding: 12px 15px;
    margin-top: 8px;
    border: 2px solid #e6e6e6;
    border-radius: 5px;
    background-color: #f5f5f5;
    transition: all 0.3s ease-in-out;
    font-size: 14px;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #e74c3c;
    background-color: #fff;
    outline: none;
    box-shadow: 0 0 8px rgba(231, 76, 60, 0.2);
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 25px;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
}

button:hover {
    background-color: #c0392b;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
}

#responseMessage {
    margin-top: 20px;
    text-align: center;
    font-size: 16px;
    color: #333;
    animation: fadeIn 1s ease-in-out;
}

/* Animation keyframes */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.additional-fields {
    display: none;
}

input,
textarea,
select {
    font-family: 'Poppins', Arial, sans-serif;
}

/* Styling delivery fields */
#deliveryDetails {
    margin-top: 15px;
    padding: 15px;
    border: 1px solid #f1c40f;
    background-color: #fffdf0;
    border-radius: 5px;
}

/* Placeholder and form input transition */
input::placeholder,
textarea::placeholder {
    color: #888;
    font-style: italic;
}

input:focus::placeholder,
textarea:focus::placeholder {
    color: #e74c3c;
    transition: color 0.3s ease-in-out;
}

/* Styling for the blood-themed details */
input[type="number"]:hover,
input[type="tel"]:hover,
textarea:hover,
input[type="text"]:hover {
    border-color: #e74c3c;
}


    </style>
</head>

<body>
    <div class="container">
        <h1>Request Blood for the individual</h1>
        <form id="bloodRequestForm">
            <label for="name">Patient Name:</label>
            <input type="text" id="name" required>

            <label for="age">Patient Age:</label>
            <input type="number" id="age" required>

            <label for="bloodType">Blood Type:</label>
            <select id="bloodType" required>
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

            <label for="location">Patient Location:</label>
            <input type="text" id="location" required>

            <label for="contact">Patient Contact Number:</label>
            <input type="tel" id="contact" required>

            <label for="nic">Patient NIC No:</label>
            <input type="text" id="nic" required minlength="10" maxlength="12" placeholder="Enter NIC No">


            <label for="reason">Reason for Request:</label>
            <textarea id="reason" rows="4" required></textarea>

            <label for="quantity">Quantity of Blood Needed (in units):</label>
            <input type="number" id="quantity" required min="1">

            <label for="delivery">Delivery Option:</label>
            <select id="delivery" required>
                <option value="No">Pick up</option>
                <option value="Yes">Yes, deliver to my home</option>
            </select>

            <div class="additional-fields" id="deliveryDetails">
                <label for="deliveryAddress">Delivery Address:</label>
                <input type="text" id="deliveryAddress">

                <label for="deliveryTime">Preferred Delivery Time:</label>
                <input type="text" id="delivery_instructions" placeholder="e.g., 2 PM or ASAP">
            </div>

            <label for="lastDonated">Last Donated Date:</label>
            <input type="date" id="lastDonated">

            <label for="weight">Weight (in kg):</label>
            <input type="number" id="weight" required>

            <label for="bloodPressure">Blood Pressure (e.g., 120/80):</label>
            <input type="text" id="bloodPressure" required>

            <label for="medicalIssues">Any Medical Issues:</label>
            <textarea id="medicalIssues" rows="4"></textarea>

            <button type="submit">Submit Request</button>
        </form>
        <div id="responseMessage"></div>
    </div>

    <script>
        document.getElementById('bloodRequestForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Validate weight (must be 50kg or more)
            const weight = document.getElementById('weight').value;
            if (weight < 50) {
                alert('Weight must be at least 50 kg.');
                return;
            }

            // Validate blood pressure (90/50 mmHg to 180/100 mmHg)
            const bloodPressure = document.getElementById('bloodPressure').value;
            const bpRegex = /^(\d{2,3})\/(\d{2,3})$/;
            const match = bloodPressure.match(bpRegex);

            if (!match) {
                alert('Please enter a valid blood pressure in the format: systolic/diastolic (e.g., 120/80).');
                return;
            }

            const systolic = parseInt(match[1], 10);
            const diastolic = parseInt(match[2], 10);

            if (systolic < 90 || systolic > 180 || diastolic < 50 || diastolic > 100) {
                alert('Blood pressure must be between 90/50 and 180/100 mmHg.');
                return;
            }

            // Validate last donation date (at least 4 months ago)
            const lastDonated = document.getElementById('lastDonated').value;
            if (lastDonated) {
                const lastDonatedDate = new Date(lastDonated);
                const today = new Date();
                const fourMonthsAgo = new Date();
                fourMonthsAgo.setMonth(today.getMonth() - 4);

                if (lastDonatedDate > fourMonthsAgo) {
                    alert('Last donation must be at least 4 months ago.');
                    return;
                }
            }

            const quantity = document.getElementById('quantity').value;
            if (quantity > 10) {
                alert('The quantity of blood needed cannot exceed 10 units.');
                return;
            }

            // Gather form data
            const formData = {
                name: document.getElementById('name').value,
                age: document.getElementById('age').value,
                bloodType: document.getElementById('bloodType').value,
                location: document.getElementById('location').value,
                contact: document.getElementById('contact').value,
                nic: document.getElementById('nic').value, // Add NIC field here
                reason: document.getElementById('reason').value,
                quantity: document.getElementById('quantity').value,
                delivery: document.getElementById('delivery').value,
                deliveryAddress: document.getElementById('deliveryAddress').value,
                delivery_instructions: document.getElementById('delivery_instructions').value,
                lastDonated: document.getElementById('lastDonated').value,
                weight: document.getElementById('weight').value,
                bloodPressure: document.getElementById('bloodPressure').value,
                medicalIssues: document.getElementById('medicalIssues').value
            };

            console.log(formData); // Log form data to console for debugging

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('responseMessage').innerText = data.message;
                    if (data.success) {
                        document.getElementById('bloodRequestForm').reset();
                        document.getElementById('deliveryDetails').style.display = 'none'; // Reset delivery details visibility
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('responseMessage').innerText = 'An error occurred. Please try again.';
                });
        });

        document.getElementById('delivery').addEventListener('change', function() {
            const deliveryDetails = document.getElementById('deliveryDetails');
            if (this.value === 'Yes') {
                deliveryDetails.style.display = 'block'; // Show additional fields
            } else {
                deliveryDetails.style.display = 'none'; // Hide additional fields
            }
        });
    </script>
</body>

</html>