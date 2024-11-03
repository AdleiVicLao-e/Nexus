<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$sectionId = $_POST['id'];

$stmtDelete = $mysqli->prepare("DELETE FROM section WHERE section_id = ?");
$stmtDelete->bind_param("i", $sectionId);
// Execute the delete statement and check for success/failure
if ($stmtDelete->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Section ID: ' . $sectionId . ' has been deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting record from database: ' . $stmtDelete->error
    ]);
}

$stmtDelete->close();
$mysqli->close();