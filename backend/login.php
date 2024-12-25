<?php
include ('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();
        $isPasswordValid = password_verify($password, $hashed_password);

        if ($isPasswordValid) {
            session_start();
            $_SESSION['user_name'] = $name;
            $_SESSION['student_id'] = $id;
            header('Location: ../frontend/dashboard.html');
            exit();
        } else {
            echo json_encode(['message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['message' => 'User not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['message' => 'Invalid request method']);
}
?>