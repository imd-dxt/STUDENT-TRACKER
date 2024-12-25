<?php
include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);
$post_id = $input['post_id'] ?? null;
$content = $input['content'] ?? '';

if (!$post_id || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("INSERT INTO comments (post_id, student_id, content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $post_id, $student_id, $content);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>