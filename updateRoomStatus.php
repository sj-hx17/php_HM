<?php
    session_start();
    if (!isset($_SESSION['name'])) {
        header("Location: FrontdeskLogin.php");
        exit();
    }

    $connect = new mysqli('localhost', 'root', '', 'sent_otp');
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roomId = intval($_POST['id']);
        
        // Update the room status to 'Needs Cleaning' and clear the guest name
        $sql = "UPDATE room SET access = 'Needs Cleaning', name = NULL WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $roomId);
        
        if ($stmt->execute()) {
            echo "Room updated successfully.";
        } else {
            echo "Error updating room: " . $connect->error;
        }
        
        $stmt->close();
    }
    
    $connect->close();
?>
