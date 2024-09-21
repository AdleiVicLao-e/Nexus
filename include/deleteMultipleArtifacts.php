<?php
include 'artifact-db.php';


$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['ids']) || empty($data['ids'])) {
    echo json_encode(['success' => false, 'message' => 'No artifacts selected for deletion.']);
    exit;
}

$ids = $data['ids'];
$idString = implode(',', array_map('intval', $ids)); // Sanitizing IDs

$query = "DELETE FROM artifact_info WHERE artifact_id IN ($idString)";
if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Selected artifacts deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting artifacts: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>