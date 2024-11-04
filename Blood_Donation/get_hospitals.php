<?php
// Database connection
include 'db.php';

// Check if the district is set
if (isset($_POST['district'])) {
    $district = $_POST['district'];

    // Prepare and execute the query to fetch hospitals based on the district
    $stmt = $conn->prepare("SELECT hospital_id, hospital_name FROM hospitals WHERE hospital_district = ?");
    $stmt->bind_param("s", $district);
    $stmt->execute();
    $result = $stmt->get_result();

    $hospitals = [];

    // Fetch the hospital data and store it in an array
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }

    // Return the hospital data as JSON
    echo json_encode($hospitals);
}
?>
