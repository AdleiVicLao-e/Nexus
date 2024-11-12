<?php

include 'user-db.php';

date_default_timezone_set("Asia/Manila");

$user_name = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
$user_school = htmlspecialchars($_POST['user_school'], ENT_QUOTES, 'UTF-8');
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
    echo json_encode(['success' => true, 'message' => 'Welcome!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: Unable to log your activity. Please try again.' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>