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
    $resultRow = $result->fetch_assoc();

    // If account exists
    if ($result->num_rows > 0) {
        // Check status
        if ($resultRow["status"] == 0) {
            // Make user online
            $updateStatusStmt = "UPDATE credentials SET status=1 WHERE username='$username'";
            mysqli_query($conn, $updateStatusStmt);
            // Redirect user
            $_SESSION["admin"] = $_POST['admin_username'];
            header("Location: ../admin/admin.php");
            exit();
        } else {
            echo '<script>
                alert("User is logged in another session.");
                window.location.href="../admin/admin-login.php";
                </script>';
        }
    } else {
        echo '<script>
                alert("Invalid credentials.");
                window.location.href="../admin/admin-login.php";
                </script>';
    }
    
    $stmt->close();
    $conn->close();
}