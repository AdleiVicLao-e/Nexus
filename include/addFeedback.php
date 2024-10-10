<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = ucfirst($_POST['rating']);
    $message = $_POST['message'];

    $sql = "SELECT MAX(feedbackId) AS maxId FROM feedback";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $newFeedbackId = is_null($row['maxId']) ? 1 : $row['maxId'] + 1;

    $sql = "INSERT INTO feedback (feedbackId, rating, message) VALUES ('$newFeedbackId', '$rating', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='../feedback.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>