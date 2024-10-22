<?php
global $mysqli, $row;
include 'artifact-db.php'; // Include your database connection

$data = json_decode(file_get_contents("php://input"), true);
$deleteMedia = isset($data['deleteMedia']) && $data['deleteMedia'];

if (empty($data['ids'])) {
    echo json_encode(['success' => false, 'message' => 'No artifacts selected for deletion.']);
    exit;
}

$ids = $data['ids'];
$idString = implode(',', array_map('intval', $ids)); // Sanitize IDs

// Fetch fileName for all the selected artifact IDs
$selectQuery = "SELECT artifact_id, fileName, name FROM artifact_info WHERE artifact_id IN ($idString)";
$result = $mysqli->query($selectQuery);

if ($result && $result->num_rows > 0) {
    $qrName = "{$idString}-{$row['name']}";
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

                    // Using realpath for accurate file paths
                    $videoPath = realpath("../assets/videos/specific/{$fileName}");
                    $qrCodePath = realpath("../qr/{$qrName}.png");

                    // Debugging: Output the paths for verification
                    error_log("Attempting to delete: $videoPath, $qrCodePath");

                    // Delete the video file if it exists
                    if ($videoPath && file_exists($videoPath)) {
                        if (unlink($videoPath)) {
                            $response['media'][] = "Video file deleted for artifact ID {$id}.";
                        } else {
                            $response['media'][] = "Failed to delete video file for artifact ID {$id}.";
                        }
                    } else {
                        $response['media'][] = "Video file not found for artifact ID {$id}: " . $videoPath;
                    }

                    // Delete the QR code file if it exists
                    if ($qrCodePath && file_exists($qrCodePath)) {
                        if (unlink($qrCodePath)) {
                            $response['media'][] = "QR code file deleted for artifact ID {$id}.";
                        } else {
                            $response['media'][] = "Failed to delete QR code file for artifact ID {$id}.";
                        }
                    } else {
                        $response['media'][] = "QR code file not found for artifact ID {$id}: " . $qrCodePath;
                    }
                }
            }
        }
        $response['success'] = true;
        $response['message'] = 'Selected artifacts and their media deleted successfully.';
    } else {
        $response['success'] = false;
        $response['message'] = 'Error deleting artifacts: ' . $mysqli->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = 'No artifacts found for the provided IDs.';
}

$mysqli->close();
echo json_encode($response);
?>
