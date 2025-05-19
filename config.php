<?php
// config.php

$host = 'localhost'; // database host
$user = 'root'; // database username
$password = ''; // database password
$dbname = 'plurn'; // database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
