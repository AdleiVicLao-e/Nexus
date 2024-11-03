<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$subcatalogueId = $_POST['id'];

$stmtDelete = $mysqli->prepare("DELETE FROM subcatalogue WHERE subcat_id = ?");
$stmtDelete->bind_param("i", $subcatalogueId);
// Execute the delete statement and check for success/failure
if ($stmtDelete->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Section ID: ' . $subcatalogueId . ' has been deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting record from database: ' . $stmtDelete->error
    ]);
}

$stmtDelete->close();
$mysqli->close();