<?php
include 'artifact-db.php';

// Upload Video

if (isset($_POST['media_id'], $_POST['new-media-title'], $_POST['new-media-description'])) {
    $mediaId = $_POST['media_id'];
    $newTitle = $_POST['new-media-title'];
    $newDescription = $_POST['new-media-description'];

    // Replace video
    if (isset($_FILES['new-media-file'])  && $_FILES['new-media-file']['error'] == 0) {
        $file = $_FILES['new-media-file'];
        $fileTmpName = $file['tmp_name'];
        $uploadDir = '../assets/videos/general/';
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $mediaId . "-" . $newTitle . "." . $fileExt;
        $fileType = $file['type'];

        // Check if a file with the same mediaId already exists and delete it
        $existingFiles = glob($uploadDir . $mediaId . '-*');
        if (count($existingFiles) > 0) {
            // File with the same mediaId exists, so delete it
            unlink($existingFiles[0]);
        }
    
        if (in_array($fileType, ['video/mp4', 'video/webm', 'video/ogg'])) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            $uploadFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                $message = '<script>
                window.location.href="../admin/admin.php";
                alert("Media successfully added");
                </script>';
            }
        }
    }

    // SQL Query with all the fields properly initialized
    $sql = "UPDATE igorot_dances SET title = ?, description = ?, file_name = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        // Handle error in preparing the statement
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement: ' . $mysqli->error]);
        exit();
    }

    // Bind parameters: 's' for string, 'i' for integer
    $stmt->bind_param("sssi", $newTitle, $newDescription, $fileName, $mediaId);

    // Execute the statement and check for success/failure
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Media Updated',
            'mediaId' => $mediaId,
            'newTitle' => $newTitle,
            'newDescription' => $newDescription
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $stmt->error
        ]);
    }

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Required data not provided.'
    ]);
}