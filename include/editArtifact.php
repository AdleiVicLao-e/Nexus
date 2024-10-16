<?php

include 'artifact-db.php';
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (isset($data['id'], $data['name'], $data['section_id'], $data['description'])) {
    $id = $data['id'];
    $name = $data['name'];
    $sectionId = $data['section_id'];
    $catalogId = isset($data['catalog_id']) && is_numeric($data['catalog_id']) ? $data['catalog_id'] : null; // Handle null case
    $subcatalogId = isset($data['subcatalog_id']) && is_numeric($data['subcatalog_id']) ? $data['subcatalog_id'] : null; // Handle null case
    $description = $data['description'];

    // Prepare and execute the update query
    $query = "
        UPDATE artifact_info
        SET name = ?, section_id = ?, catalogue_id = ?, subcat_id = ?, description = ?
        WHERE artifact_id = ?
    ";

    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param('siissi', $name, $sectionId, $catalogId, $subcatalogId, $description, $id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Artifact updated successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
        }

        // Close the statement
        $stmt->close();
    } else {
        $response = ['success' => false, 'message' => 'Failed to prepare statement: ' . $mysqli->error];
    }

    // Close the connection
    $mysqli->close();
} else {
    $response = ['success' => false, 'message' => 'Invalid input. Ensure all required fields are provided.'];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
