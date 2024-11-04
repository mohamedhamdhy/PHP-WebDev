<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    // Redirect to login page if not logged in
    header("Location: signup.php");
    exit();
}

// Get user ID and user type from session
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form input values
    $title = $_POST['title'];
    $category = $_POST['category'];
    $goal_amount = $_POST['goal_amount'];
    $duration = $_POST['duration'];
    $district = $_POST['district'];
    $description = $_POST['description'];
    $campaign_full_story = $_POST['campaign_full_story'];

    // Handle the main image upload
    $main_image = $_FILES['main_image']['name'];
    $main_image_tmp = $_FILES['main_image']['tmp_name'];
    $main_image_path = 'uploads/' . $main_image;

    // Define the uploads directory
    $uploads_dir = 'uploads/';

    // Ensure the uploads directory exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // Create directory if it does not exist
    }

    // Move uploaded main image to uploads directory
    if (move_uploaded_file($main_image_tmp, $main_image_path)) {
        // Image uploaded successfully
    } else {
        echo "Error uploading main image.";
        exit();
    }

    // Connect to the database
    require 'db.php'; // Your DB connection file

    // Determine user type
    if ($user_type == 'organization') {
        $organization_id = $user_id; // Set the user ID as the organization ID
        $normal_user_id = NULL; // Set normal user ID as NULL
    } else {
        $normal_user_id = $user_id; // Set the user ID as the normal user ID
        $organization_id = NULL; // Set organization ID as NULL
    }

    // Insert into the campaigns table
    $stmt = $conn->prepare("INSERT INTO campaigns (title, category, goal_amount, duration, district, description, images, normal_user_id, organization_id, campaign_full_story, campaign_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $campaign_status = 'pending'; 

   $stmt->bind_param("ssdisssiiss", $title, $category, $goal_amount, $duration, $district, $description, $main_image_path, $normal_user_id, $organization_id, $campaign_full_story, $campaign_status);

    if ($stmt->execute()) {
        $campaign_id = $conn->insert_id; // Get the last inserted campaign ID
        
        // Handle funding breakdown items
        $funding_items = $_POST['funding_items']; // Array of funding items descriptions
        $amounts = $_POST['amounts']; // Array of amounts for each funding item
        
        for ($i = 0; $i < count($funding_items); $i++) {
            $stmt = $conn->prepare("INSERT INTO funding_breakdown (campaign_id, item_description, amount) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $campaign_id, $funding_items[$i], $amounts[$i]);
            $stmt->execute();
        }

        // Handle additional images upload
        $additional_images = $_FILES['additional_images']['name'];
        $additional_images_tmp = $_FILES['additional_images']['tmp_name'];

        for ($i = 0; $i < count($additional_images); $i++) {
            $image_path = $uploads_dir . $additional_images[$i];
            if (move_uploaded_file($additional_images_tmp[$i], $image_path)) {
                // Insert image URL into database
                $stmt = $conn->prepare("INSERT INTO campaign_additional_images (campaign_id, image_url) VALUES (?, ?)");
                $stmt->bind_param("is", $campaign_id, $image_path);
                $stmt->execute();
            } else {
                echo "Error uploading additional images.";
                exit();
            }
        }

        // Handle video URLs
        $videos = $_POST['videos']; // Array of video URLs
        foreach ($videos as $video) {
            $stmt = $conn->prepare("INSERT INTO campaign_videos (campaign_id, video_url) VALUES (?, ?)");
            $stmt->bind_param("is", $campaign_id, $video);
            $stmt->execute();
        }

        // Success message and redirect
        echo '<script>
            alert("Campaign created successfully!");
            window.location.href = "funding.php"; // Replace with the actual URL of the funding page
        </script>';

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Campaign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-section {
            margin-bottom: 20px;
        }
        .form-section label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        .form-section input,
        .form-section textarea,
        .form-section select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-section textarea {
            height: 120px;
            resize: vertical;
        }
        .step-content {
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        .step-content.active {
            display: block;
            opacity: 1;
        }
        .navigation-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .navigation-buttons button {
            margin: 0 10px;
            padding: 12px 20px;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .navigation-buttons .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .navigation-buttons .btn-secondary:hover {
            background-color: #5a6268;
        }
        .navigation-buttons .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .navigation-buttons .btn-primary:hover {
            background-color: #0056b3;
        }
        .navigation-buttons .btn-primary:disabled {
            background-color: #b3d7ff;
            cursor: not-allowed;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
        }
        .step-indicator {
            text-align: center;
            margin-bottom: 20px;
        }
        .step-indicator span {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            margin: 0 5px;
        }
        .step-indicator .active {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <form id="campaignForm" action="crowd_funding_campaign.php" method="POST" enctype="multipart/form-data">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <span class="active">1</span>
            <span>2</span>
            <span>3</span>
        </div>

        <!-- Step 1: Campaign Basics -->
        <div id="step1" class="step-content active">
            <h4 class="mb-4">Step 1: Campaign Basics</h4>

            <div class="form-section">
                <label for="title">Campaign Title:</label>
                <input type="text" id="title" name="title" required>
                <div class="invalid-feedback" id="titleFeedback">Title is required.</div>
            </div>

            <div class="form-section">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
                <div class="invalid-feedback" id="categoryFeedback">Category is required.</div>
            </div>

            <div class="form-section">
                <label for="goal_amount">Goal Amount:</label>
                <input type="number" id="goal_amount" name="goal_amount" required>
                <div class="invalid-feedback" id="goalAmountFeedback">Goal amount is required.</div>
            </div>

            <div class="form-section">
                <label for="duration">Campaign Duration (days):</label>
                <input type="number" id="duration" name="duration" required>
                <div class="invalid-feedback" id="durationFeedback">Duration is required.</div>
            </div>

            <div class="form-section">
                <label for="district">District:</label>
                <select id="district" name="district" required>
                    <option value="">Select District</option>
                    <option value="Colombo">Colombo</option>
                    <!-- Add other districts here -->
                </select>
                <div class="invalid-feedback" id="districtFeedback">District is required.</div>
            </div>

            <div class="form-section">
                <label for="main_image">Main Image:</label>
                <input type="file" id="main_image" name="main_image" required>
                <div class="invalid-feedback" id="mainImageFeedback">Main image is required.</div>
            </div>
        </div>

        <!-- Step 2: Campaign Description -->
        <div id="step2" class="step-content">
            <h4 class="mb-4">Step 2: Campaign Description</h4>

            <div class="form-section">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
                <div class="invalid-feedback" id="descriptionFeedback">Description is required.</div>
            </div>

            <div class="form-section">
                <label for="campaign_full_story">Full Campaign Story:</label>
                <textarea id="campaign_full_story" name="campaign_full_story" required></textarea>
                <div class="invalid-feedback" id="fullStoryFeedback">Full story is required.</div>
            </div>
        </div>

        <!-- Step 3: Additional Details -->
        <div id="step3" class="step-content">
            <h4 class="mb-4">Step 3: Additional Details</h4>

            <div class="form-section">
                <h5 class="mb-3">Funding Breakdown:</h5>
                <div id="funding-breakdown-container">
                    <div class="funding-breakdown-item mb-3">
                        <label for="funding_item">Description:</label>
                        <input type="text" name="funding_items[]" required>
                        <label for="amount">Amount:</label>
                        <input type="number" name="amounts[]" required>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="addFundingBreakdown()">Add More Funding Breakdown</button>
            </div>

            <div class="form-section">
                <label for="additional_images">Additional Images:</label>
                <div id="additional-images-container" class="mb-3">
                    <input type="file" id="additional_images" name="additional_images[]" multiple>
                </div>
                <button type="button" class="btn btn-primary" onclick="addImageField()">Add More Images</button>
            </div>

            <div class="form-section">
                <label for="videos">Video Links (URLs):</label>
                <div id="video-links-container" class="mb-3">
                    <input type="text" id="videos" name="videos[]" placeholder="Video URL"><br>
                </div>
                <button type="button" class="btn btn-primary" onclick="addVideoField()">Add More Video Links</button>
            </div>
        </div>

        <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
            <button type="button" class="btn btn-secondary" id="nextBtn" onclick="changeStep(1)">Next</button>
            <input type="submit" value="Submit" class="btn btn-primary" id="submitBtn" style="display: none;">
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 3;

    function validateStep(step) {
        const stepElement = document.getElementById(`step${step}`);
        const inputs = stepElement.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    function changeStep(stepChange) {
        if (stepChange === 1 && !validateStep(currentStep)) {
            return;
        }

        document.getElementById(`step${currentStep}`).classList.remove('active');
        currentStep += stepChange;
        document.getElementById(`step${currentStep}`).classList.add('active');

        document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-block';
        document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        
        updateStepIndicator();
    }

    function updateStepIndicator() {
        const steps = document.querySelectorAll('.step-indicator span');
        steps.forEach((step, index) => {
            step.classList.toggle('active', index + 1 === currentStep);
        });
    }

    function addFundingBreakdown() {
        const container = document.getElementById('funding-breakdown-container');
        const newItem = document.createElement('div');
        newItem.classList.add('funding-breakdown-item', 'mb-3');
        newItem.innerHTML = `
            <label for="funding_item">Description:</label>
            <input type="text" name="funding_items[]" required>
            <label for="amount">Amount:</label>
            <input type="number" name="amounts[]" required>
        `;
        container.appendChild(newItem);
    }

    function addVideoField() {
        const container = document.getElementById('video-links-container');
        const newField = document.createElement('input');
        newField.setAttribute('type', 'text');
        newField.setAttribute('name', 'videos[]');
        newField.setAttribute('placeholder', 'Video URL');
        container.appendChild(newField);
        container.appendChild(document.createElement('br'));
    }

    function addImageField() {
        const container = document.getElementById('additional-images-container');
        const newImageField = document.createElement('input');
        newImageField.setAttribute('type', 'file');
        newImageField.setAttribute('name', 'additional_images[]');
        container.appendChild(newImageField);
        container.appendChild(document.createElement('br'));
    }
</script>

</body>
</html>
