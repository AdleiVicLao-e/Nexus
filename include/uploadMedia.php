<?php
include 'artifact-db.php';

$message = '';

$result = $mysqli->query("SELECT MAX(id) AS last_id FROM uncategorized_media");
$row = $result->fetch_assoc();
$lastId = $row['last_id'] !== null ? $row['last_id'] : 0;
$newId = $lastId + 1;

$uploadDir = '../assets/videos/general/';

$mediaTitle = isset($_POST['media-title']) ? $_POST['media-title'] : '';
$mediaDesc = isset($_POST['media-description']) ? $_POST['media-description'] : '';

// Upload Media
if (isset($_FILES['media-file'])  && $_FILES['media-file']['error'] == 0) {
    $file = $_FILES['media-file'];
    $fileTmpName = $file['tmp_name'];
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $newId . "-" . $mediaTitle;
    $fileType = $file['type'];

    if (in_array($fileType, ['video/mp4', 'video/webm', 'video/ogg'])) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFilePath = $uploadDir . $fileName . "." . $fileExt;
        
        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
            $message = '<script>
            window.location.href="../admin/admin.php";
            alert("Media successfully added");
            </script>';
            $sql = "INSERT INTO uncategorized_media (id, title, description, fileName) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("isss", $newId, $mediaTitle, $mediaDesc, $fileName);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Media successfully added']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }

            $stmt->close();
            $mysqli->close(); 
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
    $mysqli->close(); 
    $message = '<script>
        window.location.href="../admin/admin.php";
        alert("No media file selected. If a media file was selected, it might be an unsupported video format. Please try uploading a different video file.")
        </script>';
}

echo $message;