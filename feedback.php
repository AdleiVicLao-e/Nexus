<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedback</title>
    <link rel="stylesheet" href="res\css\feedback.css">
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
                    <a class="nav-link" href="homepage.php" title="Home">Home</a>
                    <a class="nav-link" href="scanner.php" title="Home">Scanner</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="igorot-dances.php" title="Igorot Dances">Igorot Dances</a>
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
                            <a class="nav-link" href="about.php" title="About">About</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <!--End: Menu-->
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="feedback-form">
            <h2>Museum Feedback Form</h2>
            <form onsubmit="showThankYouMessage(event)">

                <br>
                <div class="form-group">
                    <h2>Museum Feedback Form</h2>
                </div>

                <!-- Quality/Presentation of Exhibits -->
                <div class="form-group">
                    <label for="exhibits">Quality/Presentation of Exhibits:</label><br>
                    <div class="form-options">
                        <input type="radio" id="excellent1" name="exhibits" value="Excellent" required>
                        <label for="excellent1">Excellent</label><br>
                        <input type="radio" id="good1" name="exhibits" value="Good">
                        <label for="good1">Good</label><br>
                        <input type="radio" id="average1" name="exhibits" value="Average">
                        <label for="average1">Average</label><br>
                        <input type="radio" id="dissatisfied1" name="exhibits" value="Dissatisfied">
                        <label for="dissatisfied1">Dissatisfied</label>
                    </div>
                </div>

                <!-- Cleanliness and Ambiance -->
                <div class="form-group">
                    <label for="cleanliness">Cleanliness and Ambiance:</label><br>
                    <div class="form-options">
                        <input type="radio" id="excellent2" name="cleanliness" value="Excellent" required>
                        <label for="excellent2">Excellent</label><br>
                        <input type="radio" id="good2" name="cleanliness" value="Good">
                        <label for="good2">Good</label><br>
                        <input type="radio" id="average2" name="cleanliness" value="Average">
                        <label for="average2">Average</label><br>
                        <input type="radio" id="dissatisfied2" name="cleanliness" value="Dissatisfied">
                        <label for="dissatisfied2">Dissatisfied</label>
                    </div>
                </div>

                <!-- Museum Staff Service -->
                <div class="form-group">
                    <label for="staff">Museum Staff Service:</label><br>
                    <div class="form-options">
                        <input type="radio" id="excellent3" name="staff" value="Excellent" required>
                        <label for="excellent3">Excellent</label><br>
                        <input type="radio" id="good3" name="staff" value="Good">
                        <label for="good3">Good</label><br>
                        <input type="radio" id="average3" name="staff" value="Average">
                        <label for="average3">Average</label><br>
                        <input type="radio" id="dissatisfied3" name="staff" value="Dissatisfied">
                        <label for="dissatisfied3">Dissatisfied</label>
                    </div>
                </div>

                <!-- Overall Experience -->
                <div class="form-group">
                    <label for="experience">Overall Experience:</label><br>
                    <div class="form-options">
                        <input type="radio" id="excellent4" name="experience" value="Excellent" required>
                        <label for="excellent4">Excellent</label><br>
                        <input type="radio" id="good4" name="experience" value="Good">
                        <label for="good4">Good</label><br>
                        <input type="radio" id="average4" name="experience" value="Average">
                        <label for="average4">Average</label><br>
                        <input type="radio" id="dissatisfied4" name="experience" value="Dissatisfied">
                        <label for="dissatisfied4">Dissatisfied</label>
                    </div>
                </div>

                <!-- Comments, Questions, or Suggestions -->
                <div class="form-group">
                    <label for="comments">Comments, Questions, or Suggestions:</label><br>
                    <textarea id="comments" name="comments" placeholder="Enter your feedback here..." required></textarea>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" value="Submit Feedback">
                </div>
            </form>
        </div>

        <!-- Thank You Message -->
        <div id="thankYouMessage" class="thank-you-message" style="display:none;">
            <img src="/assets/img/thanks.png" alt="Descriptive text" class="responsive-image">
            <h3>Thank you for your feedback!</h3>
        </div>

        <!-- Success Overlay -->
        <div id="overlay-success"
             style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0, 0, 0, 0.7); z-index:1000;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:5px; text-align:center;">
                <p id="overlay-message-success"></p>
            </div>
        </div>

        <div id="footer" style="height:200px">
            <div class="container">
                <div class="footer-logo-block">
                    <a href="https://www.facebook.com/slumuseum" class="footer-logo">
                        <img src="assets\img\logo.png" alt="Logo footer">
                    </a>
                    <p class="copyright" style="font-size: 15px">© 2024 SLU Museum of Igorot Cultures and Arts. All
                        Rights Reserved.</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

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
    <script type="text/javascript" src="res/js/client/about.js"></script>
    <script src="res/js/scripts.js"></script>
</body>
</html>