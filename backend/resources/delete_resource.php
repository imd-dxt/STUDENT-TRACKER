<?php
include 'database.php';
include 'sessionhandler.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['resource_id'])) {
        $resourceId = $_POST['resource_id'];

        
        $sql = "SELECT file_path FROM resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $resourceId);
        $stmt->execute();
        $stmt->bind_result($filePath);
        $stmt->fetch();
        $stmt->close();

        if ($filePath) {
           
            if (unlink($filePath)) {
                
                $sql = "DELETE FROM resources WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $resourceId);

                if ($stmt->execute()) {
                    echo "Resource deleted successfully.";
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Failed to delete the file.";
            }
        } else {
            echo "Resource not found.";
        }
    } else {
        echo "No resource ID provided.";
    }
} else {
    echo "Invalid request method.";
}
?>