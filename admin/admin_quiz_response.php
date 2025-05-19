<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$message = '';

// Fetch all quizzes for dropdown
$quizzes = [];
$result = $conn->query("SELECT * FROM quizzes ORDER BY title ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
} else {
    $message = '<div class="alert alert-danger">Error fetching quizzes: ' . $conn->error . '</div>';
}

// Check if a quiz has been selected
$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$users = [];

if ($quizId) {
    $stmt = $conn->prepare("
        SELECT DISTINCT u.id AS user_id, u.username AS user_name
        FROM quiz_responses qr
        JOIN users u ON qr.user_id = u.id
        WHERE qr.quiz_id = ?
        ORDER BY u.username ASC
    ");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = '<div class="alert alert-danger">Error fetching users: ' . $conn->error . '</div>';
    }
}

// Handle quiz selection form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    $quizId = (int)$_POST['quiz_id'];
    header("Location: admin_quiz_response.php?quiz_id=$quizId");
    exit();
}
?>

<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">View Quiz Responses</h3>
            <?= $message ?>

            <!-- Quiz Selection Form -->
            <div class="card mb-4">
                <div class="card-header"><h5>Select a Quiz</h5></div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label for="quiz_id" class="form-label">Choose a Quiz</label>
                            <select class="form-select" id="quiz_id" name="quiz_id" required>
                                <option value="" disabled <?= $quizId ? '' : 'selected' ?>>Select a quiz</option>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <option value="<?= $quiz['id'] ?>" <?= $quiz['id'] == $quizId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($quiz['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">View Responses</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($quizId): ?>
                <div class="card">
                    <div class="card-header">
                        <h5>Users who took Quiz: 
                            <?= htmlspecialchars($quizzes[array_search($quizId, array_column($quizzes, 'id'))]['title']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($users): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>User</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['user_name']) ?></td>
                                                <td>
                                                    <a href="admin_view_response.php?quiz_id=<?= $quizId ?>&user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        View All Answers
                                                    </a>
                                                    <a href="admin_grade_essay.php?quiz_id=<?= $quizId ?>&user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-warning ms-2">
                                                        Grade Essays
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No users have taken this quiz yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

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
    .btn-outline-warning {
        color: #856404;
        border-color: #ffeeba;
    }
    .btn-outline-warning:hover {
        background-color: #ffeeba;
        color: #856404;
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
    .ms-2 {
        margin-left: 0.5rem !important;
    }
</style>
