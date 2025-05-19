<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php'; // Adjust if needed

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_quiz'])) {
        $exam_code = trim($_POST['exam_code']);
        $title = trim($_POST['title']);
        $quiz_type = $_POST['quiz_type'];

        if ($exam_code === '' || $title === '') {
            $message = '<div class="alert alert-danger">Exam code and title are required.</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO quizzes (exam_code, title, quiz_type, published, created_at) VALUES (?, ?, ?, 0, NOW())");
            $stmt->execute([$exam_code, $title, $quiz_type]);
            header("Location: admin_quiz_list.php");
            exit();
        }
    }

    if (isset($_POST['publish_action'])) {
        $quiz_id = $_POST['quiz_id'];
        $action = $_POST['publish_action'];
        $published = ($action === 'publish') ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE quizzes SET published = ? WHERE id = ?");
        $stmt->execute([$published, $quiz_id]);
        header("Location: admin_quiz_list.php");
        exit();
    }
}

// Fetch quizzes
$stmt = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC");
$quizzes = $stmt->fetchAll();
?>

<!-- Admin Content -->
<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">📝 Add New Quiz</h3>
            <?= $message ?>
            <form method="POST" action="admin_quiz_list.php">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Exam Code</label>
                        <input type="text" name="exam_code" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quiz Type</label>
                        <select name="quiz_type" class="form-select" required>
                            <option value="pre-test">Prelims</option>
                            <option value="post-test">Midterms</option>
                            <option value="practice">Finals</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="add_quiz" class="btn btn-primary w-100">Add Quiz</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow-sm p-4">
            <h3 class="mb-3">📋 Manage Quizzes</h3>
            <p>View, edit, publish, or delete quizzes below.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Exam Code</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($quizzes)): ?>
                            <?php foreach ($quizzes as $row): ?>
                                <?php
                                $status_badge = $row['published']
                                    ? '<span class="badge bg-success">Published</span>'
                                    : '<span class="badge bg-danger">Unpublished</span>';

                                $publish_button = $row['published']
                                    ? '<form method="POST" style="display:inline;">
                                            <input type="hidden" name="quiz_id" value="'.htmlspecialchars($row['id']).'">
                                            <input type="hidden" name="publish_action" value="unpublish">
                                            <button type="submit" class="btn btn-sm btn-warning">Unpublish</button>
                                       </form>'
                                    : '<form method="POST" style="display:inline;">
                                            <input type="hidden" name="quiz_id" value="'.htmlspecialchars($row['id']).'">
                                            <input type="hidden" name="publish_action" value="publish">
                                            <button type="submit" class="btn btn-sm btn-success">Publish</button>
                                       </form>';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['exam_code']) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['quiz_type']) ?></td>
                                    <td><?= $status_badge ?></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td>
                                        <?= $publish_button ?>
                                        <a href="edit_quiz.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="delete_quiz.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No quizzes found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

<!-- Styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
    }
    .content.main {
        margin-left: 240px;
        padding: 80px 30px 30px 30px;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .card-header {
        background-color: #6c757d;
        color: white;
        font-weight: bold;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
    .form-control, .form-select {
        border-radius: 0.25rem;
        padding: 0.75rem;
        font-size: 1rem;
    }
    .table th, .table td {
        padding: 12px;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-outline-danger:hover,
    .btn-outline-primary:hover {
        color: white;
    }
</style>
