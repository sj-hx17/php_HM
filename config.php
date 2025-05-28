<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "sent_otp";

    date_default_timezone_set('Asia/Manila');

    $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>