<?php
session_start();
if (isset($_SESSION["admin"])) {
    header("Location: ../admin/admin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QR Code Generator</title>
  <link rel="icon" href="assets/img/favicon.png" type="image/x-icon" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Jomolhari&display=swap" rel="stylesheet"> <!-- Add Jomolhari font -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif; /* Set Inter as the default font */
      text-align: center;
      padding: 20px;
      background-image: url("../assets/img/homepage_background.png");
      background-size: cover;
      background-repeat: repeat;
      background-attachment: fixed;
    }

    h1 {
      font-family: 'Jomolhari', serif; /* Set Jomolhari for the h1 element */
      margin-top: 50px;
      margin-bottom: -50px;
      font-size: 190px;
    }


    button {
      height: 70px;
      background-color: #0E1644;
      color: #FFFFFF;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      border-radius: 20px;
      font-size: 26px;
      transition: background-color 0.3s ease-in-out;
    }

    button:hover {
      background-color: #FAC301;
      color: #0E1644;
      font-weight: bold;
      border: 1.25px solid #0E1644;
    }

    .download-btn {
      display: none;
      background-color: #0E1644;
      color: #FFFFFF;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      border-radius: 20px;
      font-size: 16px;
      text-decoration: none;
      transition: background-color 0.3s ease-in-out;
      margin-top: 20px;
      width: 180px;
    }

    .download-btn:hover {
      background-color: #FAC301;
      color: #0E1644;
      font-weight: bold;
      border: 1.25px solid #0E1644;
    }

    .button-container {
      display: flex;
      justify-content: center; 
      margin-top: 20px;
    }

  </style>
</head>
<body>
  <h1>401</h1>
  <h2>Invalid Credential.</h2>
  <div>
    <button onclick="window.location.href='../admin/admin-login.php'">Login Again</button>
  </div>
</body>
</html>
