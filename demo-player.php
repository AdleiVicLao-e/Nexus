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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
  <link rel="stylesheet" href="res/css/dances.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
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
      font-size: 55px; 
      background: white;
      border-radius: 20px;
      padding: 20px;
      margin: 20px;
    }

    video {
      max-width: 90%; 
      width: 900px; 
      height: auto; 
    }

    .description {
      text-align: center;
      margin-top: 20px; 
      font-size: 25px; 
      background: white;
      border-radius: 20px;
      padding: 20px;
      margin: 20px;
    }
  </style>
</head>
<body>
  <div id="app">
    <div id="sidebar_menu_bg"></div>
    <div id="sidebar_menu">
      <button class="btn btn-radius btn-sm btn-secondary toggle-sidebar">
        <i class="fa fa-angle-left mr-2"></i>Close menu
      </button>
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
      </ul>
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
          <div id="header_menu">
            <ul class="nav header_menu-list">
              <li class="nav-item">
                <a href="scanner.php" title="Home">Home</a>
              </li>
              <li class="nav-item active">
                <a href="igorot-dances.php" title="Igorot Dances">Igorot Dances</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="feedback.php" title="Feedback">Feedback</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php" title="About">About</a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
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
</html>
