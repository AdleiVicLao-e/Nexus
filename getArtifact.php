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
        artifactinfo.artifact_id, 
        artifactinfo.name, 
        artifactinfo.description, 
        artifactinfo.condition, 
        catalogue.catalogue_Name AS catalogue_name, 
        section.section_Name AS section_name
    FROM 
        artifactinfo
    JOIN 
        catalogue ON artifactinfo.catalogue_id = catalogue.catalogue_ID
    JOIN 
        section ON artifactinfo.section_id = section.section_ID
    WHERE 
        artifactinfo.artifact_id = ?";

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