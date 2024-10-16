<?php
include 'artifact-db.php';

header('Content-Type: application/json');

// Get the search term from the request
$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

// SQL query to fetch artifact details
$query = "
    SELECT 
        a.artifact_id AS 'ID', 
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

// Close the database connection
$stmt->close();
$mysqli->close();

// Define the path to script.json
$json_file_path = '../assets/VAmodel/script.json';

// Load the scripts from script.json
$scripts = [];
if (file_exists($json_file_path)) {
    $json_data = file_get_contents($json_file_path);
    $scripts = json_decode($json_data, true);
}

// Add script data to the artifact results
foreach ($data as &$artifact) {
    // Get the script for this artifact ID, if available
    $artifact_id = $artifact['ID'];
    if (isset($scripts['scripts'][$artifact_id])) {
        $artifact['Script'] = $scripts['scripts'][$artifact_id]['script'];
    } else {
        $artifact['Script'] = ''; // or null, depending on your preference
    }
}

// Return the data as JSON
echo json_encode($data);
?>
