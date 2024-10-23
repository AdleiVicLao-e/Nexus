<?php
global $mysqli;
include 'artifact-db.php'; // Include your database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0;
$deleteMedia = isset($input['deleteMedia']) ? $input['deleteMedia'] : false; // New flag for media deletion

$response = ['success' => false, 'message' => '', 'raw_input' => $input];

if ($id) {
    // Fetch fileName and artifact name from the artifact_info table
    $selectQuery = "SELECT fileName, name FROM artifact_info WHERE artifact_id = $id";
    $result = $mysqli->query($selectQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileName = $row['fileName'];
        $qrName = "{$id}-{$row['name']}";

        // Proceed with deletion of the artifact record
        $deleteQuery = "DELETE FROM artifact_info WHERE artifact_id = $id";
        if ($mysqli->query($deleteQuery) === TRUE) {
            // If artifact is successfully deleted, proceed to delete media
            if ($deleteMedia) {
                $videoPath = "../assets/videos/specific/{$fileName}";
                $qrCodePath = "../qr/{$qrName}.png";

                // Delete the video file if it exists
                if (file_exists($videoPath)) {
                    unlink($videoPath);
                }


                // Delete the QR code file if it exists
                if (file_exists($qrCodePath)) {
                    unlink($qrCodePath);
                }
            }
            $response['success'] = true;
            $response['message'] = 'Artifact and related media deleted successfully.';
        } else {
            $response['message'] = 'Error deleting artifact: ' . $mysqli->error;
        }
    } else {
        $response['message'] = 'Artifact not found.';
    }
}

$mysqli->close();
echo json_encode($response);

?>
