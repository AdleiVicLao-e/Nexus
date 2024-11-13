<?php
include 'user-db.php';

// Prepare the SQL query to get the counts for each rating
$sql = "
    SELECT 
        'Quality/Presentation of Exhibits' AS category,
        SUM(CASE WHEN quality_presentation = 'Excellent' THEN 1 ELSE 0 END) AS excellent,
        SUM(CASE WHEN quality_presentation = 'Good' THEN 1 ELSE 0 END) AS good,
        SUM(CASE WHEN quality_presentation = 'Average' THEN 1 ELSE 0 END) AS average,
        SUM(CASE WHEN quality_presentation = 'Dissatisfied' THEN 1 ELSE 0 END) AS dissatisfied
    FROM feedback
    UNION
    SELECT 
        'Cleanliness and Ambiance',
        SUM(CASE WHEN cleanliness_ambiance = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN cleanliness_ambiance = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback
    UNION
    SELECT 
        'Museum Staff Service',
        SUM(CASE WHEN staff_service = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN staff_service = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback
    UNION
    SELECT 
        'Overall Experience',
        SUM(CASE WHEN overall_experience = 'Excellent' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Good' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Average' THEN 1 ELSE 0 END),
        SUM(CASE WHEN overall_experience = 'Dissatisfied' THEN 1 ELSE 0 END)
    FROM feedback
";

$result = $conn->query($sql);

$summary = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $summary[] = [
            'category' => $row['category'],
            'excellent' => $row['excellent'],
            'good' => $row['good'],
            'average' => $row['average'],
            'dissatisfied' => $row['dissatisfied']
        ];
    }
}

echo json_encode($summary);

$conn->close();
?>