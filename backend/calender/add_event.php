<?php
include 'database.php';
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['student_id'])) {
            throw new Exception('Student ID not set in session');
        }

        $student_id = $_SESSION['student_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $event_date = $_POST['event_date'];
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO eventcalendars (student_id, title, description, event_date, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $student_id, $title, $description, $event_date, $created_at);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>