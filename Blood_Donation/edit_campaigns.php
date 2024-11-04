<?php
// db.php: Include your database connection
include 'db.php';

// Get the campaign ID from the URL
if (isset($_GET['campaign_id'])) {
    $campaign_id = $_GET['campaign_id'];

    // Fetch the campaign details
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE campaign_id = ?");
    $stmt->bind_param("i", $campaign_id);
    $stmt->execute();
    $campaign = $stmt->get_result()->fetch_assoc();

    // Fetch additional images
    $result_images = $conn->query("SELECT * FROM campaign_additional_images WHERE campaign_id = $campaign_id");
// Fetch funding breakdown
$result_breakdown = $conn->query("SELECT * FROM funding_breakdown WHERE campaign_id = $campaign_id");
    // Fetch videos
    $result_videos = $conn->query("SELECT * FROM campaign_videos WHERE campaign_id = $campaign_id");
} else {
    die("Campaign ID not provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update campaign details
    $title = $_POST['title'];
    $category = $_POST['category'];
    $goal_amount = $_POST['goal_amount'];
    $donate_amount = $_POST['donate_amount'];
    $duration = $_POST['duration'];
    $district = $_POST['district'];
    $description = $_POST['description'];
    $campaign_full_story = $_POST['campaign_full_story'];

    // Handle image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        // Assuming main_image upload logic here
        $main_image_path = 'uploads/' . basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $main_image_path);
        // Update the campaign with the new main image path
        $stmt = $conn->prepare("UPDATE campaigns SET images = ? WHERE campaign_id = ?");
        $stmt->bind_param("si", $main_image_path, $campaign_id);
        $stmt->execute();
    }

    // Update other campaign fields
    $stmt = $conn->prepare("UPDATE campaigns SET title = ?, category = ?, goal_amount = ?, donate_amount = ?, duration = ?, district = ?, description = ?, campaign_full_story = ? WHERE campaign_id = ?");
    $stmt->bind_param("ssddssssi", $title, $category, $goal_amount, $donate_amount, $duration, $district, $description, $campaign_full_story, $campaign_id);
    $stmt->execute();

    // Handle additional images upload
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            $image_path = 'uploads/' . basename($_FILES['additional_images']['name'][$key]);
            move_uploaded_file($tmp_name, $image_path);
            $stmt = $conn->prepare("INSERT INTO campaign_additional_images (campaign_id, image_url) VALUES (?, ?)");
            $stmt->bind_param("is", $campaign_id, $image_path);
            $stmt->execute();
        }
    }

    // Handle deletion of additional images
    if (isset($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $image_id) {
            $stmt = $conn->prepare("DELETE FROM campaign_additional_images WHERE image_id = ?");
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
        }
    }


    
