<?php
global $mysqli;
include 'artifact-db.php'; // Include database connection

// Clean any output before sending the header
ob_clean();
header('Content-Type: application/json'); // Ensure the response is always JSON

try {
    // Initialize variables from POST data
    $newArtifactId = $_POST['newArtifactId'];
    $sectionId = $_POST['sectionId'];
    $catalogId = isset($_POST['catalogId']) && $_POST['catalogId'] !== '' ? $_POST['catalogId'] : 0;
    $subCatalogId = isset($_POST['subCatalogId']) && $_POST['subCatalogId'] !== '' ? $_POST['subCatalogId'] : 0;
    $artifactName = $_POST['artifactName'];

    // Upload Media
    if (isset($_FILES['media-select']) && $_FILES['media-select']['error'] == 0) {
        $file = $_FILES['media-select'];
        $fileTmpName = $file['tmp_name'];
        $uploadDir = '../assets/videos/specific/';
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileType = $file['type'];

        // Construct the artifact ID for the file name
        if ($catalogId == 0 && $subCatalogId == 0) {
            $artifactId = $newArtifactId . "." . $sectionId;
        } else if ($subCatalogId == 0) {
            $artifactId = $newArtifactId . "." . $sectionId . "." . $catalogId;
        } else {
            $artifactId = $newArtifactId . "." . $sectionId . "." . $catalogId . "." . $subCatalogId;
        }
        $fileName = $artifactId . "-" . $artifactName . "." . $fileExt;

        // Validate file type
        if (in_array($fileType, ['video/mp4', 'video/ogg', 'video/mpeg'])) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                // Insert the file path into the database
                $sql = "UPDATE artifact_info SET fileName = ? WHERE artifact_id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("si", $fileName, $newArtifactId);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Artifact successfully added. (With media file)']);
                } else {
                    throw new Exception('Error updating file path in the database: ' . $stmt->error);
                }

                $stmt->close();
            } else {
                throw new Exception('Error uploading the media.');
            }
        } else {
            throw new Exception('Invalid media file type. Only MP4, OGG, and MPEG are allowed.');
        }
    } else {
        echo json_encode(['success' => true, 'message' => 'Artifact successfully added. (No media file)']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$mysqli->close();
?>
