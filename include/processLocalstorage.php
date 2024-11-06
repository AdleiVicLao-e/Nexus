<?php
session_start();

$input = file_get_contents('php://input');

$data = json_decode($input, true);

if (isset($data['admin'])) {
    $_SESSION['admin'] = $data['admin'];

    echo json_encode([
        'status' => 'success',
        'message' => 'Data saved to session',
        'admin' => $_SESSION['admin']
    ]);
}
