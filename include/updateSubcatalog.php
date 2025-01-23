<?php
include 'artifact-db.php';

// Retrieve 'id' and 'newName' from the POST data
$subcatalogId = $_POST['id'];
$newSubcatalogName = $_POST['newName'];

$newSubcatalogName = htmlspecialchars(trim($newSubcatalogName), ENT_QUOTES, 'UTF-8');

// Check if the new subcatalog name already exists in the database (excluding the current subcatalog)
$query = "SELECT * FROM subcatalogue WHERE subcat_name = ? AND subcat_id != ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $newSubcatalogName, $subcatalogId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If a subcatalog with the same name already exists, return error
    echo json_encode([
        'success' => false,
        'message' => 'Sub catalog name already exists. Please input a different sub catalog name.'
    ]);
} else {
    // Update the subcatalog name if it's unique
    $stmt = $mysqli->prepare("UPDATE subcatalogue SET subcat_name = ? WHERE subcat_id = ?");
    $stmt->bind_param("si", $newSubcatalogName, $subcatalogId);

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