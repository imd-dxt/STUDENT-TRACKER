<?php
include 'database.php';
include 'sessionhandler.php';

header('Content-Type: application/json');

$student_id = $_SESSION['student_id'] ?? null; // Assuming `student_id` is stored in the session

if (!$student_id) {
    echo json_encode(['message' => 'Unauthorized access']);
    exit;
}

function fetchPerformanceData($conn, $student_id) {
    $stmt = $conn->prepare("SELECT id, title, subject, grade, submitted_at FROM assignments WHERE student_id = ?");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return ['message' => 'Database error'];
    }
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $performance_data = [];
    while ($row = $result->fetch_assoc()) {
        $performance_data[] = $row;
    }
    $stmt->close();
    return $performance_data;
}

function saveGrade($conn, $student_id, $data) {
    $stmt = $conn->prepare("INSERT INTO assignments (student_id, title, subject, grade, submitted_at) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return ['success' => false, 'message' => 'Database error'];
    }
    $stmt->bind_param("issds", $student_id, $data['title'], $data['subject'], $data['grade'], $data['submitted_at']);
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Grade submitted successfully'];
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to submit grade'];
    }
}

function deleteGrade($conn, $id) {
    global $student_id;  
    error_log("Attempting to delete assignment with id: $id for student_id: $student_id");
    $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ? AND student_id = ?");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return ['success' => false, 'message' => 'Database error'];
    }
    $stmt->bind_param("ii", $id, $student_id);
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Assignment deleted successfully'];
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to delete assignment'];
    }
}


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(fetchPerformanceData($conn, $student_id));
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['message' => 'Invalid input']);
        exit;
    }
    echo json_encode(saveGrade($conn, $student_id, $input));
} elseif ($method === 'DELETE') {
    error_log("DELETE request received");
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    if (!$id) {
        echo json_encode(['message' => 'Invalid input']);
        exit;
    }
    
    echo json_encode(deleteGrade($conn, $id));
} else {
    echo json_encode(['message' => 'Invalid request method']);
}

$conn->close();
?>
