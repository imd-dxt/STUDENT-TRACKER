<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('access-control-allow-methods: GET, POST, OPTIONS');
header('access-control-allow-headers: Content-Type');



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$content = $input['content'] ?? '';


if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Content cannot be empty']);
    exit;
}


if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];


$stmt = $conn->prepare("INSERT INTO forumposts (student_id, content, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $student_id, $content);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}


$stmt->close();
$conn->close();
?>