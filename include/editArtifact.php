<?php
global $mysqli;
include 'artifact-db.php'; // Include database connection


header('Content-Type: application/json');


try {
    // Decode input JSON
    $input = json_decode(file_get_contents('php://input'), true);


    // Validate input
    if (!isset($input['id'], $input['name'], $input['section_name'], $input['description'])) {
        throw new Exception('Invalid input. Ensure all required fields are provided.');
    }


    $id = $input['id'];
    $name = $input['name'];
    $sectionName = $input['section_name']; // Updated key
    $catalogName = isset($input['catalog_name']) ? $input['catalog_name'] : null; // Updated key
    $subcatalogName = isset($input['subcatalog_name']) ? $input['subcatalog_name'] : null; // Updated key
    $description = $input['description'];




    $sectionId = null;
    $catalogId = null;
    $subcatalogId = null;


    // Fetch section ID
    $stmt = $mysqli->prepare("SELECT section_id FROM section WHERE section_name = ?");
    $stmt->bind_param('s', $sectionName);
    $stmt->execute();
    $stmt->bind_result($sectionId);
    $stmt->fetch();
    $stmt->close();


    if (!$sectionId) {
        throw new Exception('Section not found.');
    }


    // Fetch catalog ID if catalogName is provided
    if ($catalogName) {
        $stmt = $mysqli->prepare("SELECT catalogue_id FROM catalogue WHERE catalogue_name = ?");
        $stmt->bind_param('s', $catalogName);
        $stmt->execute();
        $stmt->bind_result($catalogId);
        $stmt->fetch();
        $stmt->close();


        if (!$catalogId) {
            throw new Exception('Catalog not found.');
        }
    }


    // Fetch subcatalog ID if subcatalogName is provided
    if ($subcatalogName) {
        $stmt = $mysqli->prepare("SELECT subcat_id FROM subcatalogue WHERE subcat_name = ?");
        $stmt->bind_param('s', $subcatalogName);
        $stmt->execute();
        $stmt->bind_result($subcatalogId);
        $stmt->fetch();
        $stmt->close();


        if (!$subcatalogId) {
            throw new Exception('Subcatalog not found.');
        }
    }


    // Update the artifact with the retrieved IDs
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
            throw new Exception('Database error: ' . $stmt->error);
        }


        $stmt->close();
    } else {
        throw new Exception('Failed to prepare statement: ' . $mysqli->error);
    }


} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}


// Return the response as JSON
echo json_encode($response);


$mysqli->close();
?>


