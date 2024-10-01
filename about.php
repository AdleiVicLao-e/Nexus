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
    <title>About Us</title>
    <link rel="stylesheet" href="res\css\aboutStyle.css">
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
      <!--Begin: Main-->
      <div id="main-wrapper">
        <div>
          <!-- ======= Hero Section ======= -->
          <section id="hero" class="d-flex align-items-center">
            <div class="container">
              <div class="row">
                <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1">
                  <h1>Discover Igorot Heritage with the SLU Museum WebApp</h1>
                  <h2> Welcome to the <strong>
                      <a href="https://www.slu.edu.ph/museum-of-igorot-culture-and-arts/" class="museum-link"> SLU Museum of Igorot Cultures and Arts </a>
                    </strong>! Our web app enhances your visit with a digital artifact guide and informative content about our unique artifacts. </h2>
                  <a href="#about" class="btn-red">Get Started</a>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 hero-img">
                  <img src="assets/img/Museum-Photography.svg" class="img-fluid animated" alt="">
                </div>
              </div>
            </div>
          </section>
          <!-- End Hero -->
          <main id="main">
            <!-- ======= About Section ======= -->
            <section id="about" class="about">
              <div class="container">
                <div class="row justify-content-between">
                  <div class="col-lg-5 d-flex align-items-center justify-content-center about-img">
                    <img src="assets/img/collab.png" class="img-fluid" alt="" data-aos="zoom-in">
                  </div>
                  <div class="col-lg-6 pt-5 pt-lg-0">
                    <h3 data-aos="fade-up">A Collaborative Effort</h3>
                    <p data-aos="fade-up" data-aos-delay="100"> The <strong>
                        <a href="https://www.slu.edu.ph/museum-of-igorot-culture-and-arts/" class="museum-link"> SLU Museum of Igorot Cultures and Arts </a>
                      </strong> proudly presents this web application, a collaboration between <strong>Team Nexus</strong>, <strong>4th-year IT students</strong> from the <strong>School of Accountancy, Management, Computing, and Information Studies</strong>, and the museum. This project merges technology with cultural heritage, enhancing the visitor experience and promoting the rich legacy of the Igorot people. </p>
                    <div class="row">
                      <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <i class="bx bx-book-open"></i>
                        <h4>A Commitment to Cultural Heritage</h4>
                        <p>This partnership reflects our dedication to preserving and sharing the unique heritage of the Igorot people, ensuring that their stories and artifacts are accessible to everyone.</p>
                      </div>
                      <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <i class="bx bx-link"></i>
                        <h4>Bridging Technology and Tradition</h4>
                        <p>By integrating innovative technology into the museum experience, we aim to educate and engage visitors in a meaningful way, fostering a deeper appreciation for Igorot culture. </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <!-- End About Section -->
            <!-- ======= Services Section ======= -->
            <section id="services" class="services section-bg">
              <div class="container">
                <div class="section-title" data-aos="fade-up">
                  <h2>Key Features</h2>
                  <p>Discover features designed to enhance your museum visit</p>
                </div>
                <div class="row">
                  <!-- First row with 3 columns -->
                  <div class="col-md-6 col-lg-4 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-box">
                      <div class="icon">
                        <i class="bx bx-scan"></i>
                      </div>
                      <h4 class="title">
                        <a href="">QR Code Scanning</a>
                      </h4>
                      <p class="description">Easily scan QR codes placed alongside artifacts to instantly access detailed information about each piece.</p>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-4 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-box">
                      <div class="icon">
                        <i class="bx bx-book"></i>
                      </div>
                      <h4 class="title">
                        <a href="">Digital Artifact Guide</a>
                      </h4>
                      <p class="description">Discover the history, significance, and cultural background of the displayed items through comprehensive text descriptions.</p>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-4 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-box">
                      <div class="icon">
                        <i class="bx bx-intersect"></i>
                      </div>
                      <h4 class="title">
                        <a href="">Interactive Museum Experience</a>
                      </h4>
                      <p class="description">Explore the museum at your own pace with an interactive guide that enhances your learning experience.</p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <!-- Second row with 2 columns -->
                  <div class="col-md-6 col-lg-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="400">
                    <div class="icon-box">
                      <div class="icon">
                        <i class="bx bx-mobile-alt"></i>
                      </div>
                      <h4 class="title">
                        <a href="">Mobile-Friendly Design</a>
                      </h4>
                      <p class="description">Navigate and interact with the museum’s resources smoothly on any mobile device.</p>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="500">
                    <div class="icon-box">
                      <div class="icon">
                        <i class="bx bx-brain"></i>
                      </div>
                      <h4 class="title">
                        <a href="">Enhanced Learning Experience</a>
                      </h4>
                      <p class="description">Dive deeper into Igorot heritage with educational content designed to enrich your visit.</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <!-- End Services Section -->
            <!-- ======= Portfolio Section ======= -->
            <section id="portfolio" class="portfolio">
              <div class="container">
                <div class="section-title" data-aos="fade-up">
                  <h2>Portfolio</h2>
                  <p>What what whattt</p>
                </div>
                <div class="row" data-aos="fade-up" data-aos-delay="100">
                  <div class="col-lg-12">
                    <ul id="portfolio-flters">
                      <li data-filter="*" class="filter-active">All</li>
                      <li data-filter=".filter-app">App</li>
                      <li data-filter=".filter-card">Card</li>
                      <li data-filter=".filter-web">Web</li>
                    </ul>
                  </div>
                </div>
                <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
                  <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-1.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-1.jpg" data-gall="portfolioGallery" class="venobox" title="App 1">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>App 1</h4>
                        <p>App</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-2.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-2.jpg" data-gall="portfolioGallery" class="venobox" title="Web 3">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Web 3</h4>
                        <p>Web</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-3.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-3.jpg" data-gall="portfolioGallery" class="venobox" title="App 2">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>App 2</h4>
                        <p>App</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-4.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-4.jpg" data-gall="portfolioGallery" class="venobox" title="Card 2">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Card 2</h4>
                        <p>Card</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-5.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-5.jpg" data-gall="portfolioGallery" class="venobox" title="Web 2">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Web 2</h4>
                        <p>Web</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-6.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-6.jpg" data-gall="portfolioGallery" class="venobox" title="App 3">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>App 3</h4>
                        <p>App</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-7.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-7.jpg" data-gall="portfolioGallery" class="venobox" title="Card 1">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Card 1</h4>
                        <p>Card</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-8.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-8.jpg" data-gall="portfolioGallery" class="venobox" title="Card 3">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Card 3</h4>
                        <p>Card</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                    <div class="portfolio-wrap">
                      <img src="assets/img/portfolio/portfolio-9.jpg" class="img-fluid" alt="">
                      <div class="portfolio-links">
                        <a href="assets/img/portfolio/portfolio-9.jpg" data-gall="portfolioGallery" class="venobox" title="Web 3">
                          <i class="icofont-plus-circle"></i>
                        </a>
                        <a href="#" title="More Details">
                          <i class="icofont-link"></i>
                        </a>
                      </div>
                      <div class="portfolio-info">
                        <h4>Web 3</h4>
                        <p>Web</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <!-- End Portfolio Section -->
            <!-- ======= Team Section ======= -->
            <section id="team" class="team">
              <div class="container">
                <div class="section-title" data-aos="fade-up">
                  <h2>Team</h2>
                  <p>Meet the Innovators</p>
                </div>
                <div class="row">
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                    <div class="member">
                      <img src="assets\img\team\trisha.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Trisha Denise Garas</h4>
                          <span>Project Lead</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="member">
                      <img src="assets\img\team\adlei.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Adlei Vic Lao-e</h4>
                          <span>Lead Developer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                    <div class="member">
                      <img src="assets\img\team\pola.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Paula Britanny Laban</h4>
                          <span>Full-stack Developer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                    <div class="member">
                      <img src="assets\img\team\kenshin.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Ram Kenshin Ayan</h4>
                          <span>Back-end Developer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                    <div class="member">
                      <img src="assets\img\team\ben.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Jieben Kayla Abaya</h4>
                          <span>Front-end Developer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="member">
                      <img src="assets\img\team\valiant.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Valiant Mi-ing</h4>
                          <span>System Architect</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                    <div class="member">
                      <img src="assets\img\team\gail.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Andrei Gail Lumbas</h4>
                          <span>Quality Assurance Engineer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                    <div class="member">
                      <img src="assets\img\team\lindolf.png" class="img-fluid" alt="">
                      <div class="member-info">
                        <div class="member-info-content">
                          <h4>Lindolf Bert Agustin</h4>
                          <span>Quality Assurance Engineer</span>
                        </div>
                        <div class="social">
                          <a href="">
                            <i class="icofont-facebook"></i>
                          </a>
                          <a href="">
                            <i class="icofont-email"></i>
                          </a>
                          <a href="">
                            <i class="icofont-github"></i>
                          </a>
                          <a href="">
                            <i class="icofont-linkedin"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <!-- End Team Section -->
          </main>
          <!-- End #main -->
        </div>
        <!--End: Main-->
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