<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../guest-login.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedback</title>
    <link rel="stylesheet" href="res\css\feedback.css">
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="assets/icofont/icofont.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <div id="app">
      <div id="sidebar_menu_bg"></div>
      <div id="sidebar_menu">
        <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar" style="border-radius: 30px; background: #eee; border-color: #eee;
    color: #111;">
          <i class="fa fa-angle-left mr-2"></i>Close menu </button>
        <ul class="nav sidebar_menu-list">
        <li class="nav-item active">
            <a class="nav-link" href="scanner.php" title="Home">Home</a>
          </li>
          <li class="nav-item">
          <a class="nav-link" href="igorot-dances.php" title="Igorot Dances">Igorot Dances</a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="feedback.php" title="Feedback">Feedback</a>
         </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php" title="About">About</a>
          </li>
          <div class="clearfix"></div>
      </div>
      </li>
      </ul>
      <div class="clearfix"></div>
    </div>
    <div id="wrapper">
      <div id="header">
        <div class="container">
          <div id="mobile_menu">
            <i class="fa fa-bars"></i>
          </div>
          <a href="scanner.php" id="logo">
            <img src="assets\img\logo.png" alt="Logo">
          </a>
          <!--Begin: Menu-->
          <div id="header_menu">
            <ul class="nav header_menu-list">
              <li class="nav-item">
                <a href="scanner.php" title="Home">Home</a>
              </li>
              <li class="nav-item">
              <a href="igorot-dances.php" title="Igorot Dances">Igorot Dances</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="feedback.php" title="Feedback">Feedback</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php" title="About">About</a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <!--End: Menu-->
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="fb-form-container">
        <div class="fb"> <!-- Remove the dot from the class name -->
          <form class="fb-form"> <!-- Remove the dot from the class name -->
            <h2>Feedback Form</h2>
            <label>How do you rate your overall experience?</label>
            <div class="mb-3 d-flex flex-row py-1">
              <div class="form-check mr-3">
                <input class="form-check-input" type="radio" name="rating" id="rating_bad" value="bad">
                <label class="form-check-label" for="rating_bad">
                  Bad
                </label>
              </div>
              
              <div class="form-check mx-3">
                <input class="form-check-input" type="radio" name="rating" id="rating_good" value="good">
                <label class="form-check-label" for="rating_good">
                  Good
                </label>
              </div>
              
              <div class="form-check mx-3">
                <input class="form-check-input" type="radio" name="rating" id="rating_excellent" value="excellent">
                <label class="form-check-label" for="rating_excellent">
                  Excellent!
                </label>
              </div>
            </div>
            
            <div class="mb-4 small">
              <label>Please provide your feedback and suggestions in the form below:</label>
              <textarea id="message" name="message" rows="4" required></textarea> 
            </div>
            <input class="btn-red" type="submit" value="Submit">
          </form>
        </div>
      </div>
      
      
      </div>
      <div id="footer">
        <div class="container">
          <div class="footer-logo-block">
            <a href="https://www.facebook.com/slumuseum" class="footer-logo">
              <img src="assets\img\logo.png" alt="Logo footer">
            </a>
            <p class="copyright" style="font-size: 15px">© 2024 SLU Museum of Igorot Cultures and Arts. All Rights Reserved.</p>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
    <script type="text/javascript" src="res/js/client/about.js"></script>
  </body>
</html>