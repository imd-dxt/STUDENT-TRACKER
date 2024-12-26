<?php
include 'database.php';

$sql = "SELECT id, student_id, title, subject, type, file_path, uploaded_at FROM resources";
$result = $conn->query($sql);

$resources = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($resources);
?>