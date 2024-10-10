<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $artifactId = $input['artifactId'];
    $artifactName = $input['artifactName'];
    $imageData = $input['imageData'];

    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedData = base64_decode($imageData);

    $uploadDir = 'C:/wamp/www/Nexus/qr/'; // Adjust your file path (nung qr folder)
    $fileName = $artifactId . '-' . $artifactName . '.png';
    $filePath = $uploadDir . basename($fileName);

    if (file_put_contents($filePath, $decodedData)) {
        echo json_encode(['success' => true, 'message' => 'QR code saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save QR code.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>