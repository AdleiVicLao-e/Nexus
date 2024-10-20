<?php
include 'artifact-db.php'; // Include database connection

header('Content-Type: application/json');

try {
    // Decode input JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for the required fields in the input
    if (empty($input['sectionName']) && empty($input['catalogName']) && empty($input['subcatalogName'])) {
        throw new Exception('At least one name must be provided.');
    }

    $response = [];

    // Fetch section ID if section name is provided
    if (!empty($input['sectionName'])) {
        $stmt = $mysqli->prepare("SELECT section_id FROM section WHERE section_name = ?");
        $stmt->bind_param('s', $input['sectionName']);
        $stmt->execute();
        $stmt->bind_result($sectionId);
        if ($stmt->fetch()) {
            $response['sectionId'] = $sectionId;
        } else {
            $response['sectionId'] = null; // Section not found
        }
        $stmt->close();
    }

    // Fetch catalog ID if catalog name is provided
    if (!empty($input['catalogName'])) {
        $stmt = $mysqli->prepare("SELECT catalogue_id FROM catalogue WHERE catalogue_name = ?");
        $stmt->bind_param('s', $input['catalogName']);
        $stmt->execute();
        $stmt->bind_result($catalogId);
        if ($stmt->fetch()) {
            $response['catalogId'] = $catalogId;
        } else {
            $response['catalogId'] = null; // Catalog not found
        }
        $stmt->close();
    }

    // Fetch subcatalog ID if subcatalog name is provided
    if (!empty($input['subcatalogName'])) {
        $stmt = $mysqli->prepare("SELECT subcat_id FROM subcatalogue WHERE subcat_name = ?");
        $stmt->bind_param('s', $input['subcatalogName']);
        $stmt->execute();
        $stmt->bind_result($subcatalogId);
        if ($stmt->fetch()) {
            $response['subcatalogId'] = $subcatalogId;
        } else {
            $response['subcatalogId'] = null; // Subcatalog not found
        }
        $stmt->close();
    }

    // Return the IDs as a JSON response
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>
