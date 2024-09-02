<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$database = "kultoura";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$artifactName = $_POST['artifact-name'];
$sectionId = $_POST['section'];
$catalogId = $_POST['catalog'];
$subCatalogId = $_POST['sub-catalog'];
$description = $_POST['description'];
$condition = $_POST['condition'];

$sql = "INSERT INTO artifact_info (section_id, catalogue_id, subcat_id, name, description, `condition`) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiisss", $sectionId, $catalogId, $subCatalogId, $artifactName, $description, $condition);

if ($stmt->execute()) {
    echo "New artifact added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>