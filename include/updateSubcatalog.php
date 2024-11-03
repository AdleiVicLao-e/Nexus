<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$subcatalogId = $_POST['id'];
$newSubcatalogName = $_POST['newName'];

$stmt = $mysqli->prepare("UPDATE subcatalogue SET subcat_name = ? WHERE subcat_id = ?");
$stmt->bind_param("si", $newSubcatalogName, $subcatalogId);
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