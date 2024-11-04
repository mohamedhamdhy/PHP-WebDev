<?php
session_start();
include 'db.php'; // Ensure your database connection is included

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: signup.php");
    exit();
}

// Get campaign_id from the URL
if (isset($_GET['campaign_id'])) {
    $campaign_id = intval($_GET['campaign_id']); // Cast to int for safety
} else {
    echo "Campaign ID is required.";
    exit();
}

// Fetch campaign details
$stmt = $conn->prepare("SELECT * FROM campaigns WHERE campaign_id = ?");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$campaign_result = $stmt->get_result();
$campaign = $campaign_result->fetch_assoc();

if (!$campaign) {
    echo "Campaign not found.";
    exit();
}

// Fetch donation statistics
$donation_stmt = $conn->prepare("SELECT SUM(amount) AS total_donations, COUNT(*) AS donor_count FROM donations WHERE campaign_id = ?");
$donation_stmt->bind_param("i", $campaign_id);
$donation_stmt->execute();
$donation_result = $donation_stmt->get_result();
$donation_stats = $donation_result->fetch_assoc();

$total_donations = $donation_stats['total_donations'] ?? 0;
$donor_count = $donation_stats['donor_count'] ?? 0;

// Fetch top donors (both normal users and organizations)
$top_donors_stmt = $conn->prepare("
    SELECT 
        CASE 
            WHEN d.normal_user_id IS NOT NULL THEN 'normal_user' 
            ELSE 'organization' 
        END AS donor_type,
        COALESCE(d.normal_user_id, d.organization_id) AS donor_id,
        SUM(d.amount) AS total_amount
    FROM donations d
    LEFT JOIN normal_user nu ON d.normal_user_id = nu.normal_user_id
    LEFT JOIN organization o ON d.organization_id = o.organization_id
    WHERE d.campaign_id = ?
    GROUP BY donor_type, donor_id
    ORDER BY total_amount DESC
    LIMIT 5
");
$top_donors_stmt->bind_param("i", $campaign_id);
$top_donors_stmt->execute();
$top_donors_result = $top_donors_stmt->get_result();

// Fetch donation timeline
$timeline_stmt = $conn->prepare("SELECT DATE(created_at) AS donation_date, SUM(amount) AS daily_total FROM donations WHERE campaign_id = ? GROUP BY donation_date ORDER BY donation_date ASC");
$timeline_stmt->bind_param("i", $campaign_id);
$timeline_stmt->execute();
$timeline_result = $timeline_stmt->get_result();

// Prepare data for the graph
$donation_dates = [];
$daily_totals = [];

while ($row = $timeline_result->fetch_assoc()) {
    $donation_dates[] = $row['donation_date'];
    $daily_totals[] = (float)$row['daily_total'];
}

// Include TCPDF library
require_once('TCPDF-main/TCPDF-main/tcpdf.php'); // Adjust the path as needed

// Function to generate PDF
function generatePDF($top_donors_result, $campaign_title) {
    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "Donor List for Campaign: " . $campaign_title, 0, 1, 'C');

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(40, 10, 'Donor Type', 1);
    $pdf->Cell(100, 10, 'Donor Name', 1);
    $pdf->Cell(50, 10, 'Total Amount', 1);
    $pdf->Ln();

    while ($donor = $top_donors_result->fetch_assoc()) {
        $donor_id = $donor['donor_id'];
        if ($donor['donor_type'] === 'normal_user') {
            // Fetch donor details for normal user
            $donor_details_stmt = $GLOBALS['conn']->prepare("SELECT normal_user_firstname, normal_user_lastname FROM normal_user WHERE normal_user_id = ?");
            $donor_details_stmt->bind_param("i", $donor_id);
        } else {
            // Fetch donor details for organization
            $donor_details_stmt = $GLOBALS['conn']->prepare("SELECT organization_name FROM organization WHERE organization_id = ?");
            $donor_details_stmt->bind_param("i", $donor_id);
        }
        $donor_details_stmt->execute();
        $donor_details = $donor_details_stmt->get_result()->fetch_assoc();

        $pdf->Cell(40, 10, ucfirst($donor['donor_type']), 1);
        if ($donor['donor_type'] === 'normal_user') {
            $pdf->Cell(100, 10, htmlspecialchars($donor_details['normal_user_firstname'] . ' ' . $donor_details['normal_user_lastname']), 1);
        } else {
            $pdf->Cell(100, 10, htmlspecialchars($donor_details['organization_name']), 1);
        }
        $pdf->Cell(50, 10, '$' . number_format($donor['total_amount'], 2), 1);
        $pdf->Ln();
    }

    // Output PDF document
    $pdf->Output('donor_list.pdf', 'D'); // 'D' forces download
}

