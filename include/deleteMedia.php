<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$mediaId = $_POST['id'];

// Fetch the filename from the database
$stmtFetchFile = $mysqli->prepare("SELECT fileName FROM uncategorized_media WHERE id = ?");
$stmtFetchFile->bind_param("i", $mediaId);
$stmtFetchFile->execute();
$stmtFetchFile->bind_result($fileName);

// Buffer the result
$stmtFetchFile->store_result();

// Fetch the result
if ($stmtFetchFile->fetch()) {
    $filePath = '../assets/videos/general/' . $fileName;

    // Ensure the file exists before attempting to delete
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            // File deleted successfully, now delete the database record
            $stmtDelete = $mysqli->prepare("DELETE FROM uncategorized_media WHERE id = ?");
            $stmtDelete->bind_param("i", $mediaId);

            // Execute the delete statement and check for success/failure
            if ($stmtDelete->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Media with ID ' . $mediaId . ' has been deleted successfully.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error deleting record from database: ' . $stmtDelete->error
                ]);
            }

            $stmtDelete->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting file from filesystem.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'File does not exist or is not accessible.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No media found with ID ' . $mediaId
    ]);
}

// Close the statements and the connection
$stmtFetchFile->close();
$mysqli->close();