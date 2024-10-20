<?php
global $mysqli;
include 'artifact-db.php';

header('Content-Type: application/json');

// Get the search term from the request
$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

// Add wildcards for the LIKE query
$searchTermWithWildcards = '%' . $searchTerm . '%';

// SQL query to fetch artifact details
$query = "
    SELECT 
        a.artifact_id AS 'ID', 
        a.name AS 'Name',
        COALESCE(s.section_name, 'N/A') AS 'Section Name',
        COALESCE(c.catalogue_name, 'N/A') AS 'Catalogue Name',
        COALESCE(sc.subcat_name, 'N/A') AS 'Subcatalogue Name',
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
if ($stmt = $mysqli->prepare($query)) {
    // Bind parameters (all of them use the same search term)
    $stmt->bind_param('ssss', $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the result set from the query
        $result = $stmt->get_result();

        // Fetch all results as an associative array
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Close the statement
        $stmt->close();
    } else {
        // Handle query execution error
        echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
        exit;
    }
} else {
    // Handle query preparation error
    echo json_encode(['error' => 'Query preparation failed: ' . $mysqli->error]);
    exit;
}

// Close the database connection
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
