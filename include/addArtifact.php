<?php
include 'artifact-db.php';

$result = $mysqli->query("SELECT MAX(artifact_id) AS last_id FROM artifact_info");
$row = $result->fetch_assoc();
$lastArtifactId = $row['last_id'] !== null ? $row['last_id'] : 0;

$newArtifactId = $lastArtifactId + 1;

$artifactName = $_POST['artifact-name'];
$sectionId = $_POST['section'];
$catalogId = isset($_POST['catalog']) && $_POST['catalog'] !== '' ? $_POST['catalog'] : 0;
$subCatalogId = isset($_POST['sub-catalog']) && $_POST['sub-catalog'] !== '' ? $_POST['sub-catalog'] : 0;
$description = $_POST['description'];
$condition = $_POST['condition'];

$sql = "INSERT INTO artifact_info (artifact_id, section_id, catalogue_id, subcat_id, name, description, `condition`) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiiisss", $newArtifactId, $sectionId, $catalogId, $subCatalogId, $artifactName, $description, $condition);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artifact successfully added', 'artifact_id' => $newArtifactId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>