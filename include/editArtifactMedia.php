<?php
include 'artifact-db.php';

$id = $_POST['artifact-id'];
$sectionId = $_POST['section'];
$catalogueId = isset($_POST['catalog']) ? $_POST['catalog'] : 0;
$subcatId = isset($_POST['subcatalog']) ? $_POST['subcatalog'] : 0;
$name = $_POST['name'];

$artifactId = $id . "." . $sectionId . "." . $catalogueId . "." . $subcatId;

if (isset($_FILES['artifact-media'])  && $_FILES['artifact-media']['error'] == 0) {
    $file = $_FILES['artifact-media'];
    $fileTmpName = $file['tmp_name'];
    $uploadDir = '../assets/videos/specific/';
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $artifactId . "-" . $name . "." . $fileExt;
    $fileType = $file['type'];

    if (in_array($fileType, ['video/mp4', 'video/webm', 'video/ogg'])) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFilePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
            $message = '<script>
            window.location.href="../admin/admin.php";
            alert("Artifact media successfully added");
            </script>';

            // SQL Query withh all the fields properly initialized
            $sql = "UPDATE artifact_info 
                SET fileName = ? 
                WHERE artifact_id = ? AND section_id = ? AND catalogue_id = ? AND subcat_id = ?";
            
            $stmt = $mysqli->prepare($sql);

            // Bind parameters: all fields are now nullable
            $stmt->bind_param("siiss", $fileName, $artifactId, $sectionId, $catalogueId, $subcatId);

            // Execute and check for success/failure
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Artifact media successfully added']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }

            $stmt->close();
            $mysqli->close(); 
        } else {
            $message = '<script>
            window.location.href="../admin/admin.php";
            alert("Error uploading artifact media.")
            </script>';
        }
    } else {
        $message = '<script>
        window.location.href="../admin/admin.php";
        alert("Invalid media file type. Only MP4, WebM, and OGG are allowed.")
        </script>';
    }
} else {
    $mysqli->close(); 
    $message = '<script>
        window.location.href="../admin/admin.php";
        alert("No artifact media file selected. If a media file was selected, it might be an unsupported video format. Please try uploading a different video file.")
        </script>';
}

echo $message;