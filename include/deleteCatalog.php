<?php
include 'artifact-db.php';

$catalogueId = $_POST['id'];

$mysqli->begin_transaction();

try {
    // Update artifacts to set catalogue_id to NULL
    $stmtUpdate = $mysqli->prepare("UPDATE artifact_info SET catalogue_id = NULL WHERE catalogue_id = ?");
    $stmtUpdate->bind_param("i", $catalogueId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Check if there are subcatalogs associated with the catalog
    $stmtCheckSubcatalogs = $mysqli->prepare("SELECT subcat_id FROM subcatalogue WHERE catalogue_id = ?");
    $stmtCheckSubcatalogs->bind_param("i", $catalogueId);
    $stmtCheckSubcatalogs->execute();
    $result = $stmtCheckSubcatalogs->get_result();
    $hasSubcatalogs = $result->num_rows > 0;
    $stmtCheckSubcatalogs->close();

    // If there are subcatalogs, set their subcat_id to NULL for artifacts
    if ($hasSubcatalogs) {
        // Update artifacts to set subcatalog_id to NULL for subcatalogs under this catalog
        $stmtUpdateArtifacts = $mysqli->prepare(
            "UPDATE artifact_info SET subcat_id = NULL WHERE subcat_id IN (SELECT subcat_id FROM subcatalogue WHERE catalogue_id = ?)"
        );
        $stmtUpdateArtifacts->bind_param("i", $catalogueId);
        $stmtUpdateArtifacts->execute();
        $stmtUpdateArtifacts->close();

        // Delete the subcatalogs
        $stmtDeleteSubcatalogs = $mysqli->prepare("DELETE FROM subcatalogue WHERE catalogue_id = ?");
        $stmtDeleteSubcatalogs->bind_param("i", $catalogueId);
        $stmtDeleteSubcatalogs->execute();
        $stmtDeleteSubcatalogs->close();
    }

    // Delete the catalog
    $stmtDeleteCatalog = $mysqli->prepare("DELETE FROM catalogue WHERE catalogue_id = ?");
    $stmtDeleteCatalog->bind_param("i", $catalogueId);
    $stmtDeleteCatalog->execute();
    $stmtDeleteCatalog->close();

    $mysqli->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Catalogue ID: ' . $catalogueId . ' has been deleted successfully.'
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