<?php
include 'artifact-db.php';
// Get input values and trim whitespace
$artifactName = trim($_POST['artifact-name']);
$sectionId = $_POST['section'];
$catalogId = isset($_POST['catalog']) && $_POST['catalog'] !== '' ? $_POST['catalog'] : 0;
$subCatalogId = isset($_POST['sub-catalog']) && $_POST['sub-catalog'] !== '' ? $_POST['sub-catalog'] : 0;
$description = trim($_POST['description']);
$condition = trim($_POST['condition']);

$artifactName = htmlspecialchars($artifactName, ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
$condition = htmlspecialchars($condition, ENT_QUOTES, 'UTF-8');

// Validate inputs
if (empty($artifactName) || empty($description) || empty($condition)) {
    echo json_encode(['success' => false, 'message' => 'All fields must be filled and cannot be empty or whitespace only.']);
    exit;
}

// Check if artifact name already exists
$checkQuery = "SELECT COUNT(*) AS count FROM artifact_info WHERE name = ?";
$checkStmt = $mysqli->prepare($checkQuery);
$checkStmt->bind_param("s", $artifactName);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$checkRow = $checkResult->fetch_assoc();

if ($checkRow['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'An artifact with this name already exists.']);
    $checkStmt->close();
    $mysqli->close();
    exit;
}
$checkStmt->close();

// Generate new artifact ID
$result = $mysqli->query("SELECT MAX(artifact_id) AS last_id FROM artifact_info");
$row = $result->fetch_assoc();
$lastArtifactId = $row['last_id'] !== null ? $row['last_id'] : 0;
$newArtifactId = $lastArtifactId + 1;

// Insert the new artifact into the database
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