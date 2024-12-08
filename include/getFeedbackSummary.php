<?php
include 'user-db.php';

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Prepare the base SQL query to get the counts for each rating
$sql = "
    SELECT 
        'Quality/Presentation of Exhibits' AS category,
        SUM(CASE WHEN quality_presentation = 'Excellent' THEN 1 ELSE 0 END) AS excellent,
        SUM(CASE WHEN quality_presentation = 'Good' THEN 1 ELSE 0 END) AS good,
        SUM(CASE WHEN quality_presentation = 'Average' THEN 1 ELSE 0 END) AS average,
        SUM(CASE WHEN quality_presentation = 'Dissatisfied' THEN 1 ELSE 0 END) AS dissatisfied
    FROM feedback";

// Add date filtering to the query if dates are provided
if ($startDate && $endDate) {
    $sql .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
}

$sql .= "
    UNION
    SELECT 
        'Cleanliness and Ambiance',
        SUM(CASE WHEN cleanliness_ambiance = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback";

// Add date filtering to subsequent queries as well
if ($startDate && $endDate) {
    $sql .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
}

$sql .= "
    UNION
    SELECT 
        'Museum Staff Service',
        SUM(CASE WHEN staff_service = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback";

if ($startDate && $endDate) {
    $sql .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
}

$sql .= "
    UNION
    SELECT 
        'Overall Experience',
        SUM(CASE WHEN overall_experience = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback";

// Add date filtering to the last query as well
if ($startDate && $endDate) {
    $sql .= " WHERE date BETWEEN '$startDate' AND '$endDate'";
}


$result = $conn->query($sql);

$summary = [];
$hasData = false;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['excellent'] || $row['good'] || $row['average'] || $row['dissatisfied']) {
            $hasData = true;
        }

        $summary[] = [
            'category' => $row['category'],
            'excellent' => $row['excellent'],
            'good' => $row['good'],
            'average' => $row['average'],
            'dissatisfied' => $row['dissatisfied']
        ];
    }
}

if ($hasData) {
    echo json_encode($summary);
} else {
    echo json_encode([
        [
            'category' => '',
            'excellent' => '<tr><td colspan="5" class="text-center">No feedback summary available</td></tr>',
            'good' => '',
            'average' => '',
            'dissatisfied' => ''
        ]
    ]);
}

$conn->close();
?>