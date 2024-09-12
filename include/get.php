<?php
include 'artifact-db.php';

$sectionId = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
$catalogId = isset($_GET['catalog_id']) ? (int)$_GET['catalog_id'] : 0;

$sections = [];
$catalogues = [];
$subcatalogues = [];

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
} elseif ($sectionId) {
    $catalogueQuery = "SELECT catalogue_id, catalogue_name FROM catalogue WHERE section_id = $sectionId";
    $catalogueResult = $mysqli->query($catalogueQuery);
    if ($catalogueResult->num_rows > 0) {
        while ($row = $catalogueResult->fetch_assoc()) {
            $catalogues[] = $row;
        }
    }
} elseif ($catalogId) {
    $subcatalogueQuery = "SELECT subcat_id, subcat_name FROM subcatalogue WHERE catalogue_id = $catalogId";
    $subcatalogueResult = $mysqli->query($subcatalogueQuery);
    if ($subcatalogueResult->num_rows > 0) {
        while ($row = $subcatalogueResult->fetch_assoc()) {
            $subcatalogues[] = $row;
        }
    }
}

$mysqli->close();

echo json_encode([
    'sections' => $sections,
    'catalogues' => $catalogues,
    'subcatalogues' => $subcatalogues
]);
?>