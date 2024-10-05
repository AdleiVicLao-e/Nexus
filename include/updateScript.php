<?php
// updateScript.php

session_start();

// Set header to return JSON response
header('Content-Type: application/json');

// Check if the user is an admin
if (is_null($_SESSION["admin"])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

// Retrieve POST data
// Depending on Content-Type, data might come from $_POST or from raw input
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    // If JSON payload
    $input = json_decode(file_get_contents('php://input'), true);
    $artifact_id = isset($input['artifact_id']) ? trim($input['artifact_id']) : null;
    $artifact_name = isset($input['artifact_name']) ? trim($input['artifact_name']) : null;
    $script = isset($input['script']) ? trim($input['script']) : null;
} else {
    // If form-urlencoded or multipart/form-data
    $artifact_id = isset($_POST['artifact_id']) ? trim($_POST['artifact_id']) : null;
    $artifact_name = isset($_POST['artifact_name']) ? trim($_POST['artifact_name']) : null;
    $script = isset($_POST['script']) ? trim($_POST['script']) : null;
}

// Validate input
if (empty($artifact_id) || empty($artifact_name) || empty($script)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields."]);
    exit();
}

// Define the path to script.json
$json_file_path = '../assets/VAmodel/script.json';

// Check if script.json exists; if not, create it with an empty structure
if (!file_exists($json_file_path)) {
    $initial_data = ["scripts" => []];
    if (file_put_contents($json_file_path, json_encode($initial_data, JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to create script.json."]);
        exit();
    }
}

// Read the existing JSON data
$json_data = file_get_contents($json_file_path);
$data = json_decode($json_data, true);

// Handle JSON decoding errors
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to decode JSON data."]);
    exit();
}

// Update or add the script for the given artifact_id
$data['scripts'][$artifact_id] = [
    "artifact_name" => $artifact_name,
    "script" => $script
];

// Encode the updated data back to JSON
$new_json_data = json_encode($data, JSON_PRETTY_PRINT);

// Attempt to write the updated JSON back to the file
if (file_put_contents($json_file_path, $new_json_data) !== false) {
    echo json_encode(["success" => true, "message" => "Script updated successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to write to script.json."]);
}
?>
