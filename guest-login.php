<?php
if (isset($_SESSION["guest"])) {
  header("Location: ../scanner.php");
}
$_SESSION["guest"] = "guest";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Access as Guest</title>
    <link rel="stylesheet" href="res/css/styles.css" />
    <!-- Include Font Awesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
    href="https://fonts.googleapis.com/css?family=Inter"
    rel="stylesheet"
  />
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon" />
    <link href="assets/img/favicon.png" rel="icon" />
  </head>
  <body class="access-body">
    <div class="container">
      <div class="logo-container">
        <img src="assets/img/logo.png" alt="Logo" class="logo" />
      </div>
      <!-- Back button container -->
      <div class="back-button-container">
        <a href="index.php">
          <i
            class="fa fa-chevron-circle-left"
            aria-hidden="true"
            style="color: #ffffff; font-size: x-large"
          ></i>
        </a>
      </div>
      <div class="access-container">
        <form id="guest-form" onsubmit="return validateForm()">
          <div id="general-form">
            <div class="input-container">
              <p
                style="text-align: left; font-weight: lighter; font-size: 16px"
              >
                Name
              </p>
              <input
                type="text"
                id="user_name"
                class="input-field"
                placeholder="First and last name"
                required
              />
            </div>
            <div class="input-container" style="width: 300px">
              <p
                style="text-align: left; font-weight: lighter; font-size: 16px"
              >
                School
              </p>
              <select
                id="schoolSelect"
                class="input-field"
                onchange="showOtherInput()"
              >
                <option value="" disabled selected>Select School</option>
                <option value="school1">Basic Education School</option>
                <option value="school2">
                  School of Accountancy, Management, Computing and Information
                  Studies
                </option>
                <option value="school3">School of Advanced Studies</option>
                <option value="school4">
                  School of Engineering and Architecture
                </option>
                <option value="school5">School of Law</option>
                <option value="school6">School of Medicine</option>
                <option value="school7">
                  School of Nursing, Allied Health, and Biological Sciences
                </option>
                <option value="school8">
                  School of Teacher Education and Liberal Arts
                </option>
                <option value="other">Others</option>
              </select>

              <!-- Hidden input field for entering custom school name -->
              <div
                id="otherSchoolInput"
                style="display: none; margin-top: 10px"
              >
                <input
                  type="text"
                  id="otherSchoolName"
                  class="input-field"
                  placeholder="Enter your school name"
                />
              </div>
            </div>
          </div>
          <div
            id="error-message"
            style="color: red; display: none; margin-top: 10px"
          >
            Please fill out all required fields.
          </div>
          <button type="submit" class="btn-access" id="btn-submit">
            Submit
          </button>
        </form>
      </div>
    </div>

    <div id="desktop-warning">Mobile Only Site.</div>

    <script>
      function showOtherInput() {
        var select = document.getElementById("schoolSelect");
        var otherInput = document.getElementById("otherSchoolInput");

        if (select.value === "other") {
          otherInput.style.display = "block"; // Show the input field
        } else {
          otherInput.style.display = "none"; // Hide the input field if "Others" is not selected
        }
      }

      function validateForm() {
        var name = document.getElementById("user_name").value;
        var school = document.getElementById("schoolSelect").value;
        var otherSchool = document.getElementById("otherSchoolName").value;
        var otherSchoolInput =
          document.getElementById("otherSchoolInput").style.display;
        var errorMessage = document.getElementById("error-message");

        // Check if name is filled, school is selected, and otherSchool is filled if "Others" is selected
        if (
          name.trim() === "" ||
          school === "" ||
          (school === "other" && otherSchool.trim() === "")
        ) {
          errorMessage.style.display = "block"; // Show error message
          return false; // Prevent form submission
        } else {
          errorMessage.style.display = "none"; // Hide error message

          // Redirect to scanner.php
          window.location.href = "scanner.php";
          return false; // Prevent the default form action to avoid page reload
        }
      }

      function checkDevice() {
        // Define a breakpoint for mobile devices (e.g., 768px)
        if (window.innerWidth > 768) {
          // Show the overlay first
          document.getElementById("desktop-warning").style.display = "flex";

          // Delay the alert to ensure the overlay is visible first
          setTimeout(function () {
            alert("Please use your mobile phone to access this site.");
          }, 100); // Adjust the delay (in ms) if necessary
        }
      }

      // Add event listener to the submit button
      document
        .getElementById("btn-submit")
        .addEventListener("click", function (event) {
          // Prevent default form submission (optional, if this is part of a form)
          event.preventDefault();

          // Check the device size when the user clicks Submit
          validateForm();
          checkDevice();
        });
    </script>
  </body>
</html>
