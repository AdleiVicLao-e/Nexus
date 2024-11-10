<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$sectionId = $_POST['id'];
$newSectionName = $_POST['newName'];

$newSectionName = htmlspecialchars(trim($newSectionName), ENT_QUOTES, 'UTF-8');\

$stmt = $mysqli->prepare("UPDATE section SET section_name = ? WHERE section_id = ?");
$stmt->bind_param("si", $newSectionName, $sectionId);
// Execute the delete statement and check for success/failure
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating record from database: ' . $stmt->error
    ]);
}

$stmt->close();
$mysqli->close();