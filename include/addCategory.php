<?php

include 'artifact-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'create_section':
            $newSectionTitle = isset($_POST['new_section']) ? trim($_POST['new_section']) : '';

                $idQuery = "SELECT MAX(section_id) AS max_id FROM section";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newSectionId = $row['max_id'] + 1;

                $stmt = $mysqli->prepare("INSERT INTO section (section_id, section_name) VALUES (?, ?)");
                $stmt->bind_param("is", $newSectionId, $newSectionTitle);

                if ($stmt->execute()) {
                    echo '<script>
            window.location.href="../admin/admin.php";
            alert("New section added");
            </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            break;

        case 'create_catalog':
            $sectionId = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
            $newCatalogName = isset($_POST['new_catalog']) ? trim($_POST['new_catalog']) : '';

                $idQuery = "SELECT MAX(catalogue_id) AS max_id FROM catalogue";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newCatalogId = $row['max_id'] + 1;

                $stmt = $mysqli->prepare("INSERT INTO catalogue (catalogue_id, section_id, catalogue_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $newCatalogId, $sectionId, $newCatalogName);

                if ($stmt->execute()) {
                    echo '<script>
            window.location.href="../admin/admin.php";
            alert("New catalogue added");
            </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            break;

        case 'create_subcatalog':
            $catalogueId = isset($_POST['catalogue_id']) ? (int)$_POST['catalogue_id'] : 0;
            $newSubcatalogName = isset($_POST['new_subcatalog']) ? trim($_POST['new_subcatalog']) : '';

                $idQuery = "SELECT MAX(subcat_id) AS max_id FROM subcatalogue";
                $idResult = $mysqli->query($idQuery);
                $row = $idResult->fetch_assoc();
                $newSubcatalogId = $row['max_id'] + 1;

                $stmt = $mysqli->prepare("INSERT INTO subcatalogue (subcat_id, catalogue_id, subcat_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $newSubcatalogId, $catalogueId, $newSubcatalogName);

                if ($stmt->execute()) {
                    echo '<script>
            window.location.href="../admin/admin.php";
            alert("New subcatalogue added");
            </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            break;
    }
}

$mysqli->close();

?>