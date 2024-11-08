<?php
include 'include/artifact-db.php';

// Get the artifact number from the query parameter (e.g., artifact.php?artifact_id=1)
$artifact_id = isset($_GET['artifact_id']) ? intval($_GET['artifact_id']) : 0;

// SQL Query withh all the fields properly initialized
$sql = "SELECT name, fileName, description FROM artifact_info WHERE artifact_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $artifact_id);
$stmt->execute();
$result = $stmt->get_result();

$name = '';
$media_path = 'assets/videos/specific/';
$fileName = '';
$description = ''; // Variable for the description

if ($result->num_rows > 0) {
  // Fetch artifact details
  $row = $result->fetch_assoc();
  $name = $row['name'];
  $fileName = $row['fileName'];
  $description = $row['description']; // Fetching description
} else {
  $name = 'Artifact not found';
  $fileName = 'No video available'; // Fallback message if no media found
  $description = ''; // No description available
}

$stmt->close();
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
            font-family: 'Inter', sans-serif;
            background-image: url('../../assets/img/homepage_background.png');
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            background-attachment: fixed;
            margin: 0;
            height: 100vh;
        }

        h1 {
            text-align: center;
            font-family: 'Inter', sans-serif;
            margin-bottom: 20px;
            style: bold;
            font-size: 20px;
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin: 10px;
        }

        video {
            max-width: 90%;
            width: 900px;
            margin: 20px;
            height: auto;
        }

        .descriptions {
            font-family: 'Inter', sans-serif;
            text-align: center;
            margin-top: 20px;
            background: white;
            border-radius: 20px;
            padding: 10px;
            margin: 10px;
        }

        p {
            font-family: 'Inter', sans-serif;
            color: #272626;
            font-size: 12px;
        }

        #scanAgain {
            background-color: #f6c500;
            font-family: 'Inter', sans-serif;
            color: white;
            padding: 15px;
            font-size: 10px;
            margin: 10px;
            border-radius: 20px;
            cursor: pointer;
            max-width: 300px;
            border: white;
            position: fixed;
            left: 50%;
            bottom: 20px;
            transform: translateX(-50%);
        }
        </style>
</head>

<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <?php if ($media_path !== 'No video available') { ?>
        <video width="800" controls autoplay muted>
            <source src="<?php echo htmlspecialchars($media_path . $fileName); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="descriptions">
            <p><?php echo htmlspecialchars($description); ?></p> <!-- Displaying the description -->
        </div>
        <?php } else { ?>
        <p><?php echo $media_path; ?></p>
        <?php } ?>
        <button id="scanAgain" onclick="location.href='scanner.php'">Scan more artifacts</button>
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
<script defer>
const guestSession = getLocalStorageItem('guest');
if (guestSession) {
    console.log("Guest logged in. Redirecting...");
} else {
    alert("Not logged in. Redirected to Login.");
    window.location.href = "index.php";
}
</script>

</html>