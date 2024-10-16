<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT rating, message FROM feedback";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td class='text-end'>" . htmlspecialchars($row['rating']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['message']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='2' class='text-center'>No feedback available</td></tr>";
}

$conn->close();
?>
