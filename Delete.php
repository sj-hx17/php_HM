<?php
    if (isset($_GET["id"])){
        $id = $_GET["id"];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "sent_otp";

        $connection = new mysqli($servername, $username, $password, $dbname);

        $sql = "DELETE FROM otp WHERE id = $id";
        $connection->query($sql);
    }

    header("location: AdminDashboard.php");
    exit;
?>