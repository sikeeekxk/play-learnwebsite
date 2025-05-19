<?php
session_start();
require_once '../db_connection.php';

// Check if admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include layout
include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$message = '';

// Get quiz ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $message = '<div class="alert alert-danger">Invalid quiz ID.</div>';
} else {
    $quiz_id = (int)$_GET['id'];

    // Check if quiz exists
    $check = $conn->prepare("SELECT id FROM quizzes WHERE id = ?");
    $check->bind_param("i", $quiz_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $message = '<div class="alert alert-warning">Quiz not found.</div>';
    } else {
        // Delete the quiz
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $quiz_id);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Quiz deleted successfully.</div>';
        } else {
            $message = '<div class="alert alert-danger">Error deleting quiz: ' . $conn->error . '</div>';
        }
    }
}
?>

<!-- Admin Content -->
<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Delete Quiz</h3>
            <?= $message ?>
            <a href="admin_quiz_list.php" class="btn btn-primary">← Back to Quiz List</a>
        </div>
    </div>
</div>

<!-- Inline CSS for consistency -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
    }
    .content { margin-top: 80px; }
    .card-header { background-color: #6c757d; color: white; font-weight: bold; }
    .btn-primary { background-color: #007bff; border-color: #007bff; }
    .btn-primary:hover { background-color: #0056b3; border-color: #004085; }
    .form-control { border-radius: 0.25rem; padding: 0.75rem; font-size: 1rem; }
    .table th, .table td { padding: 12px; }
    .table-hover tbody tr:hover { background-color: #f1f1f1; }
    .btn-outline-danger:hover, .btn-outline-primary:hover { color: white; }
</style>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
