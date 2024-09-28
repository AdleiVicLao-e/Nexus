<?php
include 'admin-db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../admin/admin.php");
        exit();
    } else {
        echo "Login Not Successful. Please check your credentials.";
    }
    $stmt->close();
    $conn->close();
}
?>