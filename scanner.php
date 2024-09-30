<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../guest-login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Code Scanner with AR</title>
    <script src="res/js/client/jsQR.js"></script>
    <link rel="icon" href="assets\img\favicon.png" type="image/x-icon" />
    <link rel="stylesheet" href="res/css/styles.css">
    <nav>
    <i class="bx bx-menu"></i>
        <div class="logo">
          <a href="index.php">
            <img
              src="assets/img/logo.png"
              style="height: 30px; margin-top: 20px; margin-left: 90px;"
              alt="Kultoura Logo"
            >
          </a>
        </div>
    </nav>
  </head>
  <body>
    <!-- AR Elements -->
    <video id="video" autoplay></video>
    <canvas id="canvas"></canvas>
    <img
      id="noArtifactImage"
      src="assets/img/error_image.png"
      alt="No Artifact Found"
    />

    <div id="conditionLabel">
      Condition
    </div>

    <img
      id="imgCondition"
      src="assets/condition/default.png"
      alt="Placeholder Image"
      style="display: none; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);"
      ;
    />

    <!-- Floating Buttons -->
    <img class="lightbulb-icon" id="lightbulbIcon" src="/assets/img/gong.png" alt="Info" onclick="viewDetails()" /> <!-- Light bulb icon -->

    <button id="watchVideosButton" onclick="window.location.href='igorot-dances.php';">
    Watch Videos
    </button>

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

    <script>
      const video = document.getElementById("video");
      const canvas = document.getElementById("canvas");
      const canvasContext = canvas.getContext("2d");
      const noArtifactImage = document.getElementById("noArtifactImage");
      const imgCondition = document.getElementById("imgCondition");
      const conditionLabel = document.getElementById("conditionLabel");

      let artifactInfo = "";
      let displayBox = true;
      let imageValue = "default";

      // Function to start video stream
      async function startVideo() {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" },
          });
          video.srcObject = stream;
          // required to play video inline on iOS
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

            if (displayBox) {
              const centerX =
                (code.location.topLeftCorner.x +
                  code.location.topRightCorner.x) / 2;
              const centerY =
                (code.location.topLeftCorner.y +
                  code.location.bottomLeftCorner.y) / 2;

              const boxWidth = 400;
              const boxHeight = 250;
              const padding = 20;

              // Draw the image instead of a rectangle
              canvasContext.drawImage(
                boxImage,
                centerX - boxWidth / 2,
                centerY - boxHeight / 2,
                boxWidth,
                boxHeight
              );

              // Define offsets for spacing
              const topOffset = 10; // Space at the top
              const leftOffset = 10; // Space on the left

              canvasContext.font = "bold 15px 'Inter', sans-serif";
              canvasContext.fillStyle = "#502a00";
              canvasContext.textAlign = "left";
              canvasContext.textBaseline = "top";

              if (artifactInfo) {
                const lines = artifactInfo.split("\n");
                const lineHeight = 20; // Adjust the line height as necessary
                const maxLineWidth = boxWidth - 2 * padding;

                let currentY = centerY - boxHeight / 2 + padding + topOffset; // Add topOffset here

                lines.forEach((line) => {
                  let words = line.split(" ");
                  let currentLine = "";

                  words.forEach((word, index) => {
                    let testLine = currentLine + word + " ";
                    let testWidth = canvasContext.measureText(testLine).width;

                    if (testWidth > maxLineWidth && index > 0) {
                      canvasContext.fillText(
                        currentLine,
                        centerX - boxWidth / 2 + padding + leftOffset, // Add leftOffset here
                        currentY
                      );
                      currentLine = word + " ";
                      currentY += lineHeight;

                      // Stop rendering if text exceeds the box height
                      if (currentY > centerY + boxHeight / 2 - padding) return;
                    } else {
                      currentLine = testLine;
                    }
                  });

                  // Render the last line of the paragraph
                  canvasContext.fillText(
                    currentLine,
                    centerX - boxWidth / 2 + padding + leftOffset, // Add leftOffset here
                    currentY
                  );
                  currentY += lineHeight;

                  // Stop rendering if text exceeds the box height
                  if (currentY > centerY + boxHeight / 2 - padding) return;
                });
              }
            }
          } else {
            conditionLabel.style.display = "none";
            imgCondition.style.display = "none";
            document.getElementById("watchVideosButton").style.display = "none";
            artifactInfo = "";
          }
        }
        requestAnimationFrame(scanQRCode);
      }

      function fetchArtifactInfo(artifactId) {
        fetch(`include/getArtifact.php?artifact_id=${artifactId}`)
          .then((response) => response.json())
          .then((data) => {
            console.log("Data:", data);

            if (data && Object.keys(data).length > 0) {
              // If artifact is found
              artifactInfo = `
                    \n${data["Name"] || "N/A"}
                    \nCatalogue: ${data["Catalogue Name"] || "N/A"}
                    \nSubcatalogue: ${data["Subcatalogue Name"] || "N/A"}
                    \nDescription: ${data["Description"] || "N/A"}
                `.trim();
                
              noArtifactImage.style.display = "none";
              displayBox = true;

              //Show condition
              let imageValue = data["Condition"] || "default";
              changeImage(imageValue);

              //Show watch button
              document.getElementById("watchVideosButton").style.display =
                "block";
            } else {
              // If artifact is not found
              artifactInfo = "";
              displayBox = false;
              noArtifactImage.style.display = "block";

              setTimeout(() => {
                noArtifactImage.style.animation = "none";
                noArtifactImage.style.opacity = 0;
                setTimeout(() => {
                  noArtifactImage.style.display = "none";
                  noArtifactImage.style.opacity = 1;
                }, 1000);
              }, 2000);

              let imageValue = data["condition"] || "default";
              document.getElementById("watchVideosButton").style.display =
                "none";
            }
          })
          .catch((error) => {
            document.getElementById("watchVideosButton").style.display = "none";
            console.error("Error fetching artifact info:", error);
            artifactInfo = "Error fetching artifact info";
            conditionLabel.style.display = "none";
            imgCondition.style.display = "none";
          });
      }

      // Function to show the overlay
      function viewDetails() {
        const overlay = document.getElementById("infoOverlay");
        overlay.style.display = "flex";

        let countdownTime = 6;
        const countdownElement = document.getElementById("countdown");

        // Update the countdown every second
        const countdownInterval = setInterval(() => {
          countdownTime--;
          countdownElement.textContent = countdownTime;

          // When countdown reaches 0, hide the overlay
          if (countdownTime <= 0) {
            clearInterval(countdownInterval);
            overlay.style.display = "none";
          }
        }, 1000);
      }

      // Change the image according to the condition
      function changeImage(value) {
        if (value.includes("No problem")) {
          imgCondition.src = "/assets/condition/no_problem.png";
        } else if (
          value.includes("crack") ||
          value.includes("cracks") ||
          value.includes("breakage") ||
          value.includes("broken")
        ) {
          imgCondition.src = "/assets/condition/with_breakage.png";
        } else if (value.includes("holes") || value.includes("hole")) {
          imgCondition.src = "assets/condition/holes.png";
        } else if (value.includes("rotten") || value.includes("rot")) {
          imgCondition.src = "assets/condition/rotten.png";
        } else {
          imgCondition.src = "assets/condition/default.png";
        }

        conditionLabel.style.display = "block";
        imgCondition.style.display = "block";
      }

      document.getElementById("exitButton").addEventListener("click", function() {
      document.getElementById("infoOverlay").style.display = "none"; 
  });

      // Start the video stream when the page loads
      startVideo();
      
    </script>
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
