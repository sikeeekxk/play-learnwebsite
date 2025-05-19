<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$quiz_id = $_GET['id'] ?? null;
if (!$quiz_id) {
    header("Location: admin_quiz_list.php");
    exit();
}

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) {
    echo "Quiz not found.";
    exit();
}

// Insert new question
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_question'])) {
    $type = $_POST['question_type'];
    $text = $_POST['question_text'];
    $correct = $_POST['correct_answer'] ?? null; // null for Essay
    $a = $_POST['option_a'] ?? null;
    $b = $_POST['option_b'] ?? null;
    $c = $_POST['option_c'] ?? null;
    $d = $_POST['option_d'] ?? null;
    $score = ($type !== "Essay") ? ($_POST['score'] ?? 1) : null;

    $stmt = $pdo->prepare("INSERT INTO questions 
        (quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, score)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $text, $type, $a, $b, $c, $d, $correct, $score]);

    header("Location: edit_quiz.php?id=$quiz_id");
    exit();
}

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// Calculate total score
$totalScore = 0;
foreach ($questions as $q) {
    if ($q['score'] !== null) {
        $totalScore += (int)$q['score'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quiz - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f6f6f6; }
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0; left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
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
        }
        .main {
            margin-left: 240px;
            padding: 80px 30px 30px 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="admin_home.php">🏠 Dashboard</a>
    <a href="admin_quiz_list.php">📝 Manage Quizzes</a>
    <a href="manage_modules.php">📚 Manage Modules</a>
    <a href="admin_announcement_form.php">📢 Manage Announcements</a>
    <a href="admin_user_roles.php">🧑‍💼 User Roles</a>
    <a href="../logout.php">📕 Logout</a>
</div>

<div class="header">
    <div>Edit Quiz</div>
    <div>Hello, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
</div>

<div class="main">
    <h3><?= htmlspecialchars($quiz['title']) ?> <small class="text-muted">(<?= $quiz['quiz_type'] ?>)</small></h3>

    <div class="mb-4">
        <strong>Total Score:</strong> <?= $totalScore ?>
    </div>

    <div class="card p-4 mb-4">
        <h5>Add New Question</h5>
        <form method="POST">
            <input type="hidden" name="add_question" value="1">
            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select name="question_type" class="form-select" id="question_type" onchange="toggleOptions()" required>
                    <option value="MCQ">MCQ</option>
                    <option value="Identification">Identification</option>
                    <option value="True/False">True/False</option>
                    <option value="Essay">Essay</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Question Text</label>
                <textarea name="question_text" class="form-control" required></textarea>
            </div>

            <div id="mcq-options">
                <div class="row mb-3">
                    <div class="col-md-3"><label>Option A</label><input type="text" name="option_a" class="form-control"></div>
                    <div class="col-md-3"><label>Option B</label><input type="text" name="option_b" class="form-control"></div>
                    <div class="col-md-3"><label>Option C</label><input type="text" name="option_c" class="form-control"></div>
                    <div class="col-md-3"><label>Option D</label><input type="text" name="option_d" class="form-control"></div>
                </div>
            </div>

            <div id="correct-answer-container" class="mb-3">
                <label>Correct Answer</label>
                <input type="text" name="correct_answer" class="form-control">
            </div>

            <div id="score-container" class="mb-3">
                <label>Score</label>
                <input type="number" name="score" min="1" class="form-control" value="1">
            </div>

            <button class="btn btn-success">Add Question</button>
        </form>
    </div>

    <div class="card p-4">
        <h5>Existing Questions</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): ?>
                    <tr>
                        <td><?= $q['id'] ?></td>
                        <td><?= htmlspecialchars($q['question_type']) ?></td>
                        <td><?= htmlspecialchars($q['question_text']) ?></td>
                        <td><?= htmlspecialchars($q['correct_answer']) ?></td>
                        <td><?= $q['score'] !== null ? $q['score'] : '-' ?></td>
                        <td>
                            <a href="edit_question.php?id=<?= $q['id'] ?>&quiz_id=<?= $quiz_id ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete_question.php?id=<?= $q['id'] ?>&quiz_id=<?= $quiz_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($questions) === 0): ?>
                    <tr><td colspan="6" class="text-center text-muted">No questions added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="admin_quiz_list.php" class="btn btn-secondary">← Back to Quiz List</a>
        <button class="btn btn-primary" onclick="alert('All changes are already saved!')">💾 Save</button>
    </div>
</div>

<script>
function toggleOptions() {
    const type = document.getElementById("question_type").value;
    const mcqOptions = document.getElementById("mcq-options");
    const correctAnswerContainer = document.getElementById("correct-answer-container");
    const scoreContainer = document.getElementById("score-container");
    const correctAnswerInput = correctAnswerContainer.querySelector("input[name='correct_answer']");
    const scoreInput = scoreContainer.querySelector("input[name='score']");

    if (type === "MCQ") {
        mcqOptions.style.display = "block";
        correctAnswerContainer.style.display = "block";
        scoreContainer.style.display = "block";
        correctAnswerInput.required = true;
        scoreInput.required = true;
    } else if (type === "Essay") {
        mcqOptions.style.display = "none";
        correctAnswerContainer.style.display = "none";
        scoreContainer.style.display = "none";
        correctAnswerInput.required = false;
        scoreInput.required = false;
        correctAnswerInput.value = "";
        scoreInput.value = "";
    } else {
        mcqOptions.style.display = "none";
        correctAnswerContainer.style.display = "block";
        scoreContainer.style.display = "block";
        correctAnswerInput.required = true;
        scoreInput.required = true;
    }
}
// Call on page load
toggleOptions();
</script>

</body>
</html>
