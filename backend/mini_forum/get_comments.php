<?php 
/*include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['message' => 'Invalid post ID']);
    exit;
}

$stmt = $conn->prepare("SELECT id, student_id, content, created_at FROM comments WHERE post_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($comments);*/

include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['message' => 'Invalid post ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT comments.id, comments.student_id, comments.content, comments.created_at, students.name 
    FROM comments 
    JOIN students ON comments.student_id = students.id 
    WHERE comments.post_id = ? 
    ORDER BY comments.created_at ASC
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($comments);
?>
