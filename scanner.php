<?php
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
    <link
      href="https://fonts.googleapis.com/css?family=Inter"
      rel="stylesheet"
    />

    <style>
      #video {
        width: 100%;
        max-width: 400px;
        display: block;
      }

      #canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
      }

      #result {
        position: absolute;
        top: 10px;
        left: 10px;
        color: white;
        font-size: 15px;
        font-weight: bold;
        background-color: #fac301;
        font-family: "Inter", sans-serif;
        padding: 10px;
        border-radius: 5px;
      }

      #imgCondition {
        max-width: 50%;
        height: 50px;
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background-color: #fac301;
        padding: 10px;
        border-radius: 5px;
      }

      .floating-button {
        position: absolute;
        bottom: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #0e1644;
        color: white;
        font-size: 24px;
        border: none;
        cursor: pointer;
        z-index: 1002;
      }

      .floating-button.left {
        display: block;
        border: 3px solid #fac301;
        left: 20px;
      }

      #watchVideosButton {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        padding: 0;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background-image: url("./assets/img/button_background.png");
        background-size: cover;
        background-position: center;
        border: none;
        cursor: pointer;
        z-index: 1003;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: zoomIn 3s;
        border: 3px solid white;
      }

      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
      }

      .info-box {
        background-color: #fff;
        font-family: "Inter", sans-serif;
        background-image: url("./assets/img/info_box.png");
        background-size: cover;
        background-position: center;
        width: 70%;
        padding: 30px;
        align-self: center;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        border: 3px solid #363636;
      }

      .timer {
        margin-top: 10px;
        font-size: 0.7em;
        color: #8a8989;
        font-family: "Inter", sans-serif;
      }

      .info-box-text {
        font-family: "Inter", sans-serif;
        font-size: 0.9em;
      }

      #watchVideosButton::before {
        content: "\25B6";
        color: white;
        font-size: 30px;
        display: block;
      }

      @keyframes zoomIn {
        0% {
          transform: scale(0.8);
        }

        100% {
          transform: scale(1);
        }
      }

      #watchVideosButton:hover {
        background-color: #fac301;
      }

      #noArtifactImage {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        height: auto;
        z-index: 10;
        animation: fadeOut 3s ease-in-out infinite;
      }

      .magnifying-glass {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100px;
        height: 100px;
        transform: translate(-50%, -50%);
        display: none;
        animation: wave 1s ease-out;
      }

      @keyframes wave {
        0% {
          transform: translate(-50%, -50%) rotate(0deg);
        }
        50% {
          transform: translate(-50%, -50%) rotate(15deg);
        }
        100% {
          transform: translate(-50%, -50%) rotate(-15deg);
        }
      }

      /* Add the CSS for VA model container */
      #va-container {
        position: fixed;
        bottom: 20px;
        right: 1px;
        width: 465px;
        /* Adjust width as needed */
        height: 425px;
        /* Adjust height as needed */
        overflow: hidden;
        z-index: 1001;
        /* High z-index to ensure it's on top */
        pointer-events: auto;
      }
    </style>
  </head>
  <body>
    <!-- AR Elements -->
    <video id="video" autoplay></video>
    <canvas id="canvas"></canvas>
    <div id="result">Scan a QR code</div>
    <img
      id="noArtifactImage"
      src="assets/img/Error.png"
      alt="No Artifact Found"
    />

    <div
      id="conditionLabel"
      style="
        position: absolute;
        top: 90px;
        font-family: 'Inter', sans-serif;
        right: 10px;
        color: white;
        font-size: 12px;
        font-weight: bold;
        background-color: #fac301;
        padding: 8px;
        border-radius: 5px;
        display: none;
      "
    >
      Condition
    </div>

    <img
      id="imgCondition"
      src="assets/condition/default.png"
      alt="Placeholder Image"
      style="display: none"
      ;
    />

    <!-- Floating Buttons -->
    <button class="floating-button left" onclick="viewDetails()">i</button>
    <button
      id="watchVideosButton"
      onclick="window.location.href='igorot-dances.php';"
    ></button>

    <!-- Magnifying Glass -->
    <img
      src="/assets/img/magnifying_glass.png"
      class="magnifying-glass"
      id="magnifying-glass"
      alt="Magnifying Glass"
    />

    <!-- Overlay with the info box -->
    <div class="overlay" id="infoOverlay">
      <div class="info-box">
        <p class="welcome-2">WELCOME!</p>
        <p class="info-box-text">
          This is application will provide you with more information about the
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

        <!-- Timer -->
        <p class="timer">
          This message will disappear in <span id="countdown">8</span> seconds.
        </p>
      </div>
    </div>
    <div id="va-container">
      <canvas id="va-canvas"></canvas>
    </div>

    <script>
      const video = document.getElementById("video");
      const canvas = document.getElementById("canvas");
      const canvasContext = canvas.getContext("2d");
      const resultDiv = document.getElementById("result");
      const noArtifactImage = document.getElementById("noArtifactImage");
      const imgCondition = document.getElementById("imgCondition");
      const conditionLabel = document.getElementById("conditionLabel");
      const magnifyingGlass = document.getElementById("magnifying-glass");

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

      // Function to scan QR code
      function scanQRCode() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = canvasContext.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
          );
          const code = jsQR(imageData.data, canvas.width, canvas.height, {
            inversionAttempts: "dontInvert",
          });

          canvasContext.clearRect(0, 0, canvas.width, canvas.height);
          canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

          if (code) {
            const artifactId = code.data;
            resultDiv.textContent = `QR Code Detected`;
            handleArtifact(artifactId);
            fetchArtifactInfo(artifactId);

            if (displayBox) {
              const centerX =
                (code.location.topLeftCorner.x +
                  code.location.topRightCorner.x) /
                2;
              const centerY =
                (code.location.topLeftCorner.y +
                  code.location.bottomLeftCorner.y) /
                2;

              const boxWidth = 350;
              const boxHeight = 200;
              const depth = 20;
              const padding = 20;

              // Front face of the rectangle (Book cover)
              canvasContext.fillStyle = "rgba(14, 22, 68, 0.95)";
              canvasContext.fillRect(
                centerX - boxWidth / 2,
                centerY - boxHeight / 2,
                boxWidth,
                boxHeight
              );

              // Add text inside the front face of the rectangle (Book title or information)
              canvasContext.font = "bold 15px 'Inter', sans-serif";
              canvasContext.fillStyle = "white";
              canvasContext.textAlign = "left"; // Align left to control wrapping
              canvasContext.textBaseline = "top";

              if (artifactInfo) {
                const lines = artifactInfo.split("\n");
                const lineHeight = 20; // Adjust the line height as necessary
                const maxLineWidth = boxWidth - 2 * padding;

                let currentY = centerY - boxHeight / 2 + padding;

                lines.forEach((line) => {
                  let words = line.split(" ");
                  let currentLine = "";

                  words.forEach((word, index) => {
                    let testLine = currentLine + word + " ";
                    let testWidth = canvasContext.measureText(testLine).width;

                    if (testWidth > maxLineWidth && index > 0) {
                      canvasContext.fillText(
                        currentLine,
                        centerX - boxWidth / 2 + padding,
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
                    centerX - boxWidth / 2 + padding,
                    currentY
                  );
                  currentY += lineHeight;

                  // Stop rendering if text exceeds the box height
                  if (currentY > centerY + boxHeight / 2 - padding) return;
                });
              }
            }
          } else {
            resultDiv.textContent = "No QR Code Detected";
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

              //Show result and button
              document.getElementById("result").style.display = "block";
              document.getElementById("watchVideosButton").style.display =
                "block";
            } else {
              // If artifact is not found
              artifactInfo = "";
              displayBox = false;
              noArtifactImage.style.display = "block";
              resultDiv.textContent = `No artifact found`;
              magnifyingGlass.style.display = "none";

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

      // The page was loaded from the bfcache (back-forward cache)
      window.addEventListener("pageshow", (event) => {
        if (event.persisted) {
          window.location.reload();
        }
        magnifyingGlass.style.display = "block";
        setTimeout(() => {
          magnifyingGlass.style.display = "none";
        }, 2000);
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