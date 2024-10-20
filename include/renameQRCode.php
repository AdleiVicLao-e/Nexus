<?php
header('Content-Type: application/json'); // Ensure the response is treated as JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $artifactId = $input['artifactId'];
    $newArtifactName = $input['newArtifactName'];

    // Define the upload directory
    $uploadDir = '../qr/';
    $filePattern = $uploadDir . $artifactId . '-*.png'; // Pattern to find the old file

    // Find the existing QR code file using the artifactId
    $existingFiles = glob($filePattern); // Search for the file

    if (!empty($existingFiles)) {
        $oldFilePath = $existingFiles[0]; // Get the first matching file
        $newFilePath = $uploadDir . $artifactId . '-' . $newArtifactName . '.png'; // New file name

        // Rename the old file to the new file name
        if (rename($oldFilePath, $newFilePath)) {
            echo json_encode(['success' => true, 'message' => 'QR code filename updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to rename QR code file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'QR code file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
