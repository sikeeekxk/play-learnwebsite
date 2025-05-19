<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../db_connect.php';

// Make sure we have a connection - use $conn from db_connect.php
if (!isset($conn)) {
    die("Database connection not established. Check db_connect.php file.");
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid announcement ID";
    header("Location: admin_announcement_form.php");
    exit();
}

$announcement_id = $_GET['id'];

// Delete the announcement
$stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->bind_param("i", $announcement_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "Announcement deleted successfully!";
} else {
    $_SESSION['error'] = "Announcement not found or already deleted!";
}
$stmt->close();

// Redirect back to the announcement management page
header("Location: admin_announcement_form.php");
exit();
?>