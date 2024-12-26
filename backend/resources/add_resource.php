<?php
include 'database.php';
include 'sessionhandler.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['resource']) && $_FILES['resource']['error'] == 0) {
        $fileName = $_FILES['resource']['name'];
        $fileTmpName = $_FILES['resource']['tmp_name'];
        $fileSize = $_FILES['resource']['size'];
        $fileType = $_FILES['resource']['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = array('pdf');

        if (in_array($fileExt, $allowed)) {
            $uploadDir = 'uploads/';
            $filePath = $uploadDir . basename($fileName);

            if (move_uploaded_file($fileTmpName, $filePath)) {
                $studentId = $_SESSION['student_id'];
                $title = $_POST['title'];
                $subject = $_POST['subject'];
                $uploadedAt = date('Y-m-d H:i:s');

                $sql = "INSERT INTO resources (student_id, title, subject, type, file_path, uploaded_at) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssss", $studentId, $title, $subject, $fileType, $filePath, $uploadedAt);

                if ($stmt->execute()) {
                    echo "Resource uploaded successfully.";
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Only PDF files are allowed.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
} else {
    echo "Invalid request method.";
}
?>