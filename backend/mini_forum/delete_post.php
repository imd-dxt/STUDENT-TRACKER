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

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

$student_id = $_SESSION['student_id'];

// Check if the logged-in student is the owner of the post
$stmt = $conn->prepare("SELECT student_id FROM forumposts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($owner_id);
$stmt->fetch();
$stmt->close();

if ($owner_id !== $student_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Delete the post
$stmt = $conn->prepare("DELETE FROM forumposts WHERE id = ? AND student_id = ?");
$stmt->bind_param("ii", $post_id, $student_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>