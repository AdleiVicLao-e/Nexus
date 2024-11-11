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
            <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar"
                style="border-radius: 30px; background: #eee; border-color: #eee;color: #111;">
                <i class="fa fa-angle-left mr-2"></i>Close menu </button>
            <ul class="nav sidebar_menu-list">
                <li class="nav-item active">
                    <a class="nav-link" href="homepage.php" title="Home">Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="scanner.php" title="Home">Scanner</a>
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

        <!-- Content Section -->
        <section id="content">
            <h2>Discover Igorot Dances</h2>
            <p>Experience the beauty and diversity of Igorot dances, each one telling a unique story of the Igorot
                people’s life, beliefs, and heritage.</p>
            <!-- Add more dance sections as needed -->
        </section>

        <!--Begin: Related-->
        <div class="container">
            <?php
        include 'include/artifact-db.php';

        // Fetch video details from the database
        $result = $mysqli->query("SELECT title, description, fileName FROM uncategorized_media");

        if ($result->num_rows > 0) {
            // Display each video
            while ($row = $result->fetch_assoc()) {
                $title = $row['title'];
                $description = $row['description'];
                $fileName = $row['fileName'];
                $videoPath = "../assets/videos/general/" . $fileName;
                $posterPath = "../assets/videos/general/thumbnails/" . pathinfo($fileName, PATHINFO_FILENAME) . ".jpg";

                echo '
                <div style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; margin-top: 30px;">
                    <h2 style="font-size: 24px; color: #333; margin-bottom: 10px; margin-top: 10px;">' . htmlspecialchars($title) . '</h2>
                    <p style="font-size: 16px; color: #666; margin-bottom: 15px; margin-top: 10px;">' . htmlspecialchars($description) . '</p>
                    <video id="video-player" autoplay muted playsinline style="width: 100%; max-width: 600px; border-radius: 5px;" poster="' . htmlspecialchars($posterPath) . '">
                    <source src="' . htmlspecialchars($videoPath) . '" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                </div>';
            }
        } else {
            echo '<p>No videos available.</p>';
        }

        $mysqli->close();
        ?>
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
            </div>
            <div class="footer-notice">
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
    document.querySelectorAll('video').forEach(video => {
        // Handle play/pause on click
        video.addEventListener('click', function() {
            // Check if the video is currently playing
            if (video.paused) {
                // If the video is paused, play it
                video.muted = false; // Ensure audio is enabled
                video.play();
            } else {
                // If the video is playing, pause it
                video.pause();
            }
        });

        // Fullscreen toggle for video when clicked (for mobile)
        video.addEventListener('click', function() {
            // Check if the video is in fullscreen mode
            if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
                document.exitFullscreen
                    ? document.exitFullscreen()
                    : document.webkitExitFullscreen
                    ? document.webkitExitFullscreen()
                    : document.msExitFullscreen && document.msExitFullscreen();
            } else {
                // Enter full screen mode
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.webkitRequestFullscreen) { // Safari
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) { // IE/Edge
                    video.msRequestFullscreen();
                }
            }
        });

        // Rewind 10 seconds button functionality for all videos
        const rewindButton = document.createElement('button');
        rewindButton.textContent = 'Rewind 10s';
        rewindButton.style.position = 'absolute';
        rewindButton.style.bottom = '10px';
        rewindButton.style.left = '10px';
        rewindButton.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
        rewindButton.style.color = '#fff';
        rewindButton.style.padding = '10px';
        rewindButton.style.border = 'none';
        rewindButton.style.borderRadius = '5px';
        rewindButton.style.fontSize = '16px';
        video.parentElement.style.position = 'relative'; // Ensure parent is positioned for button placement
        video.parentElement.appendChild(rewindButton);

        rewindButton.addEventListener('click', function() {
            video.currentTime -= 10;  // Rewind the video by 10 seconds
        });

        // Forward 10 seconds button functionality for all videos
        const forwardButton = document.createElement('button');
        forwardButton.textContent = 'Forward 10s';
        forwardButton.style.position = 'absolute';
        forwardButton.style.bottom = '10px';
        forwardButton.style.right = '10px';
        forwardButton.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
        forwardButton.style.color = '#fff';
        forwardButton.style.padding = '10px';
        forwardButton.style.border = 'none';
        forwardButton.style.borderRadius = '5px';
        forwardButton.style.fontSize = '16px';
        video.parentElement.appendChild(forwardButton);

        forwardButton.addEventListener('click', function() {
            video.currentTime += 10;  // Jump forward by 10 seconds
        });
    });
    </script>

    <script defer>
    const guestSession = getLocalStorageItem('guest');
    if (guestSession) {
        console.log("Guest logged in. Redirecting...");
    } else {
        alert("Not logged in. Redirected to Login.");
        window.location.href = "index.php";
    }
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js">
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async>
    </script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async>
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
    <script type="text/javascript" src="res/js/client/dances.js"></script>
</body>

</html>