// Function to generate CSV
function generateCSV($top_donors_result) {
    // Set headers to force download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donor_list.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Donor Type', 'Donor Name', 'Total Amount']); // Header row

    while ($donor = $top_donors_result->fetch_assoc()) {
        $donor_id = $donor['donor_id'];
        if ($donor['donor_type'] === 'normal_user') {
            // Fetch donor details for normal user
            $donor_details_stmt = $GLOBALS['conn']->prepare("SELECT normal_user_firstname, normal_user_lastname FROM normal_user WHERE normal_user_id = ?");
            $donor_details_stmt->bind_param("i", $donor_id);
        } else {
            // Fetch donor details for organization
            $donor_details_stmt = $GLOBALS['conn']->prepare("SELECT organization_name FROM organization WHERE organization_id = ?");
            $donor_details_stmt->bind_param("i", $donor_id);
        }
        $donor_details_stmt->execute();
        $donor_details = $donor_details_stmt->get_result()->fetch_assoc();

        $donor_name = ($donor['donor_type'] === 'normal_user') ? $donor_details['normal_user_firstname'] . ' ' . $donor_details['normal_user_lastname'] : $donor_details['organization_name'];
        
        // Adding dollar symbol and formatting the amount
        $total_amount = '"' . '$' . number_format($donor['total_amount'], 2) . '"';
        
        fputcsv($output, [ucfirst($donor['donor_type']), $donor_name, $total_amount]);
    }
    fclose($output);
}



if (isset($_POST['download_pdf'])) {
    generatePDF($top_donors_result, $campaign['title']);
    exit();
}

if (isset($_POST['download_csv'])) {
    generateCSV($top_donors_result);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Analysis</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
        }

        .donor-list {
            list-style: none;
            padding: 0;
        }

        .donor-list li {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        canvas {
            max-width: 100%;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }

    </style>
</head>


<body>



    <div class="container">
        <h1>Campaign Analysis: <?php echo htmlspecialchars($campaign['title']); ?></h1>
        
        <div class="section">
            <h2>Campaign Overview</h2>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($campaign['campaign_status']); ?></p>
            <p><strong>Total Donations:</strong> $<?php echo number_format($total_donations, 2); ?></p>
            <p><strong>Donor Count:</strong> <?php echo $donor_count; ?></p>
        </div>

        <div class="section">
            <h2>Top Donors</h2>
            <ul class="donor-list">
                <?php while ($donor = $top_donors_result->fetch_assoc()): ?>
                    <?php
                    $donor_id = $donor['donor_id'];
                    if ($donor['donor_type'] === 'normal_user') {
                        // Fetch donor details for normal user
                        $donor_details_stmt = $conn->prepare("SELECT normal_user_firstname, normal_user_lastname, normal_user_profile_picture FROM normal_user WHERE normal_user_id = ?");
                        $donor_details_stmt->bind_param("i", $donor_id);
                    } else {
                        // Fetch donor details for organization
                        $donor_details_stmt = $conn->prepare("SELECT organization_name, organization_profile_picture FROM organization WHERE organization_id = ?");
                        $donor_details_stmt->bind_param("i", $donor_id);
                    }
                    $donor_details_stmt->execute();
                    $donor_details = $donor_details_stmt->get_result()->fetch_assoc();
                    ?>
                    <li>
                        <img src="<?php echo $donor['donor_type'] === 'normal_user' ? $donor_details['normal_user_profile_picture'] : $donor_details['organization_profile_picture']; ?>" alt="Donor" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                        <div>
                            <strong><?php echo htmlspecialchars($donor['donor_type'] === 'normal_user' ? $donor_details['normal_user_firstname'] . ' ' . $donor_details['normal_user_lastname'] : $donor_details['organization_name']); ?></strong>
                            <p>Total Donated: $<?php echo number_format($donor['total_amount'], 2); ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <form method="post" action="">
            <button type="submit" name="download_pdf" class="btn">Download PDF of Donors</button>
            <button type="submit" name="download_csv" class="btn">Download CSV of Donors</button>
        </form>

        <div class="section">
            <h2>Donation Trend Over Time</h2>
            <canvas id="donationTrendChart"></canvas>
        </div>

        

        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> Your Charity. All rights reserved.</p>
        </div>
    </div>

    <script>
        const donationDates = <?php echo json_encode($donation_dates); ?>;
        const dailyTotals = <?php echo json_encode($daily_totals); ?>;

        const ctx = document.getElementById('donationTrendChart').getContext('2d');
        const donationTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: donationDates,
                datasets: [{
                    label: 'Daily Total Donations ($)',
                    data: dailyTotals,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