// Update funding breakdown
if (isset($_POST['breakdown_description'])) {
    foreach ($_POST['breakdown_description'] as $key => $description) {
        if (!empty($description)) {
            $amount = $_POST['breakdown_amount'][$key];
            $breakdown_id = $_POST['breakdown_id'][$key];
            $status = $_POST['breakdown_status'][$key]; // Fetch the status from the dropdown

            if (!empty($breakdown_id)) {
                // Update existing breakdown
                $stmt = $conn->prepare("UPDATE funding_breakdown SET item_description = ?, amount = ?, status = ? WHERE breakdown_id = ?");
                $stmt->bind_param("sdsi", $description, $amount, $status, $breakdown_id);  // Change to "sdsi" for string, decimal, string, integer
                $stmt->execute();

                // Check for errors in the update
                if ($stmt->error) {
                    echo "Error updating breakdown: " . $stmt->error;
                }
            } else {
                // Insert new breakdown
                $stmt = $conn->prepare("INSERT INTO funding_breakdown (campaign_id, item_description, amount, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issd", $campaign_id, $description, $amount, $status);  // Change to "issd" for integer, string, string, decimal
                $stmt->execute();

                // Check for errors in the insert
                if ($stmt->error) {
                    echo "Error inserting breakdown: " . $stmt->error;
                }
            }
        }
    }
}



    // Handle deletion of videos
    if (isset($_POST['delete_videos'])) {
        foreach ($_POST['delete_videos'] as $video_id) {
            $stmt = $conn->prepare("DELETE FROM campaign_videos WHERE video_id = ?");
            $stmt->bind_param("i", $video_id);
            $stmt->execute();
        }
    }

    // Handle video URLs
    if (isset($_POST['video_url'])) {
        foreach ($_POST['video_url'] as $url) {
            if (!empty($url)) {
                $stmt = $conn->prepare("INSERT INTO campaign_videos (campaign_id, video_url) VALUES (?, ?)");
                $stmt->bind_param("is", $campaign_id, $url);
                $stmt->execute();
            }
        }
    }

    // Redirect or show success message
    header("Location: manage_campaigns.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Campaign</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: 'Arial', sans-serif; /* Simple, modern font */
        background-color: #eef2f5; /* Soft background color */
        color: #333; /* Dark text for readability */
        padding: 20px;
        line-height: 1.6; /* Better readability */
    }

    .container {
        max-width: 900px; /* Maximum width for the form */
        margin: 0 auto; /* Center container */
        background-color: #ffffff; /* White background for the form */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
        padding: 30px; /* Padding for content spacing */
    }

    h2 {
        text-align: center; /* Center align title */
        font-size: 2.2em; /* Larger font size */
        color: #007bff; /* Primary color */
        margin-bottom: 30px; /* Space below title */
    }

    .form-group {
        margin-bottom: 25px; /* Increased spacing between form groups */
    }

    label {
        font-weight: bold; /* Bold labels for emphasis */
        margin-bottom: 8px; /* Space below labels */
        display: block; /* Labels on new line */
        color: #555; /* Slightly muted color for labels */
    }

    input[type="text"], 
    input[type="number"], 
    textarea, 
    input[type="file"] {
        border: 1px solid #ccc; /* Light gray border */
        border-radius: 5px; /* Rounded borders */
        padding: 12px; /* Padding for inputs */
        width: 100%; /* Full width inputs */
        transition: border-color 0.3s; /* Smooth transition */
        box-sizing: border-box; /* Ensure padding is included in width */
    }

    input[type="text"]:focus, 
    input[type="number"]:focus, 
    textarea:focus {
        border-color: #007bff; /* Blue border on focus */
        outline: none; /* Remove default outline */
    }

    .image-preview {
        display: flex; /* Use flexbox for image alignment */
        flex-wrap: wrap; /* Wrap images to next line if necessary */
        margin-top: 10px; /* Space above image previews */
    }

    .image-preview div {
        margin-right: 15px; /* Space between images */
        margin-bottom: 15px; /* Space below images */
        position: relative; /* Position for delete checkbox */
    }

    .image-preview img {
        width: 120px; /* Fixed width for images */
        height: 80px; /* Fixed height for images */
        border-radius: 5px; /* Rounded corners for images */
        border: 1px solid #dee2e6; /* Border around images */
    }

    .btn {
        border-radius: 5px; /* Rounded corners for buttons */
        padding: 10px 20px; /* Padding for buttons */
        font-size: 16px; /* Font size for buttons */
        transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
        cursor: pointer; /* Pointer on hover */
        border: none; /* Remove border */
        color: #ffffff; /* White text */
    }

    .btn-primary {
        background-color: #007bff; /* Primary button color */
    }

    .btn-danger {
        background-color: #dc3545; /* Danger button color */
    }

    .btn-success {
        background-color: #28a745; /* Success button color */
    }

    .btn:hover {
        opacity: 0.9; /* Slightly transparent on hover */
        transform: translateY(-2px); /* Lift effect on hover */
    }

    h5 {
        margin-top: 25px; /* Space above section titles */
        color: #007bff; /* Color for section titles */
        border-bottom: 2px solid #007bff; /* Underline for section titles */
        padding-bottom: 10px; /* Space below titles */
    }

    #funding-breakdown, #video-section {
        background-color: #f8f9fa; /* Light background for sections */
        border: 1px solid #ddd; /* Border around sections */
        border-radius: 5px; /* Rounded corners for sections */
        padding: 15px; /* Padding for sections */
        margin-top: 10px; /* Space above sections */
    }

    #video-section .form-group {
        margin-bottom: 15px; /* Spacing for video input groups */
    }

    .remove-video-btn {
        background-color: #f8d7da; /* Light red for delete button */
        color: #721c24; /* Dark red text */
        border: none; /* Remove border */
        padding: 6px 12px; /* Padding for button */
        border-radius: 5px; /* Rounded corners */
        margin-top: 5px; /* Space above */
        cursor: pointer; /* Pointer on hover */
        transition: background-color 0.3s; /* Smooth transition */
    }

    .remove-video-btn:hover {
        background-color: #f5c6cb; /* Darker red on hover */
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px; /* Reduced padding for smaller screens */
        }

        .image-preview img {
            width: 80px; /* Smaller images on mobile */
            height: 60px; /* Adjust height on mobile */
        }

        h2 {
            font-size: 1.8em; /* Smaller heading on mobile */
        }
    }
</style>


</head>
<body>

