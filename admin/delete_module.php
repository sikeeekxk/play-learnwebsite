<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "plurn"; // Change this

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($moduleId > 0) {
    $stmt = $conn->prepare("DELETE FROM modules WHERE id = ?");
    $stmt->bind_param("i", $moduleId);
    $stmt->execute();
}

header("Location: manage_modules.php");
exit();
