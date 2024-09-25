<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL queries to get Louisians and Out-of-School visitor data per month
$louisiansQuery = "SELECT MONTH(time) as month, COUNT(*) as count FROM user_log WHERE user_school = 'Louisian' GROUP BY MONTH(time)";
$outOfSchoolQuery = "SELECT MONTH(time) as month, COUNT(*) as count FROM user_log WHERE user_school = 'Out-of-School' GROUP BY MONTH(time)";

// Fetch data
$louisiansResult = $conn->query($louisiansQuery);
$outOfSchoolResult = $conn->query($outOfSchoolQuery);

// Initialize data arrays
$louisiansData = array_fill(0, 12, 0); // Data for each month (Jan to Dec)
$outOfSchoolData = array_fill(0, 12, 0); // Data for each month (Jan to Dec)

// Fill Louisians data
if ($louisiansResult->num_rows > 0) {
    while ($row = $louisiansResult->fetch_assoc()) {
        $louisiansData[$row['month'] - 1] = $row['count']; // Store in respective month index (Jan is 0)
    }
}

// Fill Out-of-School data
if ($outOfSchoolResult->num_rows > 0) {
    while ($row = $outOfSchoolResult->fetch_assoc()) {
        $outOfSchoolData[$row['month'] - 1] = $row['count'];
    }
}

// Return data as JSON
echo json_encode([
    'louisians' => $louisiansData,
    'out_of_school' => $outOfSchoolData
]);

$conn->close();
?>
