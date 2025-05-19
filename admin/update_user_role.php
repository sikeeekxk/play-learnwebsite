<?php
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once __DIR__ . '/../db_connect.php';

// Check if $pdo is properly defined
if (!isset($pdo) || $pdo === null) {
    // If $pdo is not defined, attempt to create the connection
    try {
        // You may need to adjust these values based on your actual database configuration
        $host = 'localhost';
        $dbname = 'lala';
        $username = 'root';
        $password = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $new_role = $_POST['new_role'] ?? '';

    if (!$user_id || !in_array($new_role, ['user', 'admin'])) {
        $_SESSION['error'] = "Invalid input data";
        header("Location: admin_user_roles.php");
        exit();
    }

    try {
        // Prepare the update statement
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->bindParam(':role', $new_role, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Role updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update role";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: admin_user_roles.php");
    exit();
}

// If not a POST request, redirect back
header("Location: admin_user_roles.php");
exit();
?>