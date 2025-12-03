<?php

function getConnection(){


    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "gamify_todo";
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Database connection failed: ' . $conn->connect_error));
        exit;
    } else {
        return $conn;
    }
}