<?php
include 'admin-db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION["admin"] = $_POST['admin_username'];
        header("Location: ../admin/admin.php");
        exit();
    } else {
        header("Location: ../admin/403.php");
    }
    $stmt->close();
    $conn->close();
}
?>