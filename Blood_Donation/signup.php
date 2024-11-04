<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup and Signin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 900px;
            height: 650px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
        }
        .form-container {
            width: 50%;
            transition: transform 0.5s ease, opacity 0.5s ease;
            position: absolute;
            top: 0;
            height: 100%;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container.signin {
            left: 0;
            background: #ffffff;
            transform: translateX(0);
            opacity: 1;
        }
        .form-container.signup {
            right: 0;
            background: #f8f9fa;
            transform: translateX(100%);
            opacity: 0;
        }
        .form-content {
            width: 100%;
            max-width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 5px;
            padding: 12px;
            border: 1px solid #ced4da;
        }
        .btn-custom {
            border-radius: 5px;
            padding: 12px;
            font-size: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            width: 100%;
        }
        .toggle-btn {
            margin-top: 20px;
            text-align: center;
        }
        .toggle-btn a {
            color: #007bff;
            text-decoration: none;
        }
        /* Scrollable form area */
        .scrollable-content {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }
        .scrollable-content::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 10px;
        }
        .scrollable-content::-webkit-scrollbar-track {
            background-color: #f0f0f0;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Signin Form -->
    <div class="form-container signin" id="signin-container">
        <div class="form-content">
            <h2>Sign In</h2>
            <form action="signin_process.php" method="post">
                <div class="form-group">
                    <label for="signin-email">Email address</label>
                    <input type="email" class="form-control" id="signin-email" name="signin_email" required>
                </div>
                <div class="form-group">
                    <label for="signin-password">Password</label>
                    <input type="password" class="form-control" id="signin-password" name="signin_password" required>
                </div>
                <button type="submit" class="btn-custom">Sign In</button>
                <div class="toggle-btn">
                    <a href="#" id="toggle-signup">Don't have an account? Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Signup Form -->
    <div class="form-container signup" id="signup-container">
        <div class="form-content">
            <h2>Sign Up</h2>
            <form action="signup_process.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="user-type">For:</label>
                    <select id="user-type" name="user_type" class="form-control" required>
                        <option value="" disabled selected>Select one</option>
                        <option value="user">User</option>
                        <option value="organization">Organization</option>
                        <option value="bloodbank">Blood Bank</option>
                        <option value="hospital">Hospital</option>
                    </select>
                </div>
                <div class="scrollable-content" id="scrollable-fields">
                    <!-- Dynamic form fields based on user type selection -->
                </div>
                <button type="submit" class="btn-custom">Sign Up</button>
                <div class="toggle-btn">
                    <a href="#" id="toggle-signin">Already have an account? Sign In</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    // Toggle between signin and signup
    function showSignup() {
        document.getElementById('signin-container').style.transform = 'translateX(-100%)';
        document.getElementById('signin-container').style.opacity = '0';
        document.getElementById('signup-container').style.transform = 'translateX(0)';
        document.getElementById('signup-container').style.opacity = '1';
    }

    function showSignin() {
        document.getElementById('signin-container').style.transform = 'translateX(0)';
        document.getElementById('signin-container').style.opacity = '1';
        document.getElementById('signup-container').style.transform = 'translateX(100%)';
        document.getElementById('signup-container').style.opacity = '0';
    }

    document.getElementById('toggle-signup').addEventListener('click', function(e) {
        e.preventDefault();
        showSignup();
    });

    document.getElementById('toggle-signin').addEventListener('click', function(e) {
        e.preventDefault();
        showSignin();
    });

    // Handle user type selection and add additional fields
    document.getElementById('user-type').addEventListener('change', function() {
        var userType = this.value;
        var scrollableFieldsContainer = document.getElementById('scrollable-fields');
        
        // Clear existing content
        scrollableFieldsContainer.innerHTML = '';
        
        // Add dynamic fields based on selected user type
        if (userType === 'user') {
            scrollableFieldsContainer.innerHTML = `
                <div class="form-group">
                    <label for="profile-picture">Profile Picture</label>
                    <input type="file" class="form-control" id="profile-picture" name="profile_picture" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" class="form-control" id="first-name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" class="form-control" id="last-name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="date-of-birth">Date of Birth</label>
                    <input type="date" class="form-control" id="date-of-birth" name="date_of_birth" required>
                </div>
                
  <div class="form-group">
            <label for="nic">NIC</label>
            <input type="text" class="form-control" id="nic" name="nic" maxlength="44" required>
        </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                 <div class="form-group">
                    <label for="blood-type">Blood Type</label>
                    <select class="form-control" id="blood-type" name="blood_type" required>
                        <option value="" disabled selected>Select blood type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            `;
        } else if (userType === 'organization') {
            scrollableFieldsContainer.innerHTML = `
                <div class="form-group">
                    <label for="org-name">Organization Name</label>
                    <input type="text" class="form-control" id="org-name" name="org_name" required>
                </div>
                <div class="form-group">
                    <label for="org-email">Organization Email</label>
                    <input type="email" class="form-control" id="org-email" name="org_email" required>
                </div>
                <div class="form-group">
                    <label for="org-password">Password</label>
                    <input type="password" class="form-control" id="org-password" name="org_password" required>
                </div>
                <div class="form-group">
                    <label for="org-registration-number">Organization Registration Number</label>
                    <input type="text" class="form-control" id="org-registration-number" name="org_registration_number" required>
                </div>
                <div class="form-group">
                    <label for="org-phone">Phone</label>
                    <input type="text" class="form-control" id="org-phone" name="org_phone" required>
                </div>
                <div class="form-group">
                    <label for="org-address">Organization Address</label>
                    <input type="text" class="form-control" id="org-address" name="org_address" required>
                </div>
                <div class="form-group">
                    <label for="org-website">Organization Website</label>
                    <input type="url" class="form-control" id="org-website" name="org_website">
                </div>
                <div class="form-group">
                    <label for="org-profile-picture">Profile Picture</label>
                    <input type="file" class="form-control" id="org-profile-picture" name="profile_picture" accept="image/*" required>
                </div>
            `;
        }
        else if (userType === 'hospital') {
    scrollableFieldsContainer.innerHTML = `
        <div class="form-group">
            <label for="hospital-name">Hospital Name</label>
            <input type="text" class="form-control" id="hospital-name" name="hospital_name" required>
        </div>
        <div class="form-group">
            <label for="hospital-address">Hospital Address</label>
            <input type="text" class="form-control" id="hospital-address" name="hospital_address" required>
        </div>
        <div class="form-group">
            <label for="hospital-phone">Hospital Phone</label>
            <input type="tel" class="form-control" id="hospital-phone" name="hospital_phone" required>
        </div>
        <div class="form-group">
            <label for="hospital-email">Hospital Email</label>
            <input type="email" class="form-control" id="hospital-email" name="hospital_email" required>
        </div>
        <div class="form-group">
            <label for="hospital-website">Hospital Website</label>
            <input type="url" class="form-control" id="hospital-website" name="hospital_website">
        </div>
        <div class="form-group">
            <label for="hospital-district">Hospital District</label>
            <select class="form-control" id="hospital-district" name="hospital_district" required>
                <option value="">Select District</option>
                <option value="Ampara">Ampara</option>
                <option value="Anuradhapura">Anuradhapura</option>
                <option value="Badulla">Badulla</option>
                <option value="Batticaloa">Batticaloa</option>
                <option value="Colombo">Colombo</option>
                <option value="Galle">Galle</option>
                <option value="Gampaha">Gampaha</option>
                <option value="Hambantota">Hambantota</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Kalutara">Kalutara</option>
                <option value="Kandy">Kandy</option>
                <option value="Kegalle">Kegalle</option>
                <option value="Kilinochchi">Kilinochchi</option>
                <option value="Kurunegala">Kurunegala</option>
                <option value="Mannar">Mannar</option>
                <option value="Matale">Matale</option>
                <option value="Matara">Matara</option>
                <option value="Moneragala">Moneragala</option>
                <option value="Mullaitivu">Mullaitivu</option>
                <option value="Nuwara Eliya">Nuwara Eliya</option>
                <option value="Polonnaruwa">Polonnaruwa</option>
                <option value="Puttalam">Puttalam</option>
                <option value="Ratnapura">Ratnapura</option>
                <option value="Trincomalee">Trincomalee</option>
                <option value="Vavuniya">Vavuniya</option>
            </select>
        </div>
        <div class="form-group">
            <label for="hospital-type">Hospital Type</label>
            <select class="form-control" id="hospital-type" name="hospital_type" required>
                <option value="">Select Type</option>
                <option value="Private">Private</option>
                <option value="Government">Government</option>
            </select>
        </div>
        <div class="form-group">
            <label for="hospital-image">Hospital Image</label>
            <input type="file" class="form-control" id="hospital-image" name="hospital_image" accept="image/*">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
    `;
}

        else if (userType === 'bloodbank') {
            scrollableFieldsContainer.innerHTML = `
                <div class="form-group">
                    <label for="bloodbank-name">Blood Bank Name</label>
                    <input type="text" class="form-control" id="bloodbank-name" name="bloodbank_name" required>
                </div>
                <div class="form-group">
                    <label for="contact-person">Contact Person</label>
                    <input type="text" class="form-control" id="contact-person" name="contact_person" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
            `;
        }
       
    });
</script>
</body>
</html>
