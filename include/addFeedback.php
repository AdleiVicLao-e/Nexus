<?php
// Database connection
include 'user-db.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $result = $conn->query("SELECT MAX(feedbackId) AS last_id FROM feedback");
    $lastId = $result->fetch_assoc()['last_id'];

    $feedbackId = $lastId ? $lastId + 1 : 1;

    $stmt = $conn->prepare("INSERT INTO feedback (feedbackId, date, quality_presentation, cleanliness_ambiance, staff_service, overall_experience, comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $feedbackId, $date, $quality_presentation, $cleanliness_ambiance, $staff_service, $overall_experience, $comments);

    $date = $_POST['date'];
    $quality_presentation = $_POST['exhibits'];
    $cleanliness_ambiance = $_POST['cleanliness'];
    $staff_service = $_POST['staff'];
    $overall_experience = $_POST['experience'];
    $comments = htmlspecialchars($_POST['comments']);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
