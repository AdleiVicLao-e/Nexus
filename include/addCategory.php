<?php

include 'artifact-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'create_section':
            $newSectionTitle = isset($_POST['new_section']) ? trim($_POST['new_section']) : '';

            // Check if the section name already exists
            $checkQuery = "SELECT COUNT(*) AS count FROM section WHERE section_name = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("s", $newSectionTitle);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                echo '<script>
                    alert("Section with this name already exists");
                    window.location.href="../admin/admin.php";
                </script>';
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
                    echo '<script>
                        alert("New section added");
                        window.location.href="../admin/admin.php";
                    </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
            break;

        case 'create_catalog':
            $sectionId = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
            $newCatalogName = isset($_POST['new_catalog']) ? trim($_POST['new_catalog']) : '';

            // Check if the catalog name already exists in this section
            $checkQuery = "SELECT COUNT(*) AS count FROM catalogue WHERE catalogue_name = ? AND section_id = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("si", $newCatalogName, $sectionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                echo '<script>
                    alert("Catalogue with this name already exists in this section");
                    window.location.href="../admin/admin.php";
                </script>';
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
                    echo '<script>
                        alert("New catalogue added");
                        window.location.href="../admin/admin.php";
                    </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'create_subcatalog':
            $catalogueId = isset($_POST['catalogue_id']) ? (int)$_POST['catalogue_id'] : 0;
            $newSubcatalogName = isset($_POST['new_subcatalog']) ? trim($_POST['new_subcatalog']) : '';

            // Check if the subcatalog name already exists in this catalog
            $checkQuery = "SELECT COUNT(*) AS count FROM subcatalogue WHERE subcat_name = ? AND catalogue_id = ?";
            $stmt = $mysqli->prepare($checkQuery);
            $stmt->bind_param("si", $newSubcatalogName, $catalogueId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                echo '<script>
                    alert("Subcatalogue with this name already exists in this catalogue");
                    window.location.href="../admin/admin.php";
                </script>';
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
                    echo '<script>
                        alert("New subcatalogue added");
                        window.location.href="../admin/admin.php";
                    </script>';
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