<?php
global $mysqli;
include 'artifact-db.php'; // Include your database connection

$data = json_decode(file_get_contents("php://input"), true);
$deleteMedia = isset($data['deleteMedia']) ? $data['deleteMedia'] : false;

if (!isset($data['ids']) || empty($data['ids'])) {
    echo json_encode(['success' => false, 'message' => 'No artifacts selected for deletion.']);
    exit;
}

$ids = $data['ids'];
$idString = implode(',', array_map('intval', $ids)); // Sanitize IDs

// Fetch fileName for all the selected artifact IDs
$selectQuery = "SELECT artifact_id, fileName FROM artifact_info WHERE artifact_id IN ($idString)";
$result = $mysqli->query($selectQuery);

if ($result && $result->num_rows > 0) {
    $fileNames = [];
    while ($row = $result->fetch_assoc()) {
        $fileNames[$row['artifact_id']] = $row['fileName'];
    }

    // Proceed with deletion of the artifact records
    $query = "DELETE FROM artifact_info WHERE artifact_id IN ($idString)";
    if ($mysqli->query($query) === TRUE) {
        // If artifacts are successfully deleted, proceed to delete their media
        if ($deleteMedia) {
            foreach ($ids as $id) {
                if (isset($fileNames[$id])) {
                    $fileName = $fileNames[$id];

                    // Assuming the video path uses the fileName from the DB
                    $videoPath = "../assets/videos/specific/{$fileName}"; // Adjust path and extension as needed
                    $qrCodePath = "../assets/qrcodes/{$id}.png"; // Assuming QR code path is still based on ID

                    // Delete the video file if it exists
                    if (file_exists($videoPath)) {
                        unlink($videoPath);
                    }

                    // Delete the QR code if it exists
                    if (file_exists($qrCodePath)) {
                        unlink($qrCodePath);
                    }
                }
            }
        }
        echo json_encode(['success' => true, 'message' => 'Selected artifacts and their media deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting artifacts: ' . $mysqli->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No artifacts found for the provided IDs.']);
}

$mysqli->close();
?>
