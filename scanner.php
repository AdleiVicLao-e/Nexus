<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>QR Code Scanner with AR</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="en" />
    <meta name="description" content="Watch Igorot Dances" />
    <meta name="keywords" content="watch igorot dances ifugao bontoc kalinga kankanaey isneg ibaloi abra cordillera" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
    <link rel="stylesheet" href="res\css\styles.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
    <script src="res/js/client/jsQR.js"></script>
    <div id="sidebar_menu_bg"></div>
    <div id="sidebar_menu">
      <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar">
        <i class="fa fa-angle-left mr-2"></i>Close menu </button>
      <ul class="nav sidebar_menu-list">
        <li class="nav-item active">
          <a class="nav-link" href="homepage.php" title="Home">Home</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="igorot-dances.php" title="Igorot-Dances">Igorot Dances</a>
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
          <img src="assets/img/logo.png" alt="Logo">
        </a>
        <div id="header_menu">
          <ul class="nav header_menu-list">
            <li class="nav-item active">
              <a href="homepage.php" title="Home">Home</a>
            </li>
            <li class="nav-item">
            <a href="igorot-dances.php" title="Igorot Dances">Igorot Dances</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="feedback.php" title="Home">Feedback</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php" title="About">About</a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
       </div>
    </div>
  </head>
  <body>
    <!-- AR Elements -->
    <video id="video" autoplay></video>
    <canvas id="canvas"></canvas>
    <div id="va-container">
      <canvas id="va-canvas"></canvas>
    </div>
    <div class="edge-lighting" id="edgeLighting"></div>

    <!-- Floating Buttons -->
    <!-- Light bulb icon with onclick event -->
    <img class="lightbulb-icon" id="lightbulbIcon" src="/assets/img/gong.png" alt="Info" onclick="viewDetails()" />

    <!-- Add an audio element -->
    <audio id="lightbulbAudio" src="/assets/audio/click-sound.mp3" preload="auto"></audio>
    
    <button id="watchVideos" onclick="redirectToVideo();">Watch Videos</button>

    <!-- Overlay with the info box -->
    <div class="overlay" id="infoOverlay">
      <button class="exit-button" id="exitButton">✖</button> <!-- Exit button -->
      <div class="info-box">
        <p class="welcome-2">WELCOME!</p>
        <p class="info-box-text">
          This application will provide you with more information about the
          museum artifacts.
        </p>
        <p class="info-box-text">
          To use it, simply position your phone's camera in front of the QR
          codes located near the artifacts.
        </p>
        <p class="info-box-text">
          It will automatically detect the codes and display detailed
          information about each artifact.
        </p>
      </div>
    </div>

    <div id="desktop-warning">Mobile Only Site.</div>

    <script>
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const canvasContext = canvas.getContext("2d");
        const watchButton = document.getElementById("watchVideos");
        const audio = document.getElementById("lightbulbAudio");

        let artifactInfo = "";
        let displayBox = true;
        let imageValue = "default";
        let firstScan = false; // Track whether the first artifact is scanned

        // Variables for smoothing
        let lastTopLeft = null;
        let lastTopRight = null;
        let lastBottomLeft = null;
        let lastBottomRight = null;

        // Initially hide the watch button
        watchButton.style.display = "none";

        // Function to start video stream
        async function startVideo() {
            try {
                video.srcObject = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment" },
                });
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(scanQRCode);
            } catch (error) {
                console.error("Error accessing webcam:", error);
            }
        }

        const boxImage = new Image();
        boxImage.src = '/assets/img/display.png';

        // Function to scan QR code
        function scanQRCode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, canvas.width, canvas.height, {
                    inversionAttempts: "dontInvert",
                });

                canvasContext.clearRect(0, 0, canvas.width, canvas.height);
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

                if (code) {
                    const artifactId = code.data;

                    handleArtifact(artifactId);
                    fetchArtifactInfo(artifactId);
                    onQRCodeScanned();

                    // Smooth Tracking: Interpolate the QR code corners to simulate smoother movement
                    const smoothedCorners = smoothTrack(code.location);

                    // Draw the smooth tracked box
                    drawTrackedBox(smoothedCorners);

                    // Display the artifact information box
                    displayArtifactInfo(smoothedCorners.center);

                    // Show the watch button only after the first artifact scan
                    if (!firstScan) {
                        firstScan = true;
                    }
                    watchButton.style.display = "block";
                    setTimeout(() => {
                        watchButton.style.display = "none";
                    }, 7000);
                } else {
                    artifactInfo = "";
                    displayBox = false;
                }
            }
            requestAnimationFrame(scanQRCode);
        }

        // Function to smooth the tracking of corners
        function smoothTrack(location) {
            const smoothingFactor = 0.2;

            const currentTopLeft = location.topLeftCorner;
            const currentTopRight = location.topRightCorner;
            const currentBottomLeft = location.bottomLeftCorner;
            const currentBottomRight = location.bottomRightCorner;

            // Initialize last positions if they don't exist
            lastTopLeft = lastTopLeft || currentTopLeft;
            lastTopRight = lastTopRight || currentTopRight;
            lastBottomLeft = lastBottomLeft || currentBottomLeft;
            lastBottomRight = lastBottomRight || currentBottomRight;

            // Smooth the corners
            lastTopLeft = {
                x: lastTopLeft.x + (currentTopLeft.x - lastTopLeft.x) * smoothingFactor,
                y: lastTopLeft.y + (currentTopLeft.y - lastTopLeft.y) * smoothingFactor,
            };
            lastTopRight = {
                x: lastTopRight.x + (currentTopRight.x - lastTopRight.x) * smoothingFactor,
                y: lastTopRight.y + (currentTopRight.y - lastTopRight.y) * smoothingFactor,
            };
            lastBottomLeft = {
                x: lastBottomLeft.x + (currentBottomLeft.x - lastBottomLeft.x) * smoothingFactor,
                y: lastBottomLeft.y + (currentBottomLeft.y - lastBottomLeft.y) * smoothingFactor,
            };
            lastBottomRight = {
                x: lastBottomRight.x + (currentBottomRight.x - lastBottomRight.x) * smoothingFactor,
                y: lastBottomRight.y + (currentBottomRight.y - lastBottomRight.y) * smoothingFactor,
            };

            return {
                topLeft: lastTopLeft,
                topRight: lastTopRight,
                bottomLeft: lastBottomLeft,
                bottomRight: lastBottomRight,
                center: {
                    x: (lastTopLeft.x + lastTopRight.x) / 2,
                    y: (lastTopLeft.y + lastBottomLeft.y) / 2,
                },
            };
        }

        // Function to draw the tracked box
        function drawTrackedBox(corners) {
            canvasContext.beginPath();
            canvasContext.moveTo(corners.topLeft.x, corners.topLeft.y);
            canvasContext.lineTo(corners.topRight.x, corners.topRight.y);
            canvasContext.lineTo(corners.bottomRight.x, corners.bottomRight.y);
            canvasContext.lineTo(corners.bottomLeft.x, corners.bottomLeft.y);
            canvasContext.closePath();
            canvasContext.lineWidth = 4;
            canvasContext.strokeStyle = "#FF3B58";
            canvasContext.stroke();
        }

        // Function to display artifact information
        function displayArtifactInfo(center) {
            if (displayBox) {
                const boxWidth = 400;
                const boxHeight = 250;
                const padding = 20;

                // Draw the image instead of a rectangle
                canvasContext.drawImage(
                    boxImage,
                    center.x - boxWidth / 2,
                    center.y - boxHeight / 2,
                    boxWidth,
                    boxHeight
                );

                // Text rendering
                canvasContext.font = "bold 15px 'Inter', sans-serif";
                canvasContext.fillStyle = "#502a00";
                canvasContext.textAlign = "left";
                canvasContext.textBaseline = "top";

                if (artifactInfo) {
                    const lines = artifactInfo.split("\n");
                    const lineHeight = 20; // Adjust the line height as necessary
                    let currentY = center.y - boxHeight / 2 + padding;

                    lines.forEach((line) => {
                        canvasContext.fillText(line, center.x - boxWidth / 2 + padding, currentY);
                        currentY += lineHeight;

                        // Stop rendering if text exceeds the box height
                        if (currentY > center.y + boxHeight / 2 - padding)
                    });
                }
            }
        }

        function fetchArtifactInfo(artifactId) {
            fetch(`include/getArtifact.php?artifact_id=${artifactId}`)
                .then((response) => response.json())
                .then((data) => {
                    console.log("Data:", data);
                    if (data && Object.keys(data).length > 0) {
                        artifactInfo = `
                        \n${data["Name"] || "N/A"}
                        \nDescription: ${data["Description"] || "N/A"}
                    `.trim();
                        displayBox = true;
                    } else {
                        artifactInfo = "";
                        displayBox = false;
                    }
                })
                .catch((error) => {
                    console.error("Error fetching artifact info:", error);
                    artifactInfo = "Error fetching artifact info";
                });
        }

        // Function to show the overlay
        function viewDetails() {
            const overlay = document.getElementById("infoOverlay");
            overlay.style.display = "flex";
            audio.play();
        }

        document.getElementById("exitButton").addEventListener("click", function() {
            document.getElementById("infoOverlay").style.display = "none";
            audio.pause();
            audio.currentTime = 0;
        });

        function checkDevice() {
            // Define a breakpoint for mobile devices (e.g., 768px)
            if (window.innerWidth > 768) {
                // Show the desktop warning
                document.getElementById("desktop-warning").style.display = "flex";
                setTimeout(function() {
                    alert("Please use your mobile phone to access this site.");
                }, 100);
            }
        }

        // Variable to store the current artifact ID
        let currentArtifactId = null;

        // Function to redirect the user to the video player page with the artifactId
        function redirectToVideo() {
            if (currentArtifactId) {
                window.location.href = `demo-player.php?artifact_id=${currentArtifactId}`;
            } else {
                alert("No artifact has been scanned yet.");
            }
        }

        // Start the video stream when the page loads
        startVideo();
        checkDevice();
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.1/lazysizes.min.js" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>
    <script src="https://cubism.live2d.com/sdk-web/cubismcore/live2dcubismcore.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/dylanNew/live2d/webgl/Live2D/lib/live2d.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pixi.js@6.5.2/dist/browser/pixi.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pixi-live2d-display/dist/index.min.js"></script>
    <script src="./res/js/client/VA.js"></script>
    <script src="./res/js/scripts.js"></script>
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/AR-js-org/AR.js/aframe/build/aframe-ar.js"></script>
  </body>
</html>