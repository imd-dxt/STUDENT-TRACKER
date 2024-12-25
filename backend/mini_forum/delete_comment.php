<?php
include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);
$comment_id = $input['comment_id'] ?? null;

if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid comment ID']);
    exit;
}

$student_id = $_SESSION['student_id'];

// Check if the logged-in student is the owner of the comment
$stmt = $conn->prepare("SELECT student_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($owner_id);
$stmt->fetch();
$stmt->close();

if ($owner_id !== $student_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Delete the comment
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND student_id = ?");
$stmt->bind_param("ii", $comment_id, $student_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>