<?php

include 'artifact-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'create_section':
            $newSectionTitle = isset($_POST['new_section']) ? trim($_POST['new_section']) : '';

            // Validate input
            if (empty($newSectionTitle)) {
                $response['message'] = "Section title cannot be empty";
                echo json_encode($response);
                exit;
            }

            $newSectionTitle = htmlspecialchars($newSectionTitle, ENT_QUOTES, 'UTF-8');

            // Check if the section name already exists
            $checkQuery = "SELECT COUNT(*) AS count FROM section WHERE section_name = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("s", $newSectionTitle);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                $response['message'] = "Section with this name already exists";
                echo json_encode($response);
                exit;
            } else {
                // Generate new section ID
                $idQuery = "SELECT MAX(section_id) AS max_id FROM section";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newSectionId = $row['max_id'] + 1;

                // Insert new section
                $stmt = $mysqli->prepare("INSERT INTO section (section_id, section_name) VALUES (?, ?)");
                $stmt->bind_param("is", $newSectionId, $newSectionTitle);

                if ($stmt->execute()) {
                    $response['message'] = "New section added";
                    echo json_encode($response);
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
            break;

        case 'create_catalog':
            $sectionId = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
            $newCatalogName = isset($_POST['new_catalog']) ? trim($_POST['new_catalog']) : '';

            // Validate input
            if (empty($newCatalogName) || $sectionId <= 0) {
                $response['message'] = "Catalog name cannot be empty and a section must be selected";
                echo json_encode($response);
                exit;
            }

            $newCatalogName = htmlspecialchars($newCatalogName, ENT_QUOTES, 'UTF-8');


            // Check if the catalog name already exists in this section
            $checkQuery = "SELECT COUNT(*) AS count FROM catalogue WHERE catalogue_name = ? AND section_id = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("si", $newCatalogName, $sectionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                $response['message'] = "Catalogue with this name already exists in this section";
                echo json_encode($response);
                exit;
            } else {
                // Generate new catalog ID
                $idQuery = "SELECT MAX(catalogue_id) AS max_id FROM catalogue";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newCatalogId = $row['max_id'] + 1;

                // Insert new catalog
                $stmt = $mysqli->prepare("INSERT INTO catalogue (catalogue_id, section_id, catalogue_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $newCatalogId, $sectionId, $newCatalogName);

                if ($stmt->execute()) {

                    $response['message'] = "New catalogue added";
                    echo json_encode($response);
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'create_subcatalog':
            $catalogueId = isset($_POST['catalogue_id']) ? (int)$_POST['catalogue_id'] : 0;
            $newSubcatalogName = isset($_POST['new_subcatalog']) ? trim($_POST['new_subcatalog']) : '';

            // Validate input
            if (empty($newSubcatalogName) || $catalogueId <= 0) {
                $response['message'] = "Subcatalog name cannot be empty and a catalog must be selected";
                echo json_encode($response);
                exit;
            }

            $newSubcatalogName = htmlspecialchars($newSubcatalogName, ENT_QUOTES, 'UTF-8');

            // Check if the subcatalog name already exists in this catalog
            $checkQuery = "SELECT COUNT(*) AS count FROM subcatalogue WHERE subcat_name = ? AND catalogue_id = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("si", $newSubcatalogName, $catalogueId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                $response['message'] = "Subcatalog with this name already exists in this catalogue";
                echo json_encode($response);
                exit;
            } else {
                // Generate new subcatalog ID
                $idQuery = "SELECT MAX(subcat_id) AS max_id FROM subcatalogue";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newSubcatalogId = $row['max_id'] + 1;

                // Insert new subcatalog
                $stmt = $mysqli->prepare("INSERT INTO subcatalogue (subcat_id, catalogue_id, subcat_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $newSubcatalogId, $catalogueId, $newSubcatalogName);

                if ($stmt->execute()) {
                    $response['message'] = "New subcatalogue added";
                    echo json_encode($response);
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
            break;
    }
}
$mysqli->close();

?>