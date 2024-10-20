<?php
include 'artifact-db.php'; // Include database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clean any output before sending the header
ob_clean();
header('Content-Type: application/json'); // Ensure the response is always JSON

try {
    // Decode the input JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for valid input
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input.');
    }

    // Initialize variables from input
    $artifactId = $input['artifactId'];
    $newFileName = $input['newFileName'];
    $fileExt = $input['fileExt']; // e.g., 'mp4', 'avi'

    // Define the upload directory for videos
    $videoUploadDir = '../assets/videos/specific/';

    // Construct the old video file pattern to find the existing video
    $oldVideoPattern = $videoUploadDir . $artifactId . '-*.' . $fileExt;

    // Check if the old video file exists
    $existingFiles = glob($oldVideoPattern); // Search for matching files

    if (!empty($existingFiles)) {
        $oldFilePath = $existingFiles[0]; // Get the first matching file
        $newVideoPath = $videoUploadDir . $newFileName . '.' . $fileExt; // New file path

        // Rename the old video file to the new file name
        if (rename($oldFilePath, $newVideoPath)) {
            echo json_encode(['success' => true, 'message' => 'Video filename updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to rename video file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Video file not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
