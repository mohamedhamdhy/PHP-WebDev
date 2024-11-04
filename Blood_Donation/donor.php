<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Form with NIC Calculation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background: linear-gradient(135deg, #e53935, #c62828);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.9);
            /* Glass effect */
            backdrop-filter: blur(10px);
            /* Frosted glass effect */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
            border: 2px solid white;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 16px;
            color: #ddd;
            position: relative;
            z-index: 1;
        }

        .step.active {
            color: #e53935;
        }

        .step.active .step-icon {
            background: #e53935;
            color: #fff;
        }

        .step-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background 0.3s, color 0.3s;
        }

        .progress-bar {
            position: absolute;
            top: 65px;
            left: 20px;
            width: calc(100% - 40px);
            height: 8px;
            background: #f5f5f5;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background: #e53935;
            width: 0;
            transition: width 0.4s ease;
        }

        .form-step {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .form-step.active {
            display: block;
            opacity: 1;
        }

        input {
            width: 100%;
            padding: 14px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: #e53935;
            box-shadow: 0 0 8px rgba(229, 57, 53, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background-color: #e53935;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #c62828;
            transform: translateY(-2px);
        }

        .error {
            color: #d32f2f;
            font-size: 14px;
            margin-top: -8px;
        }

        #result {
            margin-top: 20px;
            color: #333;
            font-size: 16px;
        }

        .result-card {
            background: #fff3e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #ffccbc;
        }

        .result-card h4 {
            margin: 0;
            color: #e53935;
            font-size: 20px;
            font-weight: 600;
        }

        .result-card p {
            margin: 10px 0;
        }

        /* Blood Group Dropdown Styling */
        select {
            width: 100%;
            padding: 14px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fff;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
            appearance: none;
            /* Hides the default dropdown arrow */
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23333" d="M2 0L0 2h4z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px;
        }

        select:focus {
            border-color: #e53935;
            box-shadow: 0 0 8px rgba(229, 57, 53, 0.2);
            outline: none;
        }

        select option {
            padding: 10px;
        }

        /* Additional hover effects for a polished feel */
        select:hover {
            border-color: #c62828;
            box-shadow: 0 0 8px rgba(229, 57, 53, 0.3);
        }

        /* Container for Gender Radio Buttons */
        .gender-container {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }

        .gender-container div {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .gender-container input[type="radio"] {
            display: none;
        }

        .gender-container label {
            font-size: 16px;
            color: #333;
            padding: 8px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
        }

        .gender-container input[type="radio"]:checked+label {
            background-color: #e53935;
            color: #fff;
            border-color: #e53935;
        }

        /* Hover effect for radio buttons */
        .gender-container label:hover {
            background-color: #fbe9e7;
            border-color: #e53935;
            color: #e53935;
        }
    </style>
</head>

<body style="background: url('blood.jpg') no-repeat center center fixed; background-size: cover;">

    <div class="form-container">
        <div class="step-indicator">
            <div class="step active">
                <div class="step-icon">1</div>
                Persoal
            </div>
            <div class="step">
                <div class="step-icon">2</div>
                Confirm
            </div>
            <div class="step">
                <div class="step-icon">3</div>
                NIC
            </div>
            <div class="step">
                <div class="step-icon">4</div>
                Confirm
            </div>
            <div class="step">
                <div class="step-icon">5</div>
                Success
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress" id="progressBar"></div>
        </div>

        <form id="multiStepForm" action="submit.php" method="post">
            <!-- Step 1: User Details -->
            <div class="form-step active" id="step1">
                <h3>Step 1: Enter Your Details</h3>

                <input type="text" id="fullName" name="fullName" placeholder="Full Name" required>
                <span class="error" id="nameError"></span>

                <input type="text" id="address" name="address" placeholder="Address" required>
                <span class="error" id="addressError"></span>

                <!-- Mobile Number 1 with Country Selector -->
                <input type="tel" id="mobile1" name="mobile1" placeholder="Mobile No. 1 (required)" required>
                <span class="error" id="mobile1Error"></span>

                <!-- Mobile Number 2 with Country Selector (optional) -->
                <input type="tel" id="mobile2" name="mobile2" placeholder="Mobile No. 2 (optional)">
                <span class="error" id="mobile2Error"></span>

                <input type="email" id="email" name="email" placeholder="Email" required>
                <span class="error" id="emailError"></span>

                
                <div>
                    <input type="radio" id="male" name="gender" value="male" required>
                    <label for="male">Male</label>
                </div>
                <div>
                    <input type="radio" id="female" name="gender" value="female" required>
                    <label for="female">Female</label>
                </div>
                <div>
                    <input type="radio" id="other" name="gender" value="other" required>
                    <label for="other">Other</label>
                </div>
                <span class="error" id="genderError"></span>

                <!-- Blood Group Dropdown -->
                <label for="bloodGroup">Blood Group:</label>
                <select id="bloodGroup" name="bloodGroup" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
                <span class="error" id="bloodGroupError"></span>

                <button type="button" onclick="validateStep1()">Next</button>
            </div>

            <!-- Step 2: Confirmation -->
            <div class="form-step" id="step2">
                <h3>Step 2: Confirm Your Details</h3>
                <p><strong>Full Name:</strong> <span id="confirmFullName"></span></p>
                <p><strong>Address:</strong> <span id="confirmAddress"></span></p>
                <p><strong>Mobile No. 1:</strong> <span id="confirmMobile1"></span></p>
                <p><strong>Mobile No. 2:</strong> <span id="confirmMobile2"></span></p>
                <p><strong>Email:</strong> <span id="confirmEmail"></span></p>

                <button type="button" onclick="goToStep3()">Next</button>
            </div>

            <!-- Step 3: NIC Details Calculation -->
            <div class="form-step" id="step3">
                <h3>Step 3: Enter Your NIC Number</h3>
                <input type="text" id="nic" name="nic" placeholder="Enter NIC Number" maxlength="12">
                <button type="button" onclick="calculateDetails()">Calculate Details</button>
                <div id="result"></div>
                <button type="button" onclick="goToStep4()">Next</button>
            </div>

            <!-- Step 4: Review and Edit -->
            <div class="form-step" id="step4">
                <h3>Step 4: Review Your Details</h3>
                <p><strong>Full Name:</strong> <input type="text" id="editFullName" name="editFullName"></p>
                <p><strong>Address:</strong> <input type="text" id="editAddress" name="editAddress"></p>
                <p><strong>Mobile No. 1:</strong> <input type="tel" id="editMobile1" name="editMobile1"></p>
                <p><strong>Mobile No. 2:</strong> <input type="tel" id="editMobile2" name="editMobile2"></p>
                <p><strong>Email:</strong> <input type="email" id="editEmail" name="editEmail"></p>
                <p><strong>NIC Details:</strong> <span id="editNicDetails"></span></p>
                <input type="hidden" id="nicDetails" name="nicDetails">

                <button type="button" onclick="goToStep3()">Edit</button>

                <button type="submit">Confirm and Submit</button>
            </div>



            <!-- Step 5: Success -->
            <div class="form-step" id="step5">
                <h3>Step 5: Success</h3>
                <div class="result-card">
                    <h4>Thank You for Submitting</h4>
                    <p>Your details have been successfully sent to Community Admin.</p>
                    <p>Wait for the approval from the admin.</p>
                    <p>Once you approved your profile will be shown in the donor list.</p>
                    <p><strong>Full Name:</strong> <span id="finalFullName"></span></p>
                    <p><strong>Address:</strong> <span id="finalAddress"></span></p>
                    <p><strong>Mobile No. 1:</strong> <span id="finalMobile1"></span></p>
                    <p><strong>Mobile No. 2:</strong> <span id="finalMobile2"></span></p>
                    <p><strong>Email:</strong> <span id="finalEmail"></span></p>
                    <p><strong>NIC Details:</strong> <span id="finalNicDetails"></span></p>
                </div>
            </div>


        </form>
    </div>

    <script>
        function goToStep(step) {
            const steps = document.querySelectorAll('.form-step');
            steps.forEach(s => s.classList.remove('active'));
            document.getElementById(`step${step}`).classList.add('active');

            // Update progress bar
            const progress = (step - 1) * 25;
            document.getElementById('progressBar').style.width = progress + '%';

            // Update step indicators
            const stepElements = document.querySelectorAll('.step');
            stepElements.forEach((s, i) => s.classList.toggle('active', i < step));
        }

        function validateStep1() {
            const fullName = document.getElementById('fullName').value.trim();
            const address = document.getElementById('address').value.trim();
            const mobile1 = document.getElementById('mobile1').value.trim();
            const email = document.getElementById('email').value.trim();

            let valid = true;

            if (fullName === '') {
                document.getElementById('nameError').textContent = 'Full Name is required';
                valid = false;
            } else {
                document.getElementById('nameError').textContent = '';
            }

            if (address === '') {
                document.getElementById('addressError').textContent = 'Address is required';
                valid = false;
            } else {
                document.getElementById('addressError').textContent = '';
            }

            if (mobile1 === '') {
                document.getElementById('mobile1Error').textContent = 'Mobile No. 1 is required';
                valid = false;
            } else {
                document.getElementById('mobile1Error').textContent = '';
            }

            if (email === '') {
                document.getElementById('emailError').textContent = 'Email is required';
                valid = false;
            } else {
                document.getElementById('emailError').textContent = '';
            }

            if (valid) {
                // Save data to Step 2
                document.getElementById('confirmFullName').textContent = fullName;
                document.getElementById('confirmAddress').textContent = address;
                document.getElementById('confirmMobile1').textContent = mobile1;
                document.getElementById('confirmMobile2').textContent = document.getElementById('mobile2').value.trim();
                document.getElementById('confirmEmail').textContent = email;


                goToStep(2);
            }
        }

        function goToStep3() {
            goToStep(3);
        }

        function calculateDetails() {
            const nic = document.getElementById('nic').value.trim();
            if (nic.length < 10 || (nic.length > 10 && nic.length < 12)) {
                alert("Invalid NIC Number. It must be 10 or 12 characters long.");
                return;
            }

            let gender = "Male";
            let year = nic.length === 10 ? "19" + nic.substring(0, 2) : nic.substring(0, 4);
            let days = parseInt(nic.length === 10 ? nic.substring(2, 5) : nic.substring(4, 7));

            if (days > 500) {
                gender = "Female";
                days -= 500;
            }

            let month = 0;
            let day = 0;

            const daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            for (let i = 0; i < daysInMonth.length; i++) {
                if (days <= daysInMonth[i]) {
                    month = i + 1;
                    day = days;
                    break;
                } else {
                    days -= daysInMonth[i];
                }
            }

            // Calculate the current year
            const currentYear = new Date().getFullYear();
            const age = currentYear - parseInt(year);

            // Show the NIC details
            document.getElementById('result').textContent = `Year of Birth: ${year}, Month: ${month}, Day: ${day}, Gender: ${gender}`;

            // Set hidden input with NIC details
            document.getElementById('nicDetails').value = `${year}|${month}|${day}|${gender}`;

            // Check if age is below 19 and show an error message if true
            if (age < 19) {
                alert("You must be at least 19 years old to donate blood. Please enter NIC details for a user aged 19 or above.");
                document.querySelector("#step3 button[type='button']").disabled = true; // Disable the "Next" button to prevent progression
                return;
            } else {
                document.querySelector("#step3 button[type='button']").disabled = false; // Re-enable the "Next" button if valid
            }

            // Update the review step with calculated NIC details
            document.getElementById('editNicDetails').textContent = `Year of Birth: ${year}, Month: ${month}, Day: ${day}, Gender: ${gender}`;
        }

        function goToStep4() {
            const resultText = document.getElementById('result').textContent;
            if (resultText.includes("must be at least 19 years old")) {
                alert("Cannot proceed. Please enter details for a valid user.");
                return; // Prevent moving to Step 4
            }

            document.getElementById('editFullName').value = document.getElementById('fullName').value.trim();
            document.getElementById('editAddress').value = document.getElementById('address').value.trim();
            document.getElementById('editMobile1').value = document.getElementById('mobile1').value.trim();
            document.getElementById('editMobile2').value = document.getElementById('mobile2').value.trim();
            document.getElementById('editEmail').value = document.getElementById('email').value.trim();
            document.getElementById('editNicDetails').textContent = document.getElementById('result').textContent;

            // Set final details in Step 5
            document.getElementById('finalFullName').textContent = document.getElementById('editFullName').value;
            document.getElementById('finalAddress').textContent = document.getElementById('editAddress').value;
            document.getElementById('finalMobile1').textContent = document.getElementById('editMobile1').value;
            document.getElementById('finalMobile2').textContent = document.getElementById('editMobile2').value;
            document.getElementById('finalEmail').textContent = document.getElementById('editEmail').value;
            document.getElementById('finalNicDetails').textContent = document.getElementById('editNicDetails').textContent;

            goToStep(4);
        }




        function goToStep4() {
            document.getElementById('editFullName').value = document.getElementById('fullName').value.trim();
            document.getElementById('editAddress').value = document.getElementById('address').value.trim();
            document.getElementById('editMobile1').value = document.getElementById('mobile1').value.trim();
            document.getElementById('editMobile2').value = document.getElementById('mobile2').value.trim();
            document.getElementById('editEmail').value = document.getElementById('email').value.trim();
            document.getElementById('editNicDetails').textContent = document.getElementById('result').textContent;

            // Set final details in Step 5
            document.getElementById('finalFullName').textContent = document.getElementById('editFullName').value;
            document.getElementById('finalAddress').textContent = document.getElementById('editAddress').value;
            document.getElementById('finalMobile1').textContent = document.getElementById('editMobile1').value;
            document.getElementById('finalMobile2').textContent = document.getElementById('editMobile2').value;
            document.getElementById('finalEmail').textContent = document.getElementById('editEmail').value;
            document.getElementById('finalNicDetails').textContent = document.getElementById('editNicDetails').textContent;

            // Prepare form data
            const formData = new FormData(document.getElementById('multiStepForm'));

            // Submit form data asynchronously
            fetch('submit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    // Check if submission was successful
                    if (result.includes('New record created successfully')) {
                        // Move to Step 5 on successful submission
                        goToStep(5);
                    } else {
                        alert('Submission failed: ' + result);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

</body>

</html>