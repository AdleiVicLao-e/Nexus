<?php
include 'user-db.php';

// Get start and end date from the query parameters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Base SQL query
$sql = "SELECT user_number, user_name, user_school, time FROM user_log";

// Add date filters if provided
if ($startDate && $endDate) {
    $sql .= " WHERE DATE(time) BETWEEN '$startDate' AND '$endDate'";
}

$sql .= " ORDER BY user_number ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Visitor Number</th>
                <th>Visitor Name</th>
                <th>Visitor School</th>
                <th>Time</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
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