<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a date range is submitted
if (isset($_POST['datefilter']) && !empty($_POST['datefilter'])) {
    // Split the date range
    $dateRange = explode(' - ', $_POST['datefilter']);
    $startDate = date('Y-m-d', strtotime($dateRange[0])); // Convert to Y-m-d format
    $endDate = date('Y-m-d', strtotime($dateRange[1]));   // Convert to Y-m-d format

    // Modify the SQL query to filter between the selected dates
    $sql = "SELECT user_number, user_name, user_school, time 
            FROM user_log 
            WHERE DATE(time) BETWEEN '$startDate' AND '$endDate'";
} else {
    // Default SQL query (no filter)
    $sql = "SELECT user_number, user_name, user_school, time FROM user_log";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Visitor Number</th>
                <th>Visitor Name</th>
                <th>Visitor School</th>
                <th>Time</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["user_number"] . "</td>
                <td>" . $row["user_name"] . "</td>
                <td>" . $row["user_school"] . "</td>
                <td>" . $row["time"] . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No records found.";
}

$conn->close();
?>
