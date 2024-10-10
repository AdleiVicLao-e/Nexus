<?php
session_start();
if (isset($_SESSION["admin"])) {
  echo '<script>
    console.log("User already logged in. Redirecting...");
    </script>';
} else {
  echo '<script>
    alert("Not logged in. Redirected to Admin Login.");
    window.location.href="admin-login.php";
    </script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../res/css/adminStyle.css">
    <link rel="stylesheet" href="../res/css/datePicker.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
    <script src="../res/js/admin/javascript.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Saint Louis University Logo"
                style="width: 150px; height: auto; margin-left: 40px;">
            <h3 class="dashboardTitle" style="margin-left: 30px;"> Admin Dashboard</h3>
        </div>
        <div class="greetings">
            <img src="../res/images/user-image.png" alt="User Icon" aria-hidden="true">
            <div class="greeting" style="margin-right: 30px;">
                <div class="curator">Hi, Curator!</div>
                <nav>
                    <a href="../include/logout" aria-label="Logout">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="main-container">
        <div class="left-container">
            <div class="analytics">
                <h1>Visitor Analytics</h1>
                <h3>Visitor by School</h3>
                <canvas id="donutChart" width="1000" height="300"></canvas>
                <h3>Visitor Log Book</h3>
                <input type="text" name="datefilter" value="" placeholder="Choose Date Range"/>
                <div id="userTableContainer"
                    <table id="visitorAnalyticsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>School</th>
                                <th>Date of Visit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamically populated rows -->
                        </tbody>
                    </table>
                </div>
                <button id="refreshBtn">Refresh Data</button>
            </div>
        </div>
        <div class="right-container">
            <div class="artifacts">
                <div class="tabs">
                    <div class="tab active" onclick="openTab('search')">Search Artifact</div>
                    <div class="tab" onclick="openTab('add')">Add Artifact</div>
                    <div class="tab" onclick="openTab('add2')">Add Category</div>
                    <div class="tab" onclick="openTab('upload')">Upload Media</div>
                    <div class="tab" onclick="openTab('edit')">Edit Media</div>
                </div>
                <div id="search" class="tab-content active" style="background-color: #ffffff;">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search artifact..."
                            oninput="searchArtifact()">
                        <button class="search-button" onclick="searchArtifact()">Search</button>
                    </div>
                    <button id="toggle-multi-select" onclick="toggleMultiSelect()">Enable Multi-Select</button>
                    <button id="delete-selected-button" style="display: none;"
                        onclick="deleteSelectedArtifacts()">Delete Selected</button>
                    <div id="search-results"></div>
                </div>
                <!-- Edit Popup Window -->
                <div id="edit-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-button" onclick="closeModal()">&times;</span>
                        <h2>Edit Artifact</h2>
                        <form id="edit-form">
                            <input type="hidden" id="artifact-id">
                            <label for="artifact-id">Artifact ID:</label>
                            <span id="artifact-id-display"></span>
                            <br>
                            <label for="editName">Name:</label>
                            <input type="text" id="editName" name="name">
                            <br>
                            <label for="editSection">Section:</label>
                            <select id="editSection" name="section" onchange="fetchCatalogs(this.value)"></select>
                            <br>
                            <label for="editCatalog">Catalogue:</label>
                            <select id="editCatalog" name="catalog" onchange="fetchSubcatalogs(this.value)"></select>
                            <br>
                            <label for="editSubcatalog">Subcatalogue:</label>
                            <select id="editSubcatalog" name="subcatalog"></select>
                            <br>
                            <label for="description">Description:</label>
                            <textarea id="editDescription" name="description"></textarea>
                            <br>
                            <label for="description">Virtual Avatar Script:</label>
                            <textarea id="editScript" name="script"></textarea>
                            <br>
                            <button type="button" onclick="saveChanges()">Save</button>
                            <button id="delBtn" type="button"
                                onclick="deleteArtifact(document.getElementById('artifact-id').value)">Delete</button>
                        </form>
                    </div>
                </div>
                <div id="add" class="tab-content" style="background-color: #ffffff; margin-top: -20px;">
                    <div class="form-container">
                        <form action="../include/addArtifact.php" method="post">
                            <div class="form-group">
                                <label for="artifact-name">Artifact Name:</label>
                                <input type="text" id="artifact-name" name="artifact-name" required>
                            </div>
                            <div class="form-group">
                                <label for="section">Section:</label>
                                <select id="section" name="section" required></select>
                            </div>
                            <div class="form-group">
                                <label for="catalog">Catalog:</label>
                                <select id="catalog" name="catalog" required></select>
                            </div>
                            <div class="form-group">
                                <label for="sub-catalog">Sub Catalog:</label>
                                <select id="sub-catalog" name="sub-catalog" required></select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="condition">Condition:</label>
                                <textarea id="condition" name="condition" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="script">Virtual Avatar Script:</label>
                                <textarea id="script" name="script" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="media-upload">Upload Media:</label>
                                <input type="file" id="media-upload" name="media-upload">
                            </div>
                            <button type="submit">Add Artifact</button>
                        </form>
                    </div>
                </div>

                <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0, 0, 0, 0.7); z-index:1000;">
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:5px; text-align:center;">
                        <p id="overlay-message"></p>
                        <div id="qrcode" style="margin-top: 20px;"></div>
                        <button id="close-overlay">Okay</button>
                    </div>
                </div>
                <div id="add2" class="tab-content">
                    <div class="form-container">
                        <form action="/include/addCategory.php" method="post">
                            <!--add database here i think??? .php, action="-here-" "/include/addArtifact.php"-->
                            <h3 style="text-align: center;">Add New Section</h3>
                            <!-- Form for Adding New Section -->
                            <div class="form-group">
                                <label for="create-new-section">New Section Title:</label>
                                <input type="text" id="create-new-section" name="new_section">
                            </div>
                            <div class="button-container">
                                <button type="submit" name="action" value="create_section" class="btn">Create
                                    Section</button>
                            </div>
                            <h1> </h1>
                            <hr>
                            <!-- Form for Adding New Catalog -->
                            <h3 style="text-align: center;">Add New Catalog</h3>
                            <div class="form-group">
                                <label for="create-select-section">Choose Section:</label>
                                <select id="create-select-section" name="section_id">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="create-new-catalog">New Catalog Name:</label>
                                <input type="text" id="create-new-catalog" name="new_catalog">
                            </div>
                            <div class="button-container">
                                <button type="submit" name="action" value="create_catalog" class="btn">Create
                                    Catalog</button>
                            </div>
                            <h1> </h1>
                            <hr>
                            <!-- Form for Adding New Sub Catalog -->
                            <h3 style="text-align: center;">Add New Subcatalog</h3>
                            <div class="form-group">
                                <label for="create-select-catalog">Choose Catalog:</label>
                                <select id="create-select-catalog" name="catalogue_id">
                                    <option value="">Select Catalog</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="create-new-subcatalog">New Subcatalog Name:</label>
                                <input type="text" id="create-new-subcatalog" name="new_subcatalog">
                            </div>

                            <div class="button-container">
                                <button type="submit" name="action" value="create_subcatalog" class="btn">Create
                                    Subcatalog</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="upload" class="tab-content" style="background-color: #ffffff; margin-top: -20px;">
                    <br>
                    <h3>Upload Media</h3>
                    <form action="../include/uploadMedia.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="media-upload">Select Media:</label>
                            <input type="file" id="media-upload" name="media-upload" required>
                        </div>
                        <button type="submit">Upload Media</button>
                    </form>
                </div>
                <div id="edit" class="tab-content" style="background-color: #ffffff; margin-top: -20px;">
                    <br>
                    <h3>Edit Media</h3>
                    <form action="/include/editMedia.php" method="post">
                        <div class="form-group">
                            <label for="media-name">Media Name:</label>
                            <input type="text" id="media-name" name="media-name" required>
                        </div>
                        <div class="form-group">
                            <label for="media-description">Media Description:</label>
                            <textarea id="media-description" name="media-description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="media-file">Select New Media:</label>
                            <input type="file" id="media-file" name="media-file">
                        </div>
                        <button type="submit">Edit Media</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/res/js/admin/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Chart.js library -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const userTableContainer = document.getElementById("userTableContainer");
        const refreshBtn = document.getElementById("refreshBtn");
        // Function to fetch and display user data
        function fetchUserData() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "../include/get-user.php", true); // Adjusted path to point to the include folder
            xhr.onload = function() {
                if (this.status === 200) {
                    userTableContainer.innerHTML = this
                    .responseText; // Populate the container with the response
                    createChart(); // Call the chart creation function after fetching user data
                } else {
                    userTableContainer.innerHTML = " < p > Error fetching data. < /p>";
                }
            };
            xhr.onerror = function() {
                userTableContainer.innerHTML = " < p > Request failed. < /p>";
            };
            xhr.send();
        }
        async function fetchData() {
            try {
                const response = await fetch('../include/chart.php'); // Adjust the path
                const data = await response.json();
                if (!data.error) {
                    const ctx = document.getElementById('donutChart').getContext('2d');
                    const donutChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.schools,
                            datasets: [{
                                label: 'User Count by School',
                                data: data.counts,
                                backgroundColor: ["#ea5545", 
                                    "#f46a9b", 
                                    "#ef9b20", 
                                    "#edbf33", 
                                    "#ede15b", 
                                    "#bdcf32", 
                                    "#87bc45", 
                                    "#27aeef", 
                                    "#b33dc6", // Color 9
                                    "#c94800", "#22beb6", "#727900"
                                ],
                                borderColor: 'white',
                                borderWidth: 2,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom', // Position legend at the bottom
                                    align: 'start', // Align legend to the left
                                    labels: {
                                        font: {
                                            family: "Inter, serif",
                                            size: 12,
                                            weight: 'bold',
                                            color: '#333',
                                        },
                                        usePointStyle: true,
                                    },
                                },
                                title: {
                                    display: true,
                                    font: {
                                        family: "Inter, serif",
                                        size: 14,
                                        weight: 'bold',
                                        color: '#333',
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20,
                                    },
                                },
                                tooltip: {
                                    titleFont: {
                                        family: "Inter, serif",
                                        size: 12,
                                        weight: 'bold',
                                        color: '#fff',
                                    },
                                    bodyFont: {
                                        family: "Inter, serif",
                                        size: 10,
                                        color: '#fff',
                                    },
                                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                    borderColor: 'white',
                                    borderWidth: 1,
                                    padding: 10,
                                },
                            }
                        }
                    });
                } else {
                    console.error(data.error);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
        fetchData();
        // Event listener for refresh button
        refreshBtn.addEventListener("click", fetchUserData);
        // Fetch user data on initial load
        fetchUserData();
        // Function to fetch section data for a dropdown
        function fetchSectionData() {
            fetch('/include/get.php') // Adjust the path if necessary
                .then(response => response.json()).then(data => {
                    const sectionSelect = document.getElementById("create-select-section");
                    const sections = data.sections;
                    sections.forEach(section => {
                        const option = document.createElement("option");
                        option.value = section.section_id;
                        option.text = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                }).catch(error => {
                    console.error("Error fetching sections:", error);
                });
        }
        // Function to fetch catalog data for a dropdown
        function fetchCatalogData() {
            fetch('/include/get.php') // Adjust the path if necessary
                .then(response => response.json()).then(data => {
                    const catalogSelect = document.getElementById("create-select-catalog");
                    const catalogues = data.catalogues;
                    catalogues.forEach(catalog => {
                        const option = document.createElement("option");
                        option.value = catalog.catalogue_id;
                        option.text = catalog.catalogue_name;
                        catalogSelect.appendChild(option);
                    });
                }).catch(error => {
                    console.error("Error fetching catalogues:", error);
                });
        }
        // Consolidate DOMContentLoaded
        fetchSectionData(); // Fetch and populate sections in the dropdown
        fetchCatalogData(); // Fetch and populate catalogues in the dropdown
    });
    </script>
    <script type="text/javascript">
        $(function() {

        $('input[name="datefilter"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        });
        </script>
</body>

</html>