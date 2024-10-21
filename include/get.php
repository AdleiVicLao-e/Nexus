<?php
include 'artifact-db.php';

$sectionId = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
$catalogId = isset($_GET['catalog_id']) ? (int)$_GET['catalog_id'] : 0;

$sections = [];
$catalogues = [];
$subcatalogues = [];

// Fetch sections if neither sectionId nor catalogId is provided
if (!$sectionId && !$catalogId) {
    $sectionQuery = "SELECT section_id, section_name FROM section";
    $sectionResult = $mysqli->query($sectionQuery);
    if ($sectionResult->num_rows > 0) {
        while ($row = $sectionResult->fetch_assoc()) {
            $sections[] = $row;
        }
    }

    $catalogueQuery = "SELECT catalogue_id, catalogue_name FROM catalogue";
    $catalogueResult = $mysqli->query($catalogueQuery);
    if ($catalogueResult->num_rows > 0) {
        while ($row = $catalogueResult->fetch_assoc()) {
            $catalogues[] = $row;
        }
    }

    $subcatalogueQuery = "SELECT subcat_id, subcat_name FROM subcatalogue";
    $subcatalogueResult = $mysqli->query($subcatalogueQuery);
    if ($subcatalogueResult->num_rows > 0) {
        while ($row = $subcatalogueResult->fetch_assoc()) {
            $subcatalogues[] = $row;
        }
    }
}

// Fetch catalogues for a specific section
if ($sectionId) {
    $catalogueQuery = "SELECT catalogue_id, catalogue_name FROM catalogue WHERE section_id = ?";
    $stmt = $mysqli->prepare($catalogueQuery);
    $stmt->bind_param('i', $sectionId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $catalogues[] = $row;
        }
    }
    $stmt->close();
}

// Fetch subcatalogues for a specific catalog
if ($catalogId) {
    $subcatalogueQuery = "SELECT subcat_id, subcat_name FROM subcatalogue WHERE catalogue_id = ?";
    $stmt = $mysqli->prepare($subcatalogueQuery);
    $stmt->bind_param('i', $catalogId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subcatalogues[] = $row;
        }
    }
    $stmt->close();
}

$mysqli->close();
header('Content-Type: application/json');
echo json_encode([
    'sections' => $sections,
    'catalogues' => $catalogues,
    'subcatalogues' => $subcatalogues
]);
?>
