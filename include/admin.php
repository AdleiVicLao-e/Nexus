<?php
require("db.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Login Success";
    } else {
        echo "Login Not Successful. Please check your credentials.1";
    }
    $stmt->close();
    $conn->close();
}
?>