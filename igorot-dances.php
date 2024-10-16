<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../index.php");
}
?>
<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Igorot Dances</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="en" />
    <meta name="description" content="Watch Igorot Dances" />
    <meta name="keywords" content="watch igorot dances ifugao bontoc kalinga kankanaey isneg ibaloi abra cordillera" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <link rel="icon" href="assets\img\favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
    <link rel="stylesheet" href="res\css\dances.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
  </head>
  <body>
    <div id="app">
      <div id="sidebar_menu_bg"></div>
      <div id="sidebar_menu">
        <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar">
          <i class="fa fa-angle-left mr-2"></i>Close menu </button>
        <ul class="nav sidebar_menu-list">
          <li class="nav-item active">
            <a class="nav-link" href="homepage.php" title="Home">Home</a>
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
              <li class="nav-item active">
              <a href="igorot-dances.php" title="Home">Igorot Dances</a>
              </li>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="feedback.php" title="Home">Feedback</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php" title="Home">About</a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <!--End: Menu-->
          <div id="header_right">
            <div id="search">
              <div class="search-content">
                <form @submit="search">
                  <div class="search-icon">
                    <i class="fa fa-search"></i>
                  </div>
                  <input v-model="keyword" type="text" class="form-control search-input" autocomplete="off" name="keyword" placeholder="Enter keywords...">
                </form>
                <div class="nav search-result-pop search-suggest"></div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <!--Begin: Main-->
      <div id="main-wrapper">
        <!--Begin: Detail-->
        <div class="detail_page detail_page-style">
          <div class="cover_follow" style="background-image: url(https://www.slu.edu.ph/wp-content/uploads/2022/12/DSC0097-1-scaled-e1670400321462-2540x1270.jpg);"></div>
          <div class="container">
            <div class="prebreadcrumb"></div>
            <div style="text-align:center;margin-bottom: 20px;margin-top:20px;display:none;" id="hgiks-top"></div>
            <!--Begin: Watch-->
            <div class="detail_page-watch" data-id="112234" data-type="1">
              <div class="dp-w-cover">
                <a href="#" class="dp-w-c-play">
                  <i class="fa fa-play"></i>
                </a>
              </div>
              <div class="detail_page-infor">
                <div class="dp-i-content">
                  <div class="dp-i-c-poster">
                    <div class="film-poster mb-2">
                      <img class="film-poster-img" src="./assets/img/igorot-dance.png" title="igorot-dance" alt="watch-igorot-dance">
                    </div>
                    <div class="block-rating" id="block-rating"></div>
                  </div>
                  <div class="dp-i-c-right">
                  <div class="dp-i-c-stick">
                    <a href="#" title="Watch preview" id="preview" class="btn btn-radius btn-focus">
                      <i class="fa fa-play mr-2"></i>Watch preview
                    </a>
                  </div>
                  <!-- Video element (initially hidden) -->
                  <div id="video-container" style="display: none; text-align: center; margin-top: 20px;">
                    <video id="video-player" width="600" controls>
                      <source id="video-source" src="" type="video/mp4">
                      Your browser does not support the video tag.
                    </video>
                  </div>
                    <h2 class="heading-name">
                      <a href="#">Igorot Dances</a>
                    </h2>
                    <div class="dp-i-stats">
                      <span class="item mr-1">
                        <a href="#more" class="btn btn-sm btn-trailer" title="Explore More"> Explore More </a>
                      </span>
                      <span class="item mr-1">
                        <button class="btn btn-sm btn-quality">
                          <strong>HD</strong>
                        </button>
                      </span>
                    </div>
                    <div class="description"> Experience the vibrant culture of the Igorot people through their traditional dances. Each movement tells a story—of celebration, unity, and deep connection to the land. Discover the rich heritage of the highlands through these sacred and timeless performances. </div>
                    <div class="elements">
                      <div class="row">
                        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-12">
                          <div class="row-line">
                            <span class="type">
                              <strong>Released: </strong>
                            </span> 2024-07-31
                          </div>
                          <div class="row-line">
                            <span class="type">
                              <strong>Performed by: </strong>
                            </span>
                            <a href="" title="SLU Center of Culture and the Arts">SLU Center of Culture and the Arts</a>
                          </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12">
                          <div class="row-line">
                            <span class="type">
                              <strong>Runtime:</strong>
                            </span> 2-minute preview
                          </div>
                          <div class="row-line">
                            <span class="type">
                              <strong>Curated by:</strong>
                            </span>
                            <a href="https://www.facebook.com/slumuseum" title="SLU Museum of Igorot Cultures and Arts">SLU Museum of Igorot Cultures and Arts</a>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--End: Detail-->
        <!--Begin: Related-->
        <div class="film_related file_realted-list">
          <div class="container">
            <!--Begin: Section film list-->
            <section class="block_area block_area_category">
              <div class="block_area-header">
                <div class="float-left bah-heading mr-4">
                  <h2 class="cat-heading" id="more">Dance Videos</h2>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="block_area-content block_area-list film_list film_list-grid">
                <div class="film_list-wrap">
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Taddok" alt="watch-Taddok">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Taddok">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Taddok">
                          <strong>Taddok</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Community Dance</span>
                        <span class="float-right fdi-type">Kalinga</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Tupayya" alt="watch-Tupayya">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Tupayya">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Tupayya">
                          <strong>Tupayya</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Pair</span>
                        <span class="float-right fdi-type">Kalinga</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Lablabbaan" alt="watch-Lablabbaan">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Lablabbaan">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Lablabbaan">
                          <strong>Lablabbaan</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Pair</span>
                        <span class="float-right fdi-type">Abra</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Boogie" alt="watch-Boogie">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Boogie">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Boogie">
                          <strong>Boogie</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Courtship Dance</span>
                        <span class="float-right fdi-type">Bontoc</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Boogie Variance" alt="watch-Boogie Variance">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Boogie Variance">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Boogie Variance">
                          <strong>Boogie Variance</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">War Dance</span>
                        <span class="float-right fdi-type">Kankanaey</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Takkik" alt="watch-Takkik">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Takkik">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Takkik">
                          <strong>Takkik</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">War Dance</span>
                        <span class="float-right fdi-type">Kankanaey</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Dinuyya" alt="watch-Dinuyya">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Dinuyya">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Dinuyya">
                          <strong>Dinuyya</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Community Dance</span>
                        <span class="float-right fdi-type">Ifugao</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="flw-item">
                    <div class="film-poster">
                      <div class="pick film-poster-quality">HD</div>
                      <img data-src="assets\img\dance-igorot.jpg" class="film-poster-img lazyload" title="Balliwes" alt="watch-Balliwes">
                      <a href="video-player.php" class="film-poster-ahref flw-item-tip" title="Balliwes">
                        <i class="fa fa-play"></i>
                      </a>
                    </div>
                    <div class="film-detail film-detail-fix">
                      <h3 class="film-name">
                        <a href="video-player.php" title="Balliwes">
                          <strong>Balliwes</strong>
                        </a>
                      </h3>
                      <div class="fd-infor">
                        <span class="fdi-item">Community Dance</span>
                        <span class="float-right fdi-type">Abra</span>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </section>
            <!--End: Section film list-->
          </div>
        </div>
        <!--End: Related-->
      </div>
      <!--End: Main-->
      <div id="footer">
        <div class="container">
          <div class="footer-about">
            <div class="footer-fa-text">We are dedicated to preserving and promoting the Igorot heritage through education, cultural displays, and community engagement. Our mission is to ensure that the traditions, dances, and stories of the Cordilleras are passed down to future generations, honoring the past and inspiring the future. </div>
          </div>
          <div class="footer-notice">
            <span>All videos on this site are produced by the <strong>SLU Museum of Igorot Cultures and Arts</strong> to document and preserve Igorot heritage. Unauthorized reproduction or distribution is prohibited. </span>
          </div>
          <div class="footer-logo-block">
            <a href="https://www.facebook.com/slumuseum" class="footer-logo">
              <img src="./assets/img/logo.png" alt="Logo footer">
            </a>
            <p class="copyright">© 2024 SLU Museum of Igorot Cultures and Arts. All Rights Reserved.</p>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
    </div>

    <script>
        document.getElementById('preview').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default link behavior
        
        // Set the video source to the desired path
        var videoPath = 'assets/img/bRoll.mp4';
        var videoSource = document.getElementById('video-source');
        var videoPlayer = document.getElementById('video-player');
        
        // Set the video source and load the video
        videoSource.src = videoPath;
        videoPlayer.load();
        
        // Show the video container and play the video
        document.getElementById('video-container').style.display = 'block';
        videoPlayer.play();
      });
      </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
    <script type="text/javascript" src="res/js/client/dances.js"></script>
  </body>
</html>