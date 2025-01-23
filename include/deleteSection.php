<?php
include 'artifact-db.php';

$sectionId = $_POST['id'];

$mysqli->begin_transaction();

try {
    // Update artifacts to set section_id to NULL
    $stmtUpdate = $mysqli->prepare("UPDATE artifact_info SET section_id = NULL WHERE section_id = ?");
    $stmtUpdate->bind_param("i", $sectionId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Check if there are catalogs associated with the section
    $stmtCheckCatalogs = $mysqli->prepare("SELECT catalogue_id FROM catalogue WHERE section_id = ?");
    $stmtCheckCatalogs->bind_param("i", $sectionId);
    $stmtCheckCatalogs->execute();
    $result = $stmtCheckCatalogs->get_result();
    $hasCatalogs = $result->num_rows > 0;
    $stmtCheckCatalogs->close();

    // If there are catalogs, check for subcatalogs
    if ($hasCatalogs) {
        // Check if there are subcatalogs under any of the catalogs in the section
        $stmtCheckSubcatalogs = $mysqli->prepare(
            "SELECT subcat_id FROM subcatalogue WHERE catalogue_id IN (SELECT catalogue_id FROM catalogue WHERE section_id = ?)"
        );
        $stmtCheckSubcatalogs->bind_param("i", $sectionId);
        $stmtCheckSubcatalogs->execute();
        $resultSubcatalogs = $stmtCheckSubcatalogs->get_result();
        $hasSubcatalogs = $resultSubcatalogs->num_rows > 0;
        $stmtCheckSubcatalogs->close();

        // If there are subcatalogs, handle them
        if ($hasSubcatalogs) {
            // Update artifacts to set subcatalog_id to NULL for subcatalogs in the section
            $stmtUpdateArtifacts = $mysqli->prepare(
                "UPDATE artifact_info SET subcat_id = NULL WHERE subcat_id IN (SELECT subcat_id FROM subcatalogue WHERE catalogue_id IN (SELECT catalogue_id FROM catalogue WHERE section_id = ?))"
            );
            $stmtUpdateArtifacts->bind_param("i", $sectionId);
            $stmtUpdateArtifacts->execute();
            $stmtUpdateArtifacts->close();

            // Delete the subcatalogs
            $stmtDeleteSubcatalogs = $mysqli->prepare(
                "DELETE FROM subcatalogue WHERE catalogue_id IN (SELECT catalogue_id FROM catalogue WHERE section_id = ?)"
            );
            $stmtDeleteSubcatalogs->bind_param("i", $sectionId);
            $stmtDeleteSubcatalogs->execute();
            $stmtDeleteSubcatalogs->close();
        }

        // Update artifacts to set catalogue_id to NULL for catalogs in the section
        $stmtUpdateArtifacts = $mysqli->prepare(
            "UPDATE artifact_info SET catalogue_id = NULL WHERE catalogue_id IN (SELECT catalogue_id FROM catalogue WHERE section_id = ?)"
        );
        $stmtUpdateArtifacts->bind_param("i", $sectionId);
        $stmtUpdateArtifacts->execute();
        $stmtUpdateArtifacts->close();

        // Delete catalogs in the section
        $stmtDeleteCatalogs = $mysqli->prepare("DELETE FROM catalogue WHERE section_id = ?");
        $stmtDeleteCatalogs->bind_param("i", $sectionId);
        $stmtDeleteCatalogs->execute();
        $stmtDeleteCatalogs->close();
    }

    $stmtDeleteSection = $mysqli->prepare("DELETE FROM section WHERE section_id = ?");
    $stmtDeleteSection->bind_param("i", $sectionId);
    $stmtDeleteSection->execute();
    $stmtDeleteSection->close();

    $mysqli->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Section ID: ' . $sectionId . ' has been deleted successfully.'
    ]);
} catch (Exception $e) {
    $mysqli->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$mysqli->close();
?>