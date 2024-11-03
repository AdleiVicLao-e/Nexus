<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$catalogueId = $_POST['id'];

$stmtDelete = $mysqli->prepare("DELETE FROM catalogue WHERE catalogue_id = ?");
$stmtDelete->bind_param("i", $catalogueId);
// Execute the delete statement and check for success/failure
if ($stmtDelete->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Section ID: ' . $catalogueId . ' has been deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting record from database: ' . $stmtDelete->error
    ]);
}

$stmtDelete->close();
$mysqli->close();