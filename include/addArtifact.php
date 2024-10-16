<?php
include 'artifact-db.php';

$message = '';

$result = $mysqli->query("SELECT MAX(artifact_id) AS last_id FROM artifact_info");
$row = $result->fetch_assoc();
$lastArtifactId = $row['last_id'] !== null ? $row['last_id'] : 0;

$newArtifactId = $lastArtifactId + 1;

$artifactName = isset($_POST['artifact-name']) ? $_POST['artifact-name'] : '';
$sectionId = isset($_POST['section']) ? $_POST['section'] : 0;
$catalogId = isset($_POST['catalog']) && $_POST['catalog'] !== '' ? $_POST['catalog'] : 0;
$subCatalogId = isset($_POST['sub-catalog']) && $_POST['sub-catalog'] !== '' ? $_POST['sub-catalog'] : 0;
$description = isset($_POST['description']) ? $_POST['description'] : '';
$condition = isset($_POST['condition']) ? $_POST['condition'] : '';
$fileName = '';

// Upload Media
if (isset($_FILES['media-select'])  && $_FILES['media-select']['error'] == 0) {
    $file = $_FILES['media-select'];
    $fileTmpName = $file['tmp_name'];
    $uploadDir = '../assets/videos/specific/';

    if ($catalogId == 0 && $subCatalogId == 0) {
        $artifactId = $newArtifactId . "." . $sectionId;
    } else if ($subCatalogId == 0) {
        $artifactId = $newArtifactId . "." . $sectionId . "." . $catalogId;
    } else {
        $artifactId = $newArtifactId . "." . $sectionId . "." . $catalogId . "." . $subCatalogId;
    }

    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $artifactId . "-" . $artifactName;
    $fileType = $file['type'];

    if (in_array($fileType, ['video/mp4', 'video/webm', 'video/ogg'])) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFilePath = $uploadDir . $fileName . "." . $fileExt;
        
        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
            $message = '<script>
            window.location.href="../admin/admin.php";
            alert("Artifact successfully added. (With media file)");
            </script>';
        } else {
            $message = '<script>
            window.location.href="../admin/admin.php";
            alert("Error uploading the media.")
            </script>';
        }
    } else {
        $message = '<script>
        window.location.href="../admin/admin.php";
        alert("Invalid media file type. Only MP4, WebM, and OGG are allowed.")
        </script>';
    }
} else {
    $message = '<script>
        window.location.href="../admin/admin.php";
        alert("Artifact successfully added. (No media file) \nIf a media file was selected, it might be an unsupported video format. Please try uploading a different video file.")
        </script>';
}
// Initialize fields with conditional checks
$artifactName = isset($_POST['artifact-name']) && $_POST['artifact-name'] !== '' ? $_POST['artifact-name'] : NULL;
$sectionId = isset($_POST['section']) && $_POST['section'] !== '' ? $_POST['section'] : NULL;
$catalogId = isset($_POST['catalog']) && $_POST['catalog'] !== '' ? $_POST['catalog'] : NULL;
$subCatalogId = isset($_POST['sub-catalog']) && $_POST['sub-catalog'] !== '' ? $_POST['sub-catalog'] : NULL;
$description = isset($_POST['description']) && $_POST['description'] !== '' ? $_POST['description'] : NULL;
$condition = isset($_POST['condition']) && $_POST['condition'] !== '' ? $_POST['condition'] : NULL;
$fileName = isset($fileName) && $fileName !== '' ? $fileName : NULL;

// Generating the new artifact ID with the same fallback logic
$result = $mysqli->query("SELECT MAX(artifact_id) AS last_id FROM artifact_info");
$row = $result->fetch_assoc();
$lastArtifactId = isset($row['last_id']) && $row['last_id'] !== '' ? $row['last_id'] : 0;
$newArtifactId = $lastArtifactId + 1;

// SQL Query with all the fields properly initialized
$sql = "INSERT INTO artifact_info (artifact_id, section_id, catalogue_id, subcat_id, name, description, `condition`, fileName) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);

// Bind parameters: all fields are now nullable (can accept NULL values)
$stmt->bind_param("iiiissss", $newArtifactId, $sectionId, $catalogId, $subCatalogId, $artifactName, $description, $condition, $fileName);

// Execute and check for success/failure
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artifact successfully added', 'artifact_id' => $newArtifactId]);
    echo $message;
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();

?>