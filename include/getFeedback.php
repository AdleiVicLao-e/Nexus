<?php
include 'user-db.php';

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Updated SQL query to select all relevant fields
$sql = "SELECT date, quality_presentation, cleanliness_ambiance, staff_service, overall_experience, comments FROM feedback";

if ($startDate && $endDate) {
    $sql .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch each row from the result and display in the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td class='text-end'>" . htmlspecialchars($row['date']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['quality_presentation']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['cleanliness_ambiance']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['staff_service']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['overall_experience']) . "</td>
                <td class='text-end'>" . htmlspecialchars($row['comments']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No feedback available</td></tr>";
}

$conn->close();
?>
