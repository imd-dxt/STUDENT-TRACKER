<?php
header('Content-Type: application/json');
session_start();

// Check if the student_id is set in the session
if (!isset($_SESSION['student_id'])) {
    // If not logged in, return a response indicating the user is not logged in
    echo json_encode(['message' => 'User not logged in']);
    exit;
}

// Fetch the student_id from the session
$student_id = $_SESSION['student_id'];

// Return the student_id as a JSON response
echo json_encode(['student_id' => $student_id]);
?>
//