<?php

include 'artifact-db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0;

$response = ['success' => false, 'message' => ''];

if ($id) {
    $deleteQuery = "DELETE FROM artifact_info WHERE artifact_id = $id"; // Change 'artifacts' to your actual table name
    if ($mysqli->query($deleteQuery) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Artifact deleted successfully.';
    } else {
        $response['message'] = 'Error deleting artifact: ' . $mysqli->error;
    }
}

$mysqli->close();
echo json_encode($response);
?>