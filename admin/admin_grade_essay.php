<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';

$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (!$quizId || !$userId) {
    $_SESSION['error'] = "Quiz ID and User ID are required.";
    header("Location: admin_quiz_response.php");
    exit();
}

// Handle grading form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $responseId = (int)$_POST['response_id'];
    $score = floatval($_POST['score']);
    $correct = isset($_POST['correct']) ? (int)$_POST['correct'] : 0;

    $updateStmt = $conn->prepare("UPDATE quiz_responses SET score = ?, correct = ?, graded = 1, is_graded = 1 WHERE id = ?");
    $updateStmt->bind_param("dii", $score, $correct, $responseId);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Essay grading updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update grading: " . $conn->error;
    }

    header("Location: admin_grade_essay.php?quiz_id=$quizId&user_id=$userId");
    exit();
}

// Fetch essay responses for this quiz and user
$stmt = $conn->prepare("
    SELECT 
        qr.id AS response_id,
        qr.answer,
        qr.score,
        qr.correct,
        q.question_text,
        q.score AS max_score
    FROM quiz_responses qr
    JOIN questions q ON qr.question_id = q.id
    WHERE qr.quiz_id = ? AND qr.user_id = ? AND q.question_type = 'Essay'
");
$stmt->bind_param("ii", $quizId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$essays = $result->fetch_all(MYSQLI_ASSOC);

// Fetch quiz title and username for display
$quizTitle = '';
$userName = '';

$qQuiz = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$qQuiz->bind_param("i", $quizId);
$qQuiz->execute();
$resQuiz = $qQuiz->get_result();
if ($row = $resQuiz->fetch_assoc()) {
    $quizTitle = $row['title'];
}

$qUser = $conn->prepare("SELECT username FROM users WHERE id = ?");
$qUser->bind_param("i", $userId);
$qUser->execute();
$resUser = $qUser->get_result();
if ($row = $resUser->fetch_assoc()) {
    $userName = $row['username'];
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Grade Essays for Quiz: <?= htmlspecialchars($quizTitle) ?> — User: <?= htmlspecialchars($userName) ?></h3>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (count($essays) === 0): ?>
                <p>No essay responses found for this user and quiz.</p>
            <?php else: ?>
                <form method="POST" class="needs-validation" novalidate>
                    <?php foreach ($essays as $essay): ?>
                        <div class="mb-4 p-3 border rounded">
                            <h5><?= htmlspecialchars($essay['question_text']) ?></h5>
                            <p><strong>Answer:</strong></p>
                            <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;"><?= htmlspecialchars($essay['answer']) ?></div>
                            <div class="mt-3 row align-items-center">
                                <label for="score_<?= $essay['response_id'] ?>" class="col-sm-2 col-form-label">
                                    Score (Max <?= $essay['max_score'] ?>):
                                </label>
                                <div class="col-sm-2">
                                    <input type="number" step="0.01" min="0" max="<?= $essay['max_score'] ?>" class="form-control" 
                                        id="score_<?= $essay['response_id'] ?>" name="score" value="<?= htmlspecialchars($essay['score']) ?>" required>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="correct" id="correct_yes_<?= $essay['response_id'] ?>" value="1" <?= $essay['correct'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="correct_yes_<?= $essay['response_id'] ?>">Correct</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="correct" id="correct_no_<?= $essay['response_id'] ?>" value="0" <?= !$essay['correct'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="correct_no_<?= $essay['response_id'] ?>">Incorrect</label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <input type="hidden" name="response_id" value="<?= $essay['response_id'] ?>">
                                    <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                                    <button type="submit" class="btn btn-primary">Save Grade</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

<script>
// Bootstrap validation example (optional)
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})();
</script>

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
</style>
