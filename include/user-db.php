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

$sql = "SELECT MAX(user_number) AS last_number FROM user_log";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_user_number = ($row['last_number'] === NULL) ? 1 : $row['last_number'] + 1;
} else {
    $new_user_number = 1;
}

$stmt = $conn->prepare("INSERT INTO user_log (user_number, user_name, user_school, time) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $new_user_number, $user_name, $user_school, $time);

$stmt->close();
$conn->close();
?>
