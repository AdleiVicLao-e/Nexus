<?php
session_start();
if (isset($_SESSION["admin"])) {
    header("Location: ../admin/admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 Unauthorized</title>
  <link rel="icon" href="../assets/img/favicon.png" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Jomolhari&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      text-align: center;
      padding: 20px;
      background-image: url("../assets/img/homepage_background.png");
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      color: #000000;
    }

    h1 {
      font-family: 'Jomolhari', serif;
      font-size: 180px;
      margin: 50px 0 -50px;
      color: #000;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 30px;
    }

    button {
      height: 60px;
      width: 200px;
      background-color: #0E1644;
      color: #FFFFFF;
      border: none;
      border-radius: 20px;
      padding: 12px 20px;
      cursor: pointer;
      font-size: 20px;
      font-weight: bold;
      transition: background-color 0.3s ease-in-out;
    }

    button:hover {
      background-color: #FAC301;
      color: #0E1644;
      border: 1.5px solid #0E1644;
    }

    .button-container {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }

  </style>
</head>
<body>
  <h1>403</h1>
  <h2>Access Denied. Invalid Credentials.</h2>

  <div class="button-container">
    <button onclick="window.location.href='../admin/admin-login.php'">Login Again</button>
  </div>
</body>
</html>
