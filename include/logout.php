<?php
include 'admin-db.php';
session_start();
if (isset($_SESSION["admin"])) {
    $username = $_SESSION["admin"];
    // Make user offline
    $updateStatusStmt = "UPDATE credentials SET status=0 WHERE username='$username'";
    mysqli_query($conn, $updateStatusStmt);

    echo '<script>
    window.location.href="../admin/admin-login.php";
    </script>';
} else if (isset($_SESSION["guest"])) {
    echo '<script>
    window.location.href="../index.php";
    </script>';
}
session_destroy();
?>