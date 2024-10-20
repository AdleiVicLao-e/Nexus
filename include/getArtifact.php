<?php
global $mysqli;
include 'artifact-db.php';

header('Content-Type: application/json');

// Get the artifactId from the request
$artifactId = isset($_GET['artifact_id']) ? intval($_GET['artifact_id']) : 0;

// Prepare and execute the query
$query = "
    SELECT 
        a.artifact_id AS 'Artifact Id',
        s.section_name AS 'Section Name',
        c.catalogue_name AS 'Catalogue Name',
        sc.subcat_name AS 'Subcatalogue Name',
        a.name AS 'Name',
        a.description AS 'Description',
        a.condition AS 'Condition'
    FROM 
        artifact_info a
    LEFT JOIN 
        section s ON a.section_id = s.section_id
    LEFT JOIN 
        catalogue c ON a.catalogue_id = c.catalogue_id
    LEFT JOIN 
        subcatalogue sc ON a.subcat_id = sc.subcat_id
    WHERE 
        a.artifact_id = ?;";

// Prepare the statement
if ($stmt = $mysqli->prepare($query)) {
    // Bind parameters
    $stmt->bind_param('i', $artifactId);

    // Execute the statement
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Check if any data is returned
    if ($data = $result->fetch_assoc()) {
        // If data is found, return it as JSON
        echo json_encode($data);
    } else {
        // If no data found, return an empty response
        echo json_encode([
            'error' => true,
            'message' => 'No artifact found for this ID'
        ]);
    }

    // Close statement and connection
    $stmt->close();
} else {
    // If query preparation fails, return an error response
    echo json_encode([
        'error' => true,
        'message' => 'Failed to prepare the query'
    ]);
}

$mysqli->close();
?>
