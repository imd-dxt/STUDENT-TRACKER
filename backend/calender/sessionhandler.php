<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['message' => 'User not logged in']);
    exit;
}
$student_id = $_SESSION['student_id'];
?>
