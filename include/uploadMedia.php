<?php
// Define upload directory
$uploadDir = '../assets/videos/general/';
if (isset($_FILES['media-upload']) && $_FILES['media-upload']['error'] == 0) {
    // File variables
    $file = $_FILES['media-upload'];
    $fileTmpName = $file['tmp_name'];
    $fileName = basename($file['name']);
    $fileSize = $file['size'];
    $fileType = $file['type'];

    // Define allowed file types (video types)
    $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];

    // Check if the file type is valid
    if (in_array($fileType, $allowedTypes)) {

        // Check file size (optional: e.g., limit to 50MB)
        if ($fileSize < 50 * 1024 * 1024) {
            // Ensure the directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Define the path to save the file
            // TODO file name LOG Start - 4:33 End - 
            $uploadFilePath = $uploadDir . $fileName;

            // Move the uploaded file to the target directory
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
} else {
    echo '<script>
        window.location.href="../admin/admin.php";
        alert("No media file uploaded or an error occurred during upload.")
        </script>';
}