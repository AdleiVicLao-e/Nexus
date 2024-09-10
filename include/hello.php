<?php
include 'artifact-db.php';

header('Content-Type: application/json');

// Get the search term from the request
$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

$query = "
    SELECT 
        a.name AS 'Name',
        s.section_name AS 'Section Name',
        c.catalogue_name AS 'Catalogue Name',
        sc.subcat_name AS 'Subcatalogue Name',
        a.description AS 'Description'
    FROM 
        artifact_info a
    LEFT JOIN 
        section s ON a.section_id = s.section_id
    LEFT JOIN 
        catalogue c ON a.catalogue_id = c.catalogue_id
    LEFT JOIN 
        subcatalogue sc ON a.subcat_id = sc.subcat_id
    WHERE 
        a.name LIKE CONCAT('%', ?, '%') OR
        s.section_name LIKE CONCAT('%', ?, '%') OR
        c.catalogue_name LIKE CONCAT('%', ?, '%') OR
        sc.subcat_name LIKE CONCAT('%', ?, '%')
";

// Prepare and execute the SQL statement
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all results as an associative array
$data = $result->fetch_all(MYSQLI_ASSOC);

// Close connection
$stmt->close();
$mysqli->close();

// Return the data as JSON
echo json_encode($data);
?>