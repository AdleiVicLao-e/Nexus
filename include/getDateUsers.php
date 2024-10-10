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

// Check if a date range is submitted
if (isset($_POST['datefilter']) && !empty($_POST['datefilter'])) {
    // Split the date range
    $dateRange = explode(' - ', $_POST['datefilter']);
    $startDate = date('Y-m-d', strtotime($dateRange[0])); // Convert to Y-m-d format
    $endDate = date('Y-m-d', strtotime($dateRange[1]));   // Convert to Y-m-d format

    // Modify the SQL query to filter between the selected dates
    $sql = "SELECT user_number, user_name, user_school, time 
            FROM user_log 
            WHERE DATE(time) BETWEEN '$startDate' AND '$endDate'";
} else {
    // Default SQL query (no filter)
    $sql = "SELECT user_number, user_name, user_school, time FROM user_log";
}

$result = $conn->query($sql);

$rows = [];

if ($result->num_rows > 0) {
    // Fetch all the results into an array
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    // Return the results as JSON for JavaScript to process
    echo json_encode($rows);
} else {
    echo json_encode([]); // Return an empty array if no records found
}

$conn->close();
?>
