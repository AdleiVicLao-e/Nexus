<?php
session_start();
if (is_null($_SESSION["guest"])) {
  header("Location: ../index.php");
}

// Database connection (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kultoura";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the artifact number from the query parameter (e.g., artifact.php?artifact_id=1)
$artifact_id = isset($_GET['artifact_id']) ? intval($_GET['artifact_id']) : 0;

// Query to fetch the artifact name, media path, and description for the given artifact number
$sql = "SELECT name, media_path, description FROM artifact_info WHERE artifact_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artifact_id);
$stmt->execute();
$result = $stmt->get_result();

$name = '';
$media_path = '';
$description = ''; // Variable for the description

if ($result->num_rows > 0) {
  // Fetch artifact details
  $row = $result->fetch_assoc();
  $name = $row['name'];
  $media_path = $row['media_path'];
  $description = $row['description']; // Fetching description
} else {
  $name = 'Artifact not found';
  $media_path = 'No video available'; // Fallback message if no media found
  $description = ''; // No description available
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Artifact Media</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="en" />
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
          <a class="nav-link" href="scanner.php" title="Home">Scanner</a>
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
              <a class="nav-link" href="scanner.php" title="Home">Scanner</a>
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
      <style>
        body {
        background-image: url('../../assets/img/homepage_background.png');
        background-size: cover;
        background-position: center;
        background-repeat: repeat;
        background-attachment: fixed;
        }

        h1 {
          text-align: center;
          margin-bottom: 20px; 
          style: bold;
          font-size: 30px; 
          background: white;
          border-radius: 15px;
          padding: 15px;
          margin: 10px;
        }

        video {
          max-width: 90%; 
          width: 900px; 
          height: auto; 
        }

        .description {
          text-align: center;
          margin-top: 20px; 
          font-size: 15px; 
          background: #073066;
          border-radius: 20px;
          padding: 15px;
          margin: 10px;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <?php if ($media_path !== 'No video available') { ?>
          <video width="600" controls>
            <source src="<?php echo htmlspecialchars($media_path); ?>" type="video/mp4">
            Your browser does not support the video tag.
          </video>
          <div class="description">
            <p><?php echo htmlspecialchars($description); ?></p> <!-- Displaying the description -->
          </div>
        <?php } else { ?>
          <p><?php echo $media_path; ?></p>
        <?php } ?>
      </div>
    </div>
  </div>
</body>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar_menu");
    const mobileMenu = document.getElementById("mobile_menu");

    mobileMenu.addEventListener("click", function() {
        sidebar.classList.toggle("active"); // Toggle the sidebar menu
    });

    const closeButton = document.querySelector(".toggle-sidebar");
    closeButton.addEventListener("click", function() {
        sidebar.classList.remove("active"); // Close the sidebar when clicking 'Close menu'
    });
});
  </script>
</html>
