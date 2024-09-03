<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_name = $_POST['user_name'];
$user_school = $_POST['user_school'];
$time = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO user_log (user_name, user_school, time) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user_name, $user_school, $time);

if ($stmt->execute()) {
    echo "Record inserted successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>