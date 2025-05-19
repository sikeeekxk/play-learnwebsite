<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">Create New Quiz</h3>

            <form action="process_quiz_create.php" method="POST">
                <div class="mb-3">
                    <label for="quiz_title" class="form-label">Quiz Title</label>
                    <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
                </div>
                <div class="mb-3">
                    <label for="quiz_type" class="form-label">Quiz Type</label>
                    <select class="form-select" name="quiz_type" required>
                        <option value="pre">Pre-Test</option>
                        <option value="post">Post-Test</option>
                        <option value="practice">Practice</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create Quiz</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
