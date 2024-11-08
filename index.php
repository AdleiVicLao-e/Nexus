<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Access as Guest</title>
    <link rel="stylesheet" href="res/css/styles.css" />
    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon" />
    <link href="assets/img/favicon.png" rel="icon" />
</head>

<body class="access-body">
    <div class="container">
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="Logo" class="logo" />
        </div>
        <div class="access-container">
            <form id="guest-form" onsubmit="return validateForm()">
                <div id="general-form">
                    <div class="input-container">
                        <p style="text-align: left; font-weight: lighter; font-size: 16px"> Name </p>
                        <input type="text" id="user_name" class="input-field" placeholder="First and last name"
                            required />
                    </div>
                    <div class="input-container" style="width: 300px">
                        <p style="text-align: left; font-weight: lighter; font-size: 16px"> School </p>
                        <select id="schoolSelect" class="input-field" onchange="showOtherInput()" required>
                            <option value="" disabled selected>Select School</option>
                            <option value="Basic Education School">Basic Education School</option>
                            <option value="School of Accountancy, Management, Computing and Information Studies">School
                                of Accountancy, Management, Computing and Information Studies</option>
                            <option value="School of Advanced Studies">School of Advanced Studies</option>
                            <option value="School of Engineering and Architecture">School of Engineering and
                                Architecture</option>
                            <option value="School of Law">School of Law</option>
                            <option value="School of Medicine">School of Medicine</option>
                            <option value="School of Nursing, Allied Health, and Biological Sciences">School of Nursing,
                                Allied Health, and Biological Sciences</option>
                            <option value="School of Teacher Education and Liberal Arts">School of Teacher Education and
                                Liberal Arts</option>
                            <option value="other">Others</option>
                        </select>
                        <!-- Hidden input field for entering custom school name -->
                        <div id="otherSchoolInput" style="display: none; margin-top: 10px">
                            <input type="text" id="otherSchoolName" class="input-field"
                                placeholder="Enter your school name" />
                        </div>
                    </div>
                </div>
                <div id="error-message" style="color: red; display: none; margin-top: 10px"> Please fill out all
                    required fields. </div>
                <button type="submit" class="btn-access" id="btn-submit"> Submit </button>
            </form>
        </div>
    </div>
    <div id="overlay" class="overlay">
        <div class="overlay-message" id="overlay-message">
        </div>
    </div>
    <script>
    // Show or hide the 'Other School' input field
    function showOtherInput() {
        var select = document.getElementById("schoolSelect");
        var otherInput = document.getElementById("otherSchoolInput");
        if (select.value === "other") {
            otherInput.style.display = "block"; // Show the input field
        } else {
            otherInput.style.display = "none"; // Hide the input field if "Others" is not selected
        }
    }

    // Validate form fields
    function validateForm() {
        var name = document.getElementById("user_name").value;
        var school = document.getElementById("schoolSelect").value;
        var otherSchool = document.getElementById("otherSchoolName").value;
        var otherSchoolInput = document.getElementById("otherSchoolInput").style.display;
        var errorMessage = document.getElementById("error-message");

        // Check if name is filled, school is selected, and otherSchool is filled if "Others" is selected
        if (name.trim() === "" || school === "" || (school === "other" && otherSchool.trim() === "")) {
            errorMessage.style.display = "block"; // Show error message
            return false; // Prevent form submission
        } else {
            errorMessage.style.display = "none"; // Hide error message
            return true; // Form is valid
        }
    }

    // Add event listener to the submit button
    document.getElementById("btn-submit").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default form submission

        if (validateForm()) {
            const userName = document.getElementById('user_name').value;
            const userSchool = document.getElementById('schoolSelect').value === 'other' ?
                document.getElementById('otherSchoolName').value :
                document.getElementById('schoolSelect').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../include/userLog.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        const overlay = document.getElementById('overlay');
                        const overlayMessage = document.getElementById('overlay-message');
                        overlayMessage.textContent = response.message;
                        overlay.style.display = 'flex';

                        setLocalStorageItem('guest', userName);

                        setTimeout(function() {
                            window.location.href = 'homepage.php';
                        }, 2000);
                    } else {
                        alert('Error: ' + response.message);
                    }
                } else {
                    // Handle error case here
                    alert('Error submitting the form. Server returned status: ' + xhr.status);
                }
            };


            xhr.onerror = function() {
                alert('An error occurred while submitting the form.');
            };

            // Send the data
            xhr.send(`user_name=${encodeURIComponent(userName)}&user_school=${encodeURIComponent(userSchool)}`);
        }
    });

    // Function to set an item with an expiration time
    function setLocalStorageItem(key, value) {
        const now = new Date();

        // Calculate the time left until midnight (end of the day)
        const midnight = new Date(now);
        midnight.setHours(23, 59, 59, 999); // Set time to 23:59:59.999 (end of the day)

        // Store the item with its expiration time (midnight)
        const item = {
            value: value,
            expiry: midnight.getTime() // Expiry set to the end of the current day (midnight)
        };

        localStorage.setItem(key, JSON.stringify(item));
    }

    // Function to get an item and check if it has expired
    function getLocalStorageItem(key) {
        const itemStr = localStorage.getItem(key);

        // If the item doesn't exist, return null
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = new Date();

        // If the item has expired (past midnight), remove it from localStorage and return null
        if (now.getTime() > item.expiry) {
            localStorage.removeItem(key); // Clean up expired item
            return null;
        }

        return item.value; // Return the value if not expired
    }
    </script>
    <script defer>
    const guestSession = getLocalStorageItem('guest');
    if (guestSession) {
        console.log("Guest logged in. Redirecting...");
        window.location.href = "homepage.php";
    }
    </script>
</body>

</html>