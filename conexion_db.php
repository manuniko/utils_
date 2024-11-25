<?php

if (!isset($conn)) {
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $db = "2do_parcial";

    $conn = new PDO("mysql:host=" . $servername . ";dbname=" . $db . ";charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
