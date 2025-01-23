<?php
include 'artifact-db.php';

// Retrieve 'id' and 'newName' from the POST data
$catalogId = $_POST['id'];
$newCatalogName = $_POST['newName'];

$newCatalogName = htmlspecialchars(trim($newCatalogName), ENT_QUOTES, 'UTF-8');

// Check if the new catalog name already exists in the database (excluding the current catalog)
$query = "SELECT * FROM catalogue WHERE catalogue_name = ? AND catalogue_id != ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $newCatalogName, $catalogId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If a catalog with the same name already exists, return error
    echo json_encode([
        'success' => false,
        'message' => 'Catalog name already exists. Please input a different catalog name.'
    ]);
} else {
    // Update the catalog name if it's unique
    $stmt = $mysqli->prepare("UPDATE catalogue SET catalogue_name = ? WHERE catalogue_id = ?");
    $stmt->bind_param("si", $newCatalogName, $catalogId);

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