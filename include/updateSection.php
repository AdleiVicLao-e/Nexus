<?php
include 'artifact-db.php';

// Retrieve 'id' and 'newName' from the POST data
$sectionId = $_POST['id'];
$newSectionName = $_POST['newName'];

$newSectionName = htmlspecialchars(trim($newSectionName), ENT_QUOTES, 'UTF-8');

// Check if the new section name already exists in the database (excluding the current section)
$query = "SELECT * FROM section WHERE section_name = ? AND section_id != ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $newSectionName, $sectionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If a section with the same name already exists, return error
    echo json_encode([
        'success' => false,
        'message' => 'Section name already exists. Please input a different section name.'
    ]);
} else {
    // Update the section name if it's unique
    $stmt = $mysqli->prepare("UPDATE section SET section_name = ? WHERE section_id = ?");
    $stmt->bind_param("si", $newSectionName, $sectionId);

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
}

$stmt->close();
$mysqli->close();
?>