<div class="container">
    <h2>Edit Campaign</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($campaign['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($campaign['category']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="goal_amount">Goal Amount:</label>
            <input type="number" step="0.01" name="goal_amount" class="form-control" value="<?php echo htmlspecialchars($campaign['goal_amount']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="donate_amount">Donated Amount:</label>
            <input type="number" step="0.01" name="donate_amount" class="form-control" value="<?php echo htmlspecialchars($campaign['donate_amount']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="duration">Duration:</label>
            <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($campaign['duration']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="district">District:</label>
            <input type="text" name="district" class="form-control" value="<?php echo htmlspecialchars($campaign['district']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($campaign['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="campaign_full_story">Full Story:</label>
            <textarea name="campaign_full_story" class="form-control" rows="4" required><?php echo htmlspecialchars($campaign['campaign_full_story']); ?></textarea>
        </div>

        <h5>Main Image</h5>
        <div class="form-group image-preview">
            <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Main Image" style="width: 150px; border-radius: 4px; margin-bottom: 10px;">
            <input type="file" name="main_image" class="form-control">
        </div>

        <h5>Additional Images</h5>
        <div class="image-preview">
            <?php while ($image = $result_images->fetch_assoc()): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Additional Image" style="width: 100px; border-radius: 4px;">
                    <input type="checkbox" name="delete_images[]" value="<?php echo $image['image_id']; ?>"> Delete
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="form-group">
            <label for="additional_images">Upload New Images:</label>
            <input type="file" name="additional_images[]" class="form-control" multiple>
        </div>

        <h5>Funding Breakdown</h5>
<div id="funding-breakdown">
    <?php while ($breakdown = $result_breakdown->fetch_assoc()): ?>
        <div class="form-group">
            <input type="hidden" name="breakdown_id[]" value="<?php echo $breakdown['breakdown_id']; ?>">
            
            <label for="breakdown_description">Description:</label>
            <input type="text" name="breakdown_description[]" class="form-control" value="<?php echo htmlspecialchars($breakdown['item_description']); ?>">
            
            <label for="breakdown_amount">Amount:</label>
            <input type="number" step="0.01" name="breakdown_amount[]" class="form-control" value="<?php echo htmlspecialchars($breakdown['amount']); ?>">
            
            <label for="breakdown_status">Current Status:</label>
            <input type="text" name="breakdown_status_readonly" class="form-control" value="<?php echo htmlspecialchars($breakdown['status']); ?>" readonly>
            
            <label for="breakdown_status_edit">Edit Status:</label>
            <select name="breakdown_status[]" class="form-control">
                <option value="not completed" <?php echo ($breakdown['status'] == 'not completed') ? 'selected' : ''; ?>>Not Completed</option>
                <option value="processing" <?php echo ($breakdown['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="completed" <?php echo ($breakdown['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
    <?php endwhile; ?>
</div>






        <h5>Videos</h5>
        <div id="video-section">
            <?php while ($video = $result_videos->fetch_assoc()): ?>
                <div class="form-group">
                    <input type="hidden" name="delete_videos[]" value="<?php echo $video['video_id']; ?>">
                    <label for="video_url">Video URL:</label>
                    <input type="text" name="video_url[]" class="form-control" value="<?php echo htmlspecialchars($video['video_url']); ?>">
                    <button type="button" class="btn btn-danger" onclick="removeVideo(this)">Delete</button>
                </div>
            <?php endwhile; ?>
            <button type="button" class="btn btn-primary" onclick="addVideo()">Add New Video</button>
        </div>

        <button type="submit" class="btn btn-success">Update Campaign</button>
    </form>
</div>


    <script>
        function addBreakdown() {
            const breakdownHTML = `
                <div class="form-group">
                    <label for="breakdown_description">Description:</label>
                    <input type="text" name="breakdown_description[]" class="form-control" required>
                    <label for="breakdown_amount">Amount:</label>
                    <input type="number" step="0.01" name="breakdown_amount[]" class="form-control" required>
                </div>`;
            document.getElementById('funding-breakdown').insertAdjacentHTML('beforeend', breakdownHTML);
        }

        function addVideo() {
            const videoHTML = `
                <div class="form-group">
                    <label for="video_url">Video URL:</label>
                    <input type="text" name="video_url[]" class="form-control" required>
                    <button type="button" class="btn btn-danger" onclick="removeVideo(this)">Delete</button>
                </div>`;
            document.getElementById('video-section').insertAdjacentHTML('beforeend', videoHTML);
        }

        function removeVideo(element) {
            element.parentElement.remove();
        }
    </script>
</body>
</html>
