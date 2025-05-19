<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php'; // Make sure this file contains your PDO config

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

// Handle form submission to add quiz
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_code = $_POST['exam_code'];
    $quiz_type = $_POST['quiz_type'];
    $title = $_POST['title'];
    $created_by = $_SESSION['username']; // assuming quiz creator is current admin

    $stmt = $pdo->prepare("INSERT INTO quizzes (exam_code, quiz_type, title, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$exam_code, $quiz_type, $title]);
}
?>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Create New Quiz</h3>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Exam Code</label>
                        <input type="text" name="exam_code" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quiz Type</label>
                        <select name="quiz_type" class="form-select" required>
                            <option value="pre-test">Pre-Test</option>
                            <option value="post-test">Post-Test</option>
                            <option value="practice">Practice</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Quiz</button>
            </form>
        </div>

        <div class="card shadow-sm p-4">
            <h3 class="mb-3">All Quizzes</h3>
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Exam Code</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC");
                    while ($row = $stmt->fetch()) {
                        echo "<tr>
                                <td>{$row['exam_code']}</td>
                                <td>{$row['title']}</td>
                                <td>{$row['quiz_type']}</td>
                                <td>{$row['created_at']}</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
