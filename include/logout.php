<?php
include 'admin-db.php';
session_start();
if (isset($_SESSION["admin"])) {
    $username = $_SESSION["admin"];
    // Make user offline
    $updateStatusStmt = "UPDATE credentials SET status=0 WHERE username='$username'";
    mysqli_query($conn, $updateStatusStmt);

    session_unset();
    session_destroy();

    echo '<script>
    localStorage.clear();
    // Redirect to login page
    window.location.href="../admin/admin-login.php";
    </script>';
}
exit();
?>