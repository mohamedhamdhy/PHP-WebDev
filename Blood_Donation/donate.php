<?php
session_start();
include 'db.php'; // Ensure your database connection is included

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    // Redirect to signup page if not logged in
    header("Location: signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Get campaign_id from the URL
if (isset($_GET['campaign_id'])) {
    $campaign_id = intval($_GET['campaign_id']); // Cast to int for safety
} else {
    echo json_encode(['error' => 'Campaign ID is required.']);
    exit();
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get amount from POST data and validate
    if (isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
        $amount = floatval($_POST['amount']); // Cast to float for safety
    } else {
        echo json_encode(['error' => 'Invalid donation amount.']);
        exit();
    }

    // Get bank details from POST data
    $bank_name = htmlspecialchars(trim($_POST['bank_name']));
    $account_number = htmlspecialchars(trim($_POST['account_number']));

    // Validate bank details
    if (empty($bank_name) || empty($account_number)) {
        echo json_encode(['error' => 'Bank details are required.']);
        exit();
    }

    // Determine which ID to use based on user type
    if ($user_type === 'user') {
        $normal_user_id = $user_id; // Set normal user ID
        $organization_id = NULL; // Set organization ID to NULL
    } elseif ($user_type === 'organization') {
        $normal_user_id = NULL; // Set normal user ID to NULL
        $organization_id = $user_id; // Set organization ID
    } else {
        echo json_encode(['error' => 'Invalid user type.']);
        exit();
    }

    // Insert the donation into the donations table
    try {
        // Prepare and execute the insert query
        $insert_donation_query = $conn->prepare("INSERT INTO donations (campaign_id, normal_user_id, organization_id, amount, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insert_donation_query->bind_param("iiid", $campaign_id, $normal_user_id, $organization_id, $amount);
        $insert_donation_query->execute();

        // Update the donate_amount in the campaigns table
        $update_campaign_query = $conn->prepare("UPDATE campaigns SET donate_amount = donate_amount + ? WHERE campaign_id = ?");
        $update_campaign_query->bind_param("di", $amount, $campaign_id);
        $update_campaign_query->execute();

        // If everything is successful, redirect to the campaign details page
        header("Location: campaign_details.php?id=" . $campaign_id);
        exit();
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to insert donation: ' . $e->getMessage()]);
        exit();
    }
} else {
    // Show donation form
    ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .donation-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button.donate-button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button.donate-button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>

    <div class="donation-form">
        <h2>Donate to Campaign</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="bank_name">Bank Name:</label>
            <input type="text" name="bank_name" placeholder="Enter your bank name" required>

            <label for="account_number">Account Number:</label>
            <input type="text" name="account_number" placeholder="Enter your account number" required>

            <label for="amount">Donation Amount:</label>
            <input type="number" name="amount" placeholder="Enter donation amount" required min="0.01" step="0.01">

            <button type="submit" class="donate-button">Submit Donation</button>
        </form>
    </div>
    <?php
}
?>
