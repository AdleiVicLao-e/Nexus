<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>About Us</title> 
    <link rel="stylesheet" href="res\css\homepage.css">
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
            <a class="nav-link" href="scanner.php" title="Home">Scanner</a>
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
            <img src="./assets/img/logo.png" alt="Logo">
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
              <li class="nav-item">
                <a class="nav-link" href="feedback.php" title="Feedback">Feedback</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="about.php" title="About">About</a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <!--End: Menu-->
          <div class="clearfix"></div>
        </div>
      </div>

      <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
        <h1>Discover the Igorot Heritage</h1>
        <p>Explore the fascinating history, culture, and traditions of the Igorot people through captivating exhibits and interactive experiences.</p>
        <button onclick="scrollToSection()">Learn More</button>
        </div>
    </section>

    <!-- Main Content -->
    <section id="main-content">
        <div class="container">
            <h2>Welcome to the SLU Museum</h2>
            <p>
                At the SLU Museum of Igorot Cultures and Arts, embark on a captivating journey through the rich heritage of the Igorot people. Our museum is a vibrant hub that showcases the diverse traditions, art forms, and historical narratives defining the Igorot identity.
            </p>
            <img src="/assets/img/img1.png" alt="Description of image 1">
            
            <p>
                Explore our curated exhibits featuring stunning artifacts, traditional clothing, and intricate crafts that reflect the creativity of the Igorot community. Each piece, from ancient tools to contemporary art, tells a story about the daily lives and values of our ancestors.
            </p>
            <img src="/assets/img/img2.png" alt="Description of image 2">
            
            <p>
                Join us for workshops, educational programs, and guided tours led by staff passionate about sharing Igorot culture. Our museum fosters understanding and appreciation of the rich tapestry of Igorot heritage for students, residents, and visitors alike.
            </p>
            <img src="/assets/img/img3.png" alt="Description of image 3">
            <p>
                We look forward to welcoming you to our museum, where each visit inspires curiosity and deepens your understanding of beautiful Igorot traditions. Discover the stories that connect us and celebrate the vibrant culture that continues to thrive today!
            </p>
        </div>
    </section>

    <section id="qr-code-introduction" class="qr-code-intro">
    <div class="container">
        <h2>Discover Artifacts with QR Codes</h2>
        <p>
            Our innovative application allows you to explore the museum's artifacts through QR code scanning, enhancing your experience and understanding of Igorot culture.
        </p>
        <img src="/assets/img/qrScan.gif" alt="QR Code Feature" class="img-fluid" />
        <div class="text-center mt-3">
            <a href="scanner.php" class="btn btn-primary">Scan QR Code</a>
        </div>
    </div>
    </section>

    <section id="b-roll" class="b-roll">
    <div class="container">
        <h2>Watch Igorot Dances</h2>
        <p>Immerse yourself in the vibrant culture of the Igorot people! Experience the rhythm and movements that bring these rich traditions to life.</p>
        <video controls width="100%" poster="assets/img/poster.png"> <!-- Add your thumbnail image here -->
            <source src="assets/img/bRoll.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="text-center mt-3">
            <a href="igorot-dances.php" class="btn btn-primary">Explore Igorot Dances</a>
        </div>
    </div>
</section>

</body>
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
<script>
    function scrollToSection() {
      document.getElementById('main-content').scrollIntoView({ behavior: 'smooth' });
    }
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
    <script type="text/javascript" src="res/js/client/about.js"></script>
</html>

