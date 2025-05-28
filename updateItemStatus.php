<?php
    session_start();
    if (!isset($_SESSION['name'])) {
        die("Unauthorized access");
    }

    // Database connection
    $connect = new mysqli('localhost', 'root', '', 'sent_otp');
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        if ($id > 0 && !empty($status)) {
            $stmt = $connect->prepare("UPDATE laf SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            
            if ($stmt->execute()) {
                echo "Status updated successfully";
            } else {
                echo "Error updating status: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Invalid parameters";
        }
    } else {
        echo "Invalid request method";
    }

    $connect->close();
?>