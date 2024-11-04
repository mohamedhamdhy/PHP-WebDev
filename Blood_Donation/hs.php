<?php
// Database connection
$servername = "localhost"; // Change this if your DB is hosted somewhere else
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "final"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donor details
$query = "SELECT * FROM user_details";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .donor-card {
            display: flex;
            border: 1px solid #ddd;
            margin: 10px 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .donor-image {
            width: 30%;
            padding: 20px;
            text-align: center;
        }

        .donor-image img {
            width: 80%;
            border-radius: 50%;
        }

        .donor-info {
            padding: 20px;
            flex: 1;
        }

        .donor-info h2 {
            color: #333;
            margin: 0 0 10px 0;
        }

        .donor-info p {
            margin: 5px 0;
        }

        .request-button {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .request-button:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Blood Donor List</h1>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="donor-card">';
                echo '<div class="donor-image"><img src="path_to_image" alt="Donor Image"></div>'; // Update image path
                echo '<div class="donor-info">';
                echo '<h2>' . htmlspecialchars($row['fullName']) . '</h2>';
                echo '<p><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</p>';
                echo '<p><strong>Blood Group:</strong> ' . htmlspecialchars($row['blood_group']) . '</p>';
                echo '<p><strong>Mobile No:</strong> ' . htmlspecialchars($row['mobile1']) . '</p>';
                echo '<p><strong>Email ID:</strong> ' . htmlspecialchars($row['email']) . '</p>';
                echo '<p><strong>Date of Birth:</strong> ' . htmlspecialchars($row['day']) . '.' . htmlspecialchars($row['month']) . '.' . htmlspecialchars($row['year']) . '</p>';
                echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>';
               
                echo '<button class="request-button">Request</button>';
                echo '</div></div>';
            }
        } else {
            echo "<p>No donors found.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
