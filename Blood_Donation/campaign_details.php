<?php
// Include database connection
session_start();
include 'db.php';


   // Initialize user_id and user_type to null if not set
   $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
   $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;

// Check if an id is passed in the URL
if (isset($_GET['id'])) {
    $campaign_id = $_GET['id'];

    // Fetch campaign details
    $campaign_query = $conn->prepare("SELECT c.*, 
        IFNULL(nu.normal_user_firstname, o.organization_name) AS creator_name, 
        IFNULL(nu.normal_user_profile_picture, o.organization_profile_picture) AS creator_profile_picture,
        IFNULL(nu.normal_user_location, o.organization_address) AS creator_location,
        IFNULL(nu.normal_user_type, o.organization_type) AS creator_type,
        nu.normal_user_id, o.organization_id  -- Add these two to compare IDs
    FROM campaigns c
    LEFT JOIN normal_user nu ON c.normal_user_id = nu.normal_user_id
    LEFT JOIN organization o ON c.organization_id = o.organization_id
    WHERE c.campaign_id = ?");
$campaign_query->bind_param('i', $campaign_id);
$campaign_query->execute();
$campaign_result = $campaign_query->get_result();

if ($campaign_result->num_rows > 0) {
    $campaign = $campaign_result->fetch_assoc();
} else {
    echo "Campaign not found.";
    exit;
}

// Determine if the logged-in user is the creator
$is_creator = false;

if ($user_type === 'user' && $campaign['normal_user_id'] == $user_id) {
    $is_creator = true;
} elseif ($user_type === 'organization' && $campaign['organization_id'] == $user_id) {
    $is_creator = true;
}


    // Fetch additional images
    $images_query = $conn->prepare("SELECT image_url FROM campaign_additional_images WHERE campaign_id = ?");
    $images_query->bind_param('i', $campaign_id);
    $images_query->execute();
    $images_result = $images_query->get_result();

    $additional_images = [];
    while ($image = $images_result->fetch_assoc()) {
        $additional_images[] = $image['image_url'];
    }

// Fetch funding breakdown including status
$funding_query = $conn->prepare("SELECT item_description, amount, status FROM funding_breakdown WHERE campaign_id = ?");
$funding_query->bind_param('i', $campaign_id);
$funding_query->execute();
$funding_result = $funding_query->get_result();

$funding_breakdown = [];
while ($funding = $funding_result->fetch_assoc()) {
    $funding_breakdown[] = $funding;
}


    // Fetch donations
    $donations_query = $conn->prepare("SELECT d.amount, d.created_at, 
            IFNULL(nu.normal_user_firstname, o.organization_name) AS donor_name 
        FROM donations d
        LEFT JOIN normal_user nu ON d.normal_user_id = nu.normal_user_id
        LEFT JOIN organization o ON d.organization_id = o.organization_id
        WHERE d.campaign_id = ?");
    $donations_query->bind_param('i', $campaign_id);
    $donations_query->execute();
    $donations_result = $donations_query->get_result();

    $donors = [];
    while ($donor = $donations_result->fetch_assoc()) {
        $donors[] = $donor;
    }

   // Fetch comments along with user_id, organization_id, and user type
$comments_query = $conn->prepare("
SELECT cc.crowd_comments, cc.created_at, 
       cc.normal_user_id, cc.organization_id, 
       cc.crowd_comments_id, -- Include the ID for edit/delete
       CASE 
           WHEN cc.normal_user_id IS NOT NULL THEN 'user' 
           ELSE 'organization' 
       END AS user_type, 
       CASE 
           WHEN cc.normal_user_id IS NOT NULL THEN nu.normal_user_firstname 
           ELSE o.organization_name 
       END AS commenter_name 
FROM crowd_comments cc
LEFT JOIN normal_user nu ON cc.normal_user_id = nu.normal_user_id
LEFT JOIN organization o ON cc.organization_id = o.organization_id
WHERE cc.campaign_id = ?
");
$comments_query->bind_param('i', $campaign_id);
$comments_query->execute();
$comments_result = $comments_query->get_result();

$comments = [];
while ($comment = $comments_result->fetch_assoc()) {
$comments[] = $comment;
}

} else {
    echo "No campaign selected.";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <style>
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .empty-state {
    padding: 15px; /* Padding for the empty state */
    background-color: #f9f9f9; /* Light background for contrast */
    border: 1px solid #ddd; /* Light border */
    border-radius: 5px; /* Rounded corners */
    text-align: center; /* Center the text */
    margin-top: 10px; /* Space above */
    font-style: italic; /* Italicize the text for emphasis */
    color: #888; /* Gray color for a subtle appearance */
}


        h1, h2, h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .campaign-details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px; /* Added gap between columns */
        }

        .left-column, .right-column {
            width: 48%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .left-column:hover, .right-column:hover {
            transform: translateY(-5px); /* Added lift effect on hover */
        }

        .slider img {
            width: 100%;
            border-radius: 8px;
        }

        .creator-info {
            background-color: #eef2f5;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .creator-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .progress-container {
            margin-top: 20px;
        }
        .donate-button {
    display: inline-block; /* Allows padding and margins to work like a button */
    margin-top: 20px;
    padding: 12px 25px;
    background-color: #3498db;
    color: white;
    text-decoration: none; /* Remove underline from link */
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.2s;
}

.donate-button:hover {
    background-color: #2980b9;
    transform: translateY(-2px); /* Slight lift effect on hover */
}


        .comment-section {
            margin-top: 30px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }

        .comment-section form {
            display: flex;
            flex-direction: column;
        }

        .comment-section textarea {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            resize: vertical;
            transition: border-color 0.3s;
        }

        .comment-section textarea:focus {
            border-color: #3498db;
            outline: none; /* Remove default outline */
        }

        .comment-section button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .comment-section button:hover {
            background-color: #0056b3;
        }

        .progress-bar {
            background-color: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
            height: 20px;
            margin-top: 10px;
        }

        .progress-fill {
            background-color: #28a745;
            height: 100%;
            transition: width 0.5s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .campaign-details-container {
                flex-direction: column;
            }
            .left-column, .right-column {
                width: 100%;
                margin-bottom: 20px;
            }
        }
  

        .donor-section, .comment-section {
            margin-top: 30px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }
        
        .donor-list, .comment-list {
            list-style-type: none;
            padding: 0;
        }

        .donor-list li, .comment-list li {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        /* Step Container Styles */
.step-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.step {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.step-item {
    flex: 2;
}

/* Status Section */
.step-status {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.progress-bar {
    width: 100px;
    height: 10px;
    border-radius: 5px;
    background-color: #ddd;
}

.progress-bar.not_completed {
    background-color: #f44336; /* Red for not completed */
}

.progress-bar.processing {
    background-color: #ffc107; /* Yellow for processing */
}

.progress-bar.completed {
    background-color: #4caf50; /* Green for completed */
}

.status-text {
    font-size: 14px;
    color: #333;
}

    </style>
</head>
<body>
    <?php include("nav.php")?>
    <div class="container">
        <div class="campaign-details-container">
            <!-- Left Column: Campaign Details -->
            <div class="left-column">
                <!-- Image Slider -->
                <div class="slider">
                    <img src="<?php echo htmlspecialchars($campaign['images']); ?>" alt="Campaign Main Image">
                    <?php foreach ($additional_images as $image_url): ?>
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Additional Image">
                    <?php endforeach; ?>
                </div>

                <h1><?php echo htmlspecialchars($campaign['title']); ?></h1>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($campaign['category']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($campaign['district']); ?></p>
                <p><strong>Goal Amount:</strong> $<?php echo number_format($campaign['goal_amount'], 2); ?></p>
                <p><strong>Donated Amount:</strong> $<?php echo number_format($campaign['donate_amount'], 2); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($campaign['duration']); ?> days</p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($campaign['description'])); ?></p>
                <p><strong>Campaign Full Story:</strong> <?php echo nl2br(htmlspecialchars($campaign['campaign_full_story'])); ?></p>


                <h2>Funding Breakdown</h2>
<div class="step-container">
    <?php foreach ($funding_breakdown as $funding): ?>
        <div class="step">
            <div class="step-item">
                <strong><?php echo htmlspecialchars($funding['item_description']); ?>:</strong>
                $<?php echo number_format($funding['amount'], 2); ?>
            </div>
            <div class="step-status">
                <div class="progress-bar <?php echo strtolower($funding['status']); ?>"></div>
                <span class="status-text"><?php echo ucfirst($funding['status']); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- Creator Info -->
<div class="creator-info">
    <img src="<?php echo htmlspecialchars($campaign['creator_profile_picture']); ?>" alt="Creator Image">
    <div>
        <h3>Creator Info</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($campaign['creator_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($campaign['creator_location']); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($campaign['creator_type']); ?></p>

        <?php
        // Determine if the logged-in user is the creator
        $creator_id = isset($campaign['normal_user_id']) ? $campaign['normal_user_id'] : $campaign['organization_id'];
        $is_creator = ($user_id == $creator_id);
        ?>

        <!-- Display Send Message button only if the logged-in user is NOT the creator -->
        <?php if (!$is_creator): ?>
            <form method="GET" action="inbox.php">
                <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
                <input type="hidden" name="creator_id" value="<?php echo $creator_id; ?>">
               
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <button type="submit" class="donate-button">Send Message</button>
            </form>
        <?php endif; ?>
    </div>
</div>


                <!-- Donor Section -->
<div class="donor-section">
    <h2>Donors</h2>
    <?php if (count($donors) > 0): ?>
        <ul class="donor-list">
            <?php foreach ($donors as $donor): ?>
                <li>
                    <strong><?php echo htmlspecialchars($donor['donor_name']); ?>:</strong> $<?php echo number_format($donor['amount'], 2); ?> 
                    <span>(<?php echo date("F j, Y", strtotime($donor['created_at'])); ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No donors yet.</p>
    <?php endif; ?>
</div>

<div class="comment-section">
    <h2>Comments</h2>
    <?php if (count($comments) > 0): ?>
        <ul class="comment-list">
            <?php foreach ($comments as $comment): ?>
                <li id="comment-<?php echo $comment['crowd_comments_id']; ?>">
                    <strong><?php echo htmlspecialchars($comment['commenter_name']); ?></strong> 
                    <span>(<?php echo htmlspecialchars($comment['user_type']); ?>)</span>
                    
                    <p class="comment-text" id="comment-text-<?php echo $comment['crowd_comments_id']; ?>">
                        <?php echo nl2br(htmlspecialchars($comment['crowd_comments'])); ?>
                    </p>
                    
                    <small>Commented on <?php echo date("F j, Y", strtotime($comment['created_at'])); ?></small>
                    
                    <?php if (!empty($comment['normal_user_id'])): ?>
                        <p>User ID: <?php echo htmlspecialchars($comment['normal_user_id']); ?></p>
                    <?php elseif (!empty($comment['organization_id'])): ?>
                        <p>Organization ID: <?php echo htmlspecialchars($comment['organization_id']); ?></p>
                    <?php endif; ?>

                    <!-- Conditional display of Edit/Delete buttons -->
                    <?php if (
                        (($comment['normal_user_id'] == $user_id && $user_type === 'user') || 
                        ($comment['organization_id'] == $user_id && $user_type === 'organization'))
                    ): ?>
                        <div class="comment-actions">
                            <button class="btn btn-edit" onclick="editComment(<?php echo $comment['crowd_comments_id']; ?>)">Edit</button>
                            <a href="#" onclick="deleteComment(<?php echo $comment['crowd_comments_id']; ?>)" class="btn btn-delete">Delete</a>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="empty-state">No comments yet. Be the first to comment!</div>
    <?php endif; ?>






 <!-- Add Comment Form -->
 <?php if (!isset($_SESSION['user_id'])): ?>
        <p>You must <a href="login.php">sign in</a> or <a href="signup.php">sign up</a> to add a comment.</p>
    <?php else: ?>
        <form method="POST" action="add_comment.php">
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
            <textarea name="comment" placeholder="Add your comment here..." required></textarea>
            <button type="submit" class="submit-comment-button">Submit Comment</button>
        </form>
    <?php endif; ?>
</div>



                
              
            </div>

            <div class="right-column">
    <!-- Funding Progress -->
    <div class="progress-container">
        <h2>Funding Progress</h2>

        <?php if ($campaign['donate_amount'] > 0): ?>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo ($campaign['donate_amount'] / $campaign['goal_amount']) * 100; ?>%;"></div>
                <div class="progress-line"></div> <!-- Line at the top of the progress fill -->
            </div>
            <p>Total Raised: $<?php echo number_format($campaign['donate_amount'], 2); ?></p>
            <p>Goal Amount: $<?php echo number_format($campaign['goal_amount'], 2); ?></p>
            <p>Number of Donors: <?php echo count($donors); ?></p>
            <p>Percentage of Goal: <?php echo round(($campaign['donate_amount'] / $campaign['goal_amount']) * 100, 2); ?>%</p>
        <?php else: ?>
            <div class="empty-state">
                <p>No donations have been made yet. Be the first to contribute!</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="donate-container">
        <form method="POST" action="donate.php">
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
            <a href="donate.php?campaign_id=<?php echo $campaign_id; ?>" class="donate-button">Donate Now</a>
        </form>
    </div>

    <div class="share-section">
        <h3>Share This Campaign</h3>
        <button class="share-button">Share on Facebook</button>
        <button class="share-button">Share on Twitter</button>
        <button class="share-button">Share via Email</button>
    </div>
</div>


<script>
    function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        $.ajax({
            type: 'POST',
            url: 'delete_comment.php', // URL to your delete script
            data: { comment_id: commentId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the comment from the UI or reload the comments
                    alert(response.message);
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert(response.message); // Show error message
                }
            },
            error: function() {
                alert('Error: Unable to process the request.');
            }
        });
    }
}

function editComment(commentId) {
    var commentText = document.getElementById('comment-text-' + commentId);
    var originalText = commentText.innerHTML;
    
    // Create a text area for editing
    var textarea = document.createElement('textarea');
    textarea.value = originalText.replace(/<br\s*\/?>/gi, "\n"); // Convert <br> to newline
    commentText.innerHTML = '';
    commentText.appendChild(textarea);
    
    // Create a save button
    var saveButton = document.createElement('button');
    saveButton.innerHTML = 'Save';
    saveButton.onclick = function() {
        var updatedComment = textarea.value;
        
        // Send AJAX request to update the comment
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_comment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Update the comment text with the new text
                commentText.innerHTML = nl2br(updatedComment);
            } else {
                alert('Error updating comment. Please try again.');
            }
        };
        
        xhr.send('comment_id=' + commentId + '&crowd_comments=' + encodeURIComponent(updatedComment));
    };
    
    commentText.appendChild(saveButton);
}

// Function to convert new lines to <br> for display
function nl2br(str) {
    return str.replace(/(\r\n|\n|\r)/g, '<br>');
}
</script>

    <script>

        $(document).ready(function(){
            $('.slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear'
            });
        });

        $(document).ready(function() {
    $('#comment-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            url: 'add_comment.php', // URL to the PHP script that handles adding comments
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                // Assuming response contains the newly added comment data
                const newComment = JSON.parse(response);
                const commentHtml = `
                    <li>
                        <strong>${newComment.commenter_name}:</strong>
                        <p>${newComment.crowd_comments}</p>
                        <span>(${newComment.created_at})</span>
                    </li>
                `;
                $('#comment-list').append(commentHtml); // Append the new comment to the list
                $('textarea[name="comment"]').val(''); // Clear the textarea
            },
            error: function() {
                alert('Error adding comment. Please try again.');
            }
        });
    });
});
        
        
    </script>
</body>
</html>

