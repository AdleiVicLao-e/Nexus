<?php
include 'artifact-db.php';

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

$sql = "INSERT INTO artifact_info (artifact_id, section_id, catalogue_id, subcat_id, name, description, `condition`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

// Upload Media
if (isset($_FILES['media-upload'])) {
    $file = $_FILES['media-upload'];
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
    $fileSize = $file['size'];
    $fileType = $file['type'];

    $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];

    if (in_array($fileType, $allowedTypes)) {

        if ($fileSize < 50 * 1024 * 1024) {

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadFilePath = $uploadDir . $fileName . "." . $fileExt;
            if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                // Save to JSON
                $mediaDetails = [
                    'artifact_id' => $artifactId,
                    'artifact_name' => $artifactName,
                    'description' => $description,
                    'condition' => $condition,
                    'file_path' => $uploadFilePath,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                    'upload_time' => date('Y-m-d H:i:s')
                ];

                $jsonFile = '../assets/videos/specific-media.json';
                $existingData = [];

                if (file_exists($jsonFile)) {
                    $existingData = json_decode(file_get_contents($jsonFile), true);
                }

                // Append new media details
                $existingData[] = $mediaDetails;

                // Save updated data back to JSON file
                file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));

                echo '<script>
                    window.location.href="../admin/admin.php";
                    alert("File successfully uploaded!");
                    </script>';
            } else {
                echo '<script>
                    window.location.href="../admin/admin.php";
                    alert("Error uploading the media.")
                    </script>';
            }
        } else {
            echo '<script>
                window.location.href="../admin/admin.php";
                alert("Media file size exceeds the allowed limit (50MB).")
                </script>';
        }
    } else {
        echo '<script>
            window.location.href="../admin/admin.php";
            alert("Invalid media file type. Only MP4, WebM, and OGG are allowed.")
            </script>';
    }
}

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiiisss", $newArtifactId, $sectionId, $catalogId, $subCatalogId, $artifactName, $description, $condition);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Artifact successfully added', 'artifact_id' => $newArtifactId]);
    echo '<script>
            window.location.href="../admin/admin.php";
            alert("Artifact successfully added.")
            </script>';
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();   
?>