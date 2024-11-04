<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "final";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hospital data
$sql = "SELECT * FROM hospitals";
$result = $conn->query($sql);

$hospitals = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Calculate total blood availability
        $totalBlood = $row['A_plus'] + $row['B_plus'] + $row['O_plus'] + $row['AB_plus'] +
                      $row['A_minus'] + $row['B_minus'] + $row['O_minus'] + $row['AB_minus'];

        $hospitals[$row['name']] = [
            'details' => [
                'address' => $row['address'],
                'phone' => $row['phone'],
                'email' => $row['email']
            ],
            'blood' => [
                'A+' => $row['A_plus'],
                'B+' => $row['B_plus'],
                'O+' => $row['O_plus'],
                'AB+' => $row['AB_plus'],
                'A-' => $row['A_minus'],
                'B-' => $row['B_minus'],
                'O-' => $row['O_minus'],
                'AB-' => $row['AB_minus']
            ],
            'total' => $totalBlood // Add total blood availability
        ];
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
            color: #333;
        }

        .sidebar {
            background: #d9534f;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            width: 250px;
            z-index: 1;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar li {
            margin: 10px 0;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #c9302c;
        }

        .header {
            padding: 20px;
            background-color: #5bc0de;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: 250px; /* Adjust for sidebar width */
        }

        .main-content {
            margin-left: 250px; /* Adjust for sidebar width */
            padding: 20px;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            flex: 1 0 150px; /* Flex-grow, flex-shrink, flex-basis */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .contact-details {
            margin-top: 20px;
            background-color: #f2f2f2;
            padding: 15px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .header, .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <ul id="hospital-list">
            <?php foreach ($hospitals as $name => $details): ?>
                <li><a href="#" onclick="showAvailability('<?php echo $name; ?>')"><?php echo $name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Header -->
    <header class="header">
        <h1>Blood Bank Management System</h1>
        <button class="logout btn btn-light"><span>Logout</span> <i class="fas fa-sign-out-alt"></i></button>
    </header>

    <!-- Main Content -->
    <main class="main-content" id="content">
        <h2>Select a hospital to view blood availability</h2>
    </main>

    <script>
        const bloodData = <?php echo json_encode($hospitals); ?>; // Initialize blood data from PHP

        // Show blood availability for selected hospital
        function showAvailability(hospitalName) {
            const availability = bloodData[hospitalName];
            let content = `<h2>${hospitalName} Blood Availability</h2><div class="cards">`;
            
            // Display the total blood availability
            content += `
                <div class="card" style="flex: 1 0 100%;">
                    <h3>Total Blood Available</h3>
                    <p>${availability.total}</p>
                </div>
            `;

            for (const [bloodType, quantity] of Object.entries(availability.blood)) {
                content += `
                <div class="card">
                    <i class="fas fa-tint"></i>
                    <h3>${bloodType}</h3>
                    <p>Available: ${quantity}</p>
                </div>
                `;
            }

            // Display hospital contact details
            content += `
            <div class="contact-details">
                <h3>Contact Details</h3>
                <p><strong>Address:</strong> ${availability.details.address}</p>
                <p><strong>Phone:</strong> ${availability.details.phone}</p>
                <p><strong>Email:</strong> ${availability.details.email}</p>
            </div>`;

            content += `</div>`;
            document.getElementById('content').innerHTML = content;
        }
    </script>
</body>
</html>
