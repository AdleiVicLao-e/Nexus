<?php
session_start();
if (isset($_SESSION["guest"])) {
  header("Location: ../scanner.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SLU Museum</title>
  <link rel="stylesheet" href="res/css/styles.css">
  <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
  <link
  href="https://fonts.googleapis.com/css?family=Inter"
  rel="stylesheet"
/>
</head>
<body class="welcome-body">
<header class="banner">
  <div class="logo-container">
    <img src="assets/img/logo.png" alt="Logo" class="logo">
  </div>
</header>
<div class="welcome-container">
  <a href="guest-login.php" class="btn-red">Get Started</a>
</div>
</body>
</html>
