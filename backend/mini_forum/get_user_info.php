<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (isset($_SESSION['student_id'])) {
    echo json_encode(['student_id' => $_SESSION['student_id']]);
} else {
    echo json_encode(['message' => 'Not logged in']);
}
?>