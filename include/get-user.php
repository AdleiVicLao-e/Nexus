<?php
include 'user-db.php';

// SQL query to fetch data from user_log table
$sql = "SELECT user_number, user_name, user_school, time FROM user_log ORDER BY user_number ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'>
            <tr>
                <th>Visitor Number</th>  <!-- Change from ID to User Number -->
                <th>Visitor Name</th>
                <th>Visitor School</th>
                <th>Time</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["user_number"] . "</td>  <!-- Change from id to user_number -->
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
