<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../database.php';
include '../sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $stmt = $conn->prepare("
        SELECT forumposts.id, forumposts.student_id, forumposts.content, forumposts.created_at, students.name 
        FROM forumposts 
        JOIN students ON forumposts.student_id = students.id 
        ORDER BY forumposts.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($posts);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>