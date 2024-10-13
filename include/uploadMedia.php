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