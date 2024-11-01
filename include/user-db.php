<?php

date_default_timezone_set("Asia/Manila");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$user_name = $_POST['user_name'];
$user_school = $_POST['user_school'];
$time = date("Y-m-d H:i:s");

// Get the last user number
$sql = "SELECT MAX(user_number) AS last_number FROM user_log";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_user_number = ($row['last_number'] === NULL) ? 1 : $row['last_number'] + 1;
} else {
    $new_user_number = 1;
}

// Prepare and bind the statement
$stmt = $conn->prepare("INSERT INTO user_log (user_number, user_name, user_school, time) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $new_user_number, $user_name, $user_school, $time);

// Execute the statement and return success/failure response
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Record inserted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting record: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
