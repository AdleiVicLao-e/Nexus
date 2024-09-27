<?php
if (is_null($_SESSION["guest"])) {
  header("Location: ../guest-login.php");
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Igorot Dances</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="index, follow">
        <meta http-equiv="content-language" content="en"/>
        <meta name="description" content="Watch Igorot Dances"/>
        <meta name="keywords" content="watch igorot dances ifugao bontoc kalinga kankanaey isneg ibaloi abra cordillera"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>
        <link rel="icon" href="assets\img\favicon.png" type="image/x-icon">
        <!--Begin: Stylesheet-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
        <link rel="stylesheet" href="res/css/video-player.css">
        <!--End: Stylesheet-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
        <link
        href="https://fonts.googleapis.com/css?family=Inter"
        rel="stylesheet"
      />
    </head>
    <body>
        <div id="app">
            <div style="display: none">
                <h1>Watch ---</h1>
            </div>
            <div id="sidebar_menu_bg"></div>
            <div id="sidebar_menu">
                <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar"><i class="fa fa-angle-left mr-2"></i>Close menu
                </button>
                <ul class="nav sidebar_menu-list">
                    <li class="nav-item active"><a class="nav-link" href="scanner.php"
                                                   title="Home">Home</a></li>
                    <li class="nav-item">
                        <div class="toggle-submenu" data-toggle="collapse" data-target="#sidebar_subs_genre" aria-expanded="false"
                             aria-controls="sidebar_subs_genre"></div>
                        <div class="collapse multi-collapse sidebar_menu-sub" id="sidebar_subs_genre">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Ethnic Groups
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">Bontoc</a>
                                    <a class="dropdown-item" href="#">Ibaloi</a>
                                    <a class="dropdown-item" href="#">Ifugao</a>
                                    <a class="dropdown-item" href="#">Kalanguya</a>
                                    <a class="dropdown-item" href="#">Kankanaey</a>
                                    <a class="dropdown-item" href="#">Isinai</a>
                                    <a class="dropdown-item" href="#">Isneg</a>
                                    <a class="dropdown-item" href="#">Itneg/Tingguian</a>
                                    <a class="dropdown-item" href="#">Kalinga</a>
                                </div>
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
                        <div id="mobile_menu"><i class="fa fa-bars"></i></div>
                        <a href="scanner.php" id="logo"><img src="assets\img\logo.png" alt="Logo">
                        </a>
                        <!--Begin: Menu-->
                        <div id="header_menu">
                            <ul class="nav header_menu-list">
                                <li class="nav-item"><a href="scanner.php" title="Home">Home</a></li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Ethnic Groups
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="#">Bontoc</a>
                                        <a class="dropdown-item" href="#">Ibaloi</a>
                                        <a class="dropdown-item" href="#">Ifugao</a>
                                        <a class="dropdown-item" href="#">Kalanguya</a>
                                        <a class="dropdown-item" href="#">Kankanaey</a>
                                        <a class="dropdown-item" href="#">Isinai</a>
                                        <a class="dropdown-item" href="#">Isneg</a>
                                        <a class="dropdown-item" href="#">Itneg/Tingguian</a>
                                        <a class="dropdown-item" href="#">Kalinga</a>
                                    </div>
                                </li>   
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <!--End: Menu-->
                        <div id="header_right">
                            <div id="search">
                                <div class="search-content">
                                    <form @submit="search">
                                        <div class="search-icon"><i class="fa fa-search"></i></div>
                                        <input v-model="keyword" type="text" class="form-control search-input" autocomplete="off"
                                               name="keyword" placeholder="Enter keywords...">
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
                    <div class="watching">
                        <div class="container">
                            <div style="text-align: center;margin-bottom: 20px;margin-top:20px;display:none;" id="vpn-top"></div>
                            <div style="text-align:center;margin-bottom: 20px;margin-top:20px;display:none;" id="hgiks-top"></div>
                            <div class="prebreadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="/">Home</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="igorot-dances.php" title="Movie">Dances</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Taddok</li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="alert mb-3" style="background: #ffaa00; color: #111; font-size: 16px; font-weight: 600;">If you get any error message when trying to watch the video, please Refresh the page.</div>
                            <div class="watching_player">
                                <div class="watching_player-area">
                                    <div id="mask-player" style="padding-bottom: 56.25%;">
                                        <div class="loading-relative">
                                            <div class="loading">
                                                <div class="span1"></div>
                                                <div class="span2"></div>
                                                <div class="span3"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="watch-player" style="display: none;"></div>
                                    <div id="watch-iframe" style="display: none;padding-bottom: 56.25%;">
                                        <iframe id="iframe-embed" width="100%" height="500" scrolling="no" frameborder="0" src="" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                                    </div>
                                </div>
                                <div class="watching_player-control">
                                    <a href="javascript:void(0)" id="turn-off-light" class="btn btn-sm btn-radius btn-secondary mr-2">
                                        <i class="fa fa-lightbulb mr-2"></i>
                                    </a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Begin: Detail-->
                    <div class="detail_page watch_page">
                        <div class="container">
                            <!--Begin: Watch-->
                            <div class="detail_page-watch" data-watch_id="10670431" data-server="" data-season="" data-episode="" data-id="113755" data-type="1">
                                <div id="vs-vid"></div>
                                <div style="text-align:center;margin-bottom: 20px;margin-top:20px;display:none;" id="hgiks-middle"></div>
                                <div class="detail_page-infor">
                                    <div class="dp-i-content">
                                        <div class="dp-i-c-poster">
                                            <div class="film-poster mb-2">
                                                <img class="film-poster-img" src="assets\img\dance-igorot.jpg" title="Rebel Ridge" alt="watch-Rebel Ridge">
                                            </div>
                                            <div class="block-rating" id="block-rating"></div>
                                        </div>
                                        <div class="dp-i-c-right">
                                            <h3>
                                                <a href="#">Taddok</a>
                                            </h3>
                                            <h4 style="color: #FAC301;">Kalinga</h4>
                                            <div class="dp-i-stats">
                                                <br>
                                                <span class="item mr-1">
                                                    <button data-toggle="modal" data-target="#modaltrailer" title="Preview" class="btn btn-sm btn-trailer">
                                                        <i class="fas fa-video mr-2"></i>
                                                        Preview
                                                    </button>
                                                </span>
                                                <span class="item mr-1">
                                                    <button class="btn btn-sm btn-quality">
                                                        <strong>HD</strong>
                                                    </button>
                                                </span>
                                            </div>
                                            <div class="description" style="text-align: justify; ">The <strong>"Taddok"</strong> is a vibrant and culturally significant dance from the Kalinga people, often performed during fiestas and special festivities. This traditional dance features a captivating display of movement and rhythm, with men skillfully beating gongs as they skip their feet in sync with the tempo.
                                                <br>The women, dancing the <strong>"Tanggi"</strong>, gracefully move with hands wide open at shoulder level, mirroring the rhythm of the gongs while skipping around the dance area. Together, they dance in a circular formation around the plaza, occasionally adding unique patterns to break the monotony of the basic steps.
                                                <br>During major events, taddok contests are held, with dancers donning their finest traditional costumes. These competitions encourage creativity, as participants introduce variations of the steps to stand out and make the celebration even more lively and engaging. The "Taddok" not only showcases Kalinga's heritage but also brings communities together through the joy of movement and tradition.
        </div>
                                            <div class="elements">
                                                <div class="row">
                                                    <div class="col-xl-5 col-lg-6 col-md-8 col-sm-12">
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Released: </strong>
                                                            </span>
                                                            2024-08-27
                    
                                                        </div>
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Origin: </strong>
                                                            </span>
                                                            <a href="#" title="Action">Kalinga</a>
                                                        </div>
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Dance Category: </strong>
                                                            </span>
                                                            <a href="#" title="Brannon Cross">Community Dance</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12">
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Duration: </strong>
                                                            </span>
                                                            5
                        min
                    
                                                        </div>
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Performed by: </strong>
                                                            </span>
                                                            <a href="https://web.facebook.com/ccpgslubaguio" title="performer">SLU Cordillera Cultural Performing Group</a>
                                                        </div>
                                                        <div class="row-line">
                                                            <span class="type">
                                                                <strong>Curated by: </strong>
                                                            </span>
                                                            <a href="https://www.facebook.com/slumuseum" title="slumuseum">SLU Museum of Igorot Culture and Arts</a>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <!--End: Watch-->
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Tadok"
                     alt="watch-Tadok">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Tadok"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Tadok"><strong>Tadok</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Tupayya"
                     alt="watch-Tupayya">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Tupayya"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Tupayya"><strong>Tupayya</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Lablabbaan"
                     alt="watch-Lablabbaan">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Lablabbaan"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Lablabbaan"><strong>Lablabbaan</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Boogie"
                     alt="watch-Boogie">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Boogie"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Boogie"><strong>Boogie</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Boogie Variance"
                     alt="watch-Boogie Variance">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Boogie Variance"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Boogie Variance"><strong>Boogie Variance</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Takkik"
                     alt="watch-Takkik">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Takkik"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Takkik"><strong>Takkik</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Dinuyya"
                     alt="watch-Dinuyya">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Dinuyya"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Dinuyya"><strong>Dinuyya</strong></a>
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
                
                <img data-src="assets\img\dance-igorot.jpg"
                     class="film-poster-img lazyload" title="Balliwes"
                     alt="watch-Balliwes">
                <a href="#"
                   class="film-poster-ahref flw-item-tip" 
                   title="Balliwes"><i class="fa fa-play"></i></a>
            </div>
            <div class="film-detail film-detail-fix">
                
                    <h3 class="film-name"><a
                                href="video-player.php" 
                                title="Balliwes"><strong>Balliwes</strong></a>
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
                <div style="text-align:center;margin-bottom: 20px;margin-top:20px;display:none;" id="hgiks-bottom"></div>
                <div id="footer">
                    <h1 style="display: none;">Watch Free Trap Full Movies Online HD</h1>
                    <div class="container">
                        <div class="footer-about">
                            <div class="footer-fa-text">We are dedicated to preserving and promoting the Igorot heritage through education, cultural displays, and community engagement. Our mission is to ensure that the traditions, dances, and stories of the Cordilleras are passed down to future generations, honoring the past and inspiring the future.
                            </div>
                        </div>
                        <div class="footer-notice">
                            <span>All videos on this site are produced by the <strong>SLU Museum of Igorot Culture and Arts</strong> to document and preserve Igorot heritage. Unauthorized reproduction or distribution is prohibited.</span>
                        </div>
                        <div class="footer-logo-block">
                            <a href="https://www.facebook.com/slumuseum" class="footer-logo"><img
                                        src="assets\img\logo.png" alt="Logo footer"></a>
                            <p class="copyright">© 2024 SLU Museum of Igorot Culture and Arts. All Rights Reserved.</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!--Begin: Modal-->
            <div class="modal fade premodal premodal-login" id="modallogin" tabindex="-1" role="dialog" aria-labelledby="modallogintitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="tab-content">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade premodal premodal-trailer" id="modaltrailer" tabindex="-1" role="dialog" aria-labelledby="modaltrailertitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <div class="iframe16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/bBLYjXIYWvk?si=Ls8fSevjBpBAraRr" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End: Modal-->
            <div id="mask-overlay"></div>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
        <script>
            var currPage = 'watch';
        </script>
        <script type="text/javascript" src="res/js/client/video-player.js"></script>
        <script src="res\js\player.js"></script>
    </body>
</html>
