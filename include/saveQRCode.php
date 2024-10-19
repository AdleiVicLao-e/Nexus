<?php
header('Content-Type: application/json'); // Ensure the response is treated as JSON
error_reporting(0); // Disable error reporting to prevent accidental HTML output

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $artifactId = $input['artifactId'];
    $artifactName = $input['artifactName'];
    $imageData = $input['imageData'];

    // Process the base64 image data
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedData = base64_decode($imageData);

    // Path to save the image
    $uploadDir = '../qr/';
    $fileName = $artifactId . '-' . $artifactName . '.png';
    $filePath = $uploadDir . basename($fileName);

    // Save the file and return success/failure as JSON
    if (file_put_contents($filePath, $decodedData)) {
        echo json_encode(['success' => true, 'message' => 'QR code saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save QR code.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
