<?php
include 'artifact-db.php';

// Retrieve 'id' from the POST data
$subcatalogueId = $_POST['id'];

// Begin transaction
$mysqli->begin_transaction();

try {
    // Update artifacts to set subcat_id to NULL where the subcat_id matches the one being deleted
    $stmtUpdateArtifacts = $mysqli->prepare("UPDATE artifact_info SET subcat_id = NULL WHERE subcat_id = ?");
    $stmtUpdateArtifacts->bind_param("i", $subcatalogueId);
    $stmtUpdateArtifacts->execute();
    $stmtUpdateArtifacts->close();

    $stmtDelete = $mysqli->prepare("DELETE FROM subcatalogue WHERE subcat_id = ?");
    $stmtDelete->bind_param("i", $subcatalogueId);
    $stmtDelete->execute();
    $stmtDelete->close();

    $mysqli->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Subcatalog ID: ' . $subcatalogueId . ' has been deleted successfully.'
    ]);
} catch (Exception $e) {
    // Rollback transaction
    $mysqli->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// Close the connection
$mysqli->close();
?>