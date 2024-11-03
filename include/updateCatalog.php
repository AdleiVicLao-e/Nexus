<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$catalogId = $_POST['id'];
$newCatalogName = $_POST['newName'];

$stmt = $mysqli->prepare("UPDATE catalogue SET catalogue_name = ? WHERE catalogue_id = ?");
$stmt->bind_param("si", $newCatalogName, $catalogId);
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