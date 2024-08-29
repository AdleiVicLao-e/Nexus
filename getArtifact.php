<?php
header('Content-Type: application/json');

// Database connection parameters
$host = 'localhost'; // Update with your WAMP server hostname
$dbname = 'kultoura';
$username = 'root'; // Update with your MySQL username
$password = ''; // Update with your MySQL password

// Create connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

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

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $artifactId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the result
$data = $result->fetch_assoc();

// Close connection
$stmt->close();
$mysqli->close();

// Return the data as JSON
echo json_encode($data);
?>