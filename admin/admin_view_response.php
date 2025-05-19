<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connection.php';
include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (!$quizId || !$userId) {
    echo "Invalid request.";
    exit();
}

// Fetch quiz title
$quizResult = $conn->query("SELECT title FROM quizzes WHERE id = $quizId");
$quiz = $quizResult->fetch_assoc();

// Fetch user name
$userResult = $conn->query("SELECT username FROM users WHERE id = $userId");
$user = $userResult->fetch_assoc();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_answers'])) {
    $deleteStmt = $conn->prepare("DELETE FROM quiz_responses WHERE quiz_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $quizId, $userId);
    if ($deleteStmt->execute()) {
        echo "<div class='alert alert-success'>All answers for this user have been removed. They can now retake the quiz.</div>";
        $responses = []; // clear table
        $totalScore = 0;
        $maxScore = 0;
    } else {
        echo "<div class='alert alert-danger'>Failed to delete answers. Please try again.</div>";
    }
}

// Fetch all responses from this user for this quiz along with question details
$stmt = $conn->prepare("
    SELECT qr.answer, qr.correct, qr.score AS earned_score, qr.created_at, q.question_text, q.question_type, q.score AS max_score
    FROM quiz_responses qr
    JOIN questions q ON qr.question_id = q.id
    WHERE qr.quiz_id = ? AND qr.user_id = ?
    ORDER BY qr.created_at ASC
");
$stmt->bind_param("ii", $quizId, $userId);
$stmt->execute();
$responses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$totalScore = 0;
$maxScore = 0;

?>

<div class="header">
    <div>Admin Panel</div>
    <div>Hello, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
</div>

<div class="main min-vh-100 d-flex flex-column">
    <div class="card shadow-sm p-4 mb-4">
        <h3>Answers by <?= htmlspecialchars($user['username']) ?> for Quiz: <?= htmlspecialchars($quiz['title']) ?></h3>
        <a href="admin_quiz_response.php?quiz_id=<?= $quizId ?>" class="btn btn-secondary mb-3">← Back to Users List</a>

        <?php if ($responses): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Correct</th>
                            <th>Score Earned</th>
                            <th>Answered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($responses as $response): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($response['question_text']) ?>
                                    <small class="text-muted">(<?= htmlspecialchars($response['question_type']) ?>)</small>
                                </td>
                                <td><?= htmlspecialchars($response['answer']) ?></td>
                                <td>
                                    <?php
                                        if ($response['question_type'] === 'Essay') {
                                            // Essay might be manually graded; correct could be 0 or 1 or null
                                            echo '<span class="badge bg-secondary">Manual Check</span>';
                                        } else {
                                            echo $response['correct'] == 1
                                                ? '<span class="badge bg-success">Yes</span>'
                                                : '<span class="badge bg-danger">No</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if ($response['question_type'] === 'Essay') {
                                            // Show earned score if already graded, else N/A
                                            if (is_numeric($response['earned_score']) && $response['earned_score'] !== null) {
                                                echo htmlspecialchars($response['earned_score']) . " / " . htmlspecialchars($response['max_score']);
                                                $totalScore += (float)$response['earned_score'];
                                                $maxScore += (float)$response['max_score'];
                                            } else {
                                                // Not graded yet
                                                echo "N/A";
                                                $maxScore += (float)$response['max_score']; // max score still counts for total max
                                            }
                                        } else {
                                            // Non-essay question auto graded
                                            $earned = ($response['correct'] == 1) ? (float)$response['max_score'] : 0;
                                            $totalScore += $earned;
                                            $maxScore += (float)$response['max_score'];
                                            echo $earned . " / " . $response['max_score'];
                                        }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($response['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="3">Total Score</td>
                            <td colspan="2"><?= $totalScore ?> / <?= $maxScore ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form method="post" onsubmit="return confirm('Are you sure you want to remove this user\'s answers? This cannot be undone.');">
                <input type="hidden" name="delete_answers" value="1">
                <button type="submit" class="btn btn-danger mt-3">🗑️ Remove All Answers (Allow Retake)</button>
            </form>
        <?php else: ?>
            <p>No answers found for this user on this quiz.</p>
        <?php endif; ?>
    </div>
    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

<style>
    body { font-family: Arial, sans-serif; background-color: #f4f6f9; }
    .header {
        height: 60px;
        background-color: #7b2cbf;
        color: white;
        padding: 15px 20px;
        position: fixed;
        left: 240px;
        right: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: bold;
        z-index: 1030;
    }
    .main {
        margin-left: 240px;
        padding: 80px 30px 30px 30px;
        display: flex;
        flex-direction: column;
    }
    .card-header {
        background-color: #6c757d;
        color: white;
        font-weight: bold;
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .table th, .table td {
        padding: 12px;
    }
</style>
