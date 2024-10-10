<?php
session_start();
if (isset($_SESSION["admin"])) {
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