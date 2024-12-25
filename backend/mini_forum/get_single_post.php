<?php
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    echo json_encode(['message' => 'Invalid post ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT forumposts.id, forumposts.student_id, forumposts.content, forumposts.created_at, students.name 
    FROM forumposts 
    JOIN students ON forumposts.student_id = students.id 
    WHERE forumposts.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$post = $result->fetch_assoc();

$stmt->close();
$conn->close();

if ($post) {
    echo json_encode($post);
} else {
    echo json_encode(['message' => 'Post not found']);
}
?>