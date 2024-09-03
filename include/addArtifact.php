<?php
include 'artifact-db.php';

header('Content-Type: application/json');

$artifactName = $_POST['artifact-name'];
$sectionId = $_POST['section'];
$catalogId = $_POST['catalog'];
$subCatalogId = $_POST['sub-catalog'];
$description = $_POST['description'];
$condition = $_POST['condition'];

$sql = "INSERT INTO artifact_info (section_id, catalogue_id, subcat_id, name, description, `condition`) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiisss", $sectionId, $catalogId, $subCatalogId, $artifactName, $description, $condition);

if ($stmt->execute()) {
    echo "New artifact added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>