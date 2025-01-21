<?php
session_start();
if (isset($_SESSION["admin"])) {
  echo '<script>
    alert("Already logged in. Redirected to Admin Page.");
    window.location.href="admin.php";
    </script>';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Kultoura</title>
    <link rel="stylesheet" href="../res/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="../assets/img/favicon.png" rel="icon">
  </head>
  <body class="login-body">
    <div class="container">
      <div class="logo-container">
        <img src="../assets/img/logo.png" alt="Logo" class="logo">
      </div>
      <div class="login-container">
        <h1>Log In</h1>
        <form action="../include/admin-login.php" method="post">
          <div class="input-container">
            <p style="text-align: left; font-weight: lighter">Admin ID</p>
            <input type="text" class="input-field" placeholder="Enter Admin ID" name="admin_username">
            <i class="fa fa-user" id="fa icon"></i>
          </div>
          <div class="input-container">
            <p style="text-align: left; font-weight: lighter">Password</p>
            <input type="password" class="input-field" placeholder="Enter Password" id="password-field" name="admin_password">
            <i class="fa fa-lock icon-left" id="fa icon"></i>
            <i class="fa fa-eye toggle-password icon-right" id="toggle-password"></i>
          </div>
          <div id="error-message" style="color: red; display: none; margin-top: 10px;"> Please fill out both fields. </div>
          <button type="submit" class="btn-login">Login</button>
        </form>
      </div>
    </div>
    <script src="../res/js/scripts.js"></script>
    <!-- Overlay Message -->
    <?php
    if (isset($_SESSION['login_error'])):
        $message = $_SESSION['login_error'];
        unset($_SESSION['login_error']);
        ?>
        <div id="messageOverlay" class="overlay" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
            <div class="overlay-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <p id="overlayMessage"><?php echo $message; ?></p>
                <button onclick="closeOverlay()" style="margin-top: 10px;">Okay</button>
            </div>
        </div>
    <?php endif; ?>
  </body>

  <script>
      window.history.replaceState(null, null, window.location.href);

      function checkDevice() {
        // Define a breakpoint for mobile devices (e.g., 768px)
        if (window.innerWidth < 768) {
            // Redirect to the mobile page
            window.location.href = "mobile.html";
        }}

        checkDevice();

      function closeOverlay() {
          document.getElementById('messageOverlay').style.display = 'none';
      }
  </script>
</html>