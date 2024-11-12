<?php
include 'user-db.php';

// Allowed schools list
$allowed_schools = [
    "Basic Education School",
    "School of Accountancy, Management, Computing and Information Studies",
    "School of Advanced Studies",
    "School of Engineering and Architecture",
    "School of Law",
    "School of Medicine",
    "School of Nursing, Allied Health, and Biological Sciences",
    "School of Teacher Education and Liberal Arts"
];

// SQL query to fetch data from user_log table
$sql = "SELECT user_school, COUNT(user_number) as count FROM user_log GROUP BY user_school";
$result = $conn->query($sql);

// Prepare data for the pie chart
$schools = [];
$counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $school = in_array($row['user_school'], $allowed_schools) ? $row['user_school'] : "Others";

        // Check if the "Others" category already exists in the array
        if (in_array($school, $schools)) {
            // Increment the count of "Others" if it already exists
            $index = array_search($school, $schools);
            $counts[$index] += (int)$row['count'];
        } else {
            // Add new school entry if it doesn't exist
            $schools[] = $school;
            $counts[] = (int)$row['count'];
        }
    }
} else {
    echo json_encode(["error" => "No records found."]);
    exit;
}

// Prepare data for JSON output
$data = [
    'schools' => $schools,
    'counts' => $counts
];

$conn->close();

// Output JSON data
header('Content-Type: application/json');
echo json_encode($data);
?>
