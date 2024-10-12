<?php
$uploadDir = '../assets/videos/general/';

$mediaTitle = $_POST['media-title'];
$mediaDescription = $_POST['media-description'];

$file = $_FILES['media-file'];
$fileTmpName = $file['tmp_name'];
$fileName = basename($file['name']);
$fileSize = $file['size'];
$fileType = $file['type'];

$allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];

if (in_array($fileType, $allowedTypes)) {

    if ($fileSize < 50 * 1024 * 1024) {

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
            // Save to JSON
            $mediaDetails = [
                'title' => $mediaTitle,
                'description' => $mediaDescription,
                'file_path' => $uploadFilePath,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'file_type' => $fileType,
                'upload_time' => date('Y-m-d H:i:s')
            ];

            $jsonFile = '../assets/videos/general-media.json';
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
?>