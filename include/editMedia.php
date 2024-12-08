<?php
include 'artifact-db.php';

if (isset($_POST['media_id'], $_POST['new-media-title'], $_POST['new-media-description'])) {
    $mediaId = $_POST['media_id'];
    $newTitle = $_POST['new-media-title'];
    $newDescription = $_POST['new-media-description'];

    // SQL Query with all the fields properly initialized
    $sql = "UPDATE igorot_dances SET title = ?, description = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        // Handle error in preparing the statement
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement: ' . $mysqli->error]);
        exit();
    }

    // Bind parameters: 's' for string, 'i' for integer
    $stmt->bind_param("ssi", $newTitle, $newDescription, $mediaId);

    // Execute the statement and check for success/failure
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Media Updated',
            'mediaId' => $mediaId,
            'newTitle' => $newTitle,
            'newDescription' => $newDescription
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $stmt->error
        ]);
    }

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Required data not provided.'
    ]);
}