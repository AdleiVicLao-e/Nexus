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

// SQL query to fetch data from user_log table
$sql = "SELECT user_school, COUNT(user_number) as count FROM user_log GROUP BY user_school";
$result = $conn->query($sql);

// Prepare data for the pie chart
$schools = [];
$counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row['user_school'];
        $counts[] = (int)$row['count']; // Cast to int for proper JSON output
    }
} else {
    echo json_encode(["error" => "No records found."]);
    exit;
}

$conn->close();

// Prepare data for JSON output
$data = [
    'schools' => $schools,
    'counts' => $counts
];

// Output JSON data
header('Content-Type: application/json');
echo json_encode($data);
?>
