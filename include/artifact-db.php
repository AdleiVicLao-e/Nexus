<?php
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

?>