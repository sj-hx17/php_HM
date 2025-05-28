<?php
    session_start();
    include("config.php");

    header('Content-Type: application/json');

    $query = "SELECT * FROM laf WHERE status = 'Lost' ORDER BY date DESC";
    $result = $conn->query($query);

    $lostItems = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lostItems[] = $row;
        }
    }

    echo json_encode($lostItems);
    $conn->close();
?>