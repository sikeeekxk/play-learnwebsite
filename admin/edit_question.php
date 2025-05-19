<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$question_id = $_GET['id'] ?? null;
if (!$question_id) {
    exit("Question ID is missing.");
}

// Fetch question
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

if (!$question) {
    exit("Question not found.");
}

$quiz_id = $question['quiz_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['question_type'];
    $text = $_POST['question_text'];
    $correct = $_POST['correct_answer'] ?? null;
    $a = $_POST['option_a'] ?? null;
    $b = $_POST['option_b'] ?? null;
    $c = $_POST['option_c'] ?? null;
    $d = $_POST['option_d'] ?? null;

    $score = ($type !== "Essay") ? ($_POST['score'] ?? 1) : null;

    $stmt = $pdo->prepare("UPDATE questions SET 
        question_text = ?, question_type = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, score = ?
        WHERE id = ?");
    $stmt->execute([$text, $type, $a, $b, $c, $d, $correct, $score, $question_id]);

    header("Location: edit_quiz.php?id=$quiz_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card p-4">
        <h4>Edit Question (<?= htmlspecialchars($question['question_type']) ?>)</h4>
        <form method="POST">
            <div class="mb-3">
                <label>Question Type</label>
                <select name="question_type" id="question_type" class="form-select" onchange="toggleOptions()" required>
                    <option value="MCQ" <?= $question['question_type'] == 'MCQ' ? 'selected' : '' ?>>MCQ</option>
                    <option value="Identification" <?= $question['question_type'] == 'Identification' ? 'selected' : '' ?>>Identification</option>
                    <option value="True/False" <?= $question['question_type'] == 'True/False' ? 'selected' : '' ?>>True/False</option>
                    <option value="Essay" <?= $question['question_type'] == 'Essay' ? 'selected' : '' ?>>Essay</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Question Text</label>
                <textarea name="question_text" class="form-control" required><?= htmlspecialchars($question['question_text']) ?></textarea>
            </div>

            <div id="mcq-options">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label>Option A</label>
                        <input type="text" name="option_a" class="form-control" value="<?= htmlspecialchars($question['option_a']) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Option B</label>
                        <input type="text" name="option_b" class="form-control" value="<?= htmlspecialchars($question['option_b']) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Option C</label>
                        <input type="text" name="option_c" class="form-control" value="<?= htmlspecialchars($question['option_c']) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Option D</label>
                        <input type="text" name="option_d" class="form-control" value="<?= htmlspecialchars($question['option_d']) ?>">
                    </div>
                </div>
            </div>

            <div id="correct-answer-container" class="mb-3">
                <label>Correct Answer</label>
                <input type="text" name="correct_answer" class="form-control" value="<?= htmlspecialchars($question['correct_answer']) ?>">
            </div>

            <div id="score-container" class="mb-3">
                <label>Score</label>
                <input type="number" name="score" class="form-control" min="1" value="<?= htmlspecialchars($question['score']) ?>">
            </div>

            <button class="btn btn-primary">Update Question</button>
            <a href="edit_quiz.php?id=<?= $quiz_id ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
function toggleOptions() {
    const type = document.getElementById("question_type").value;
    const mcqOptions = document.getElementById("mcq-options");
    const scoreContainer = document.getElementById("score-container");
    const correctAnswerContainer = document.getElementById("correct-answer-container");

    if (type === "MCQ") {
        mcqOptions.style.display = "block";
        scoreContainer.style.display = "block";
        correctAnswerContainer.style.display = "block";
    } else if (type === "Essay") {
        mcqOptions.style.display = "none";
        scoreContainer.style.display = "none";
        correctAnswerContainer.style.display = "none";
    } else {
        mcqOptions.style.display = "none";
        scoreContainer.style.display = "block";
        correctAnswerContainer.style.display = "block";
    }
}
toggleOptions(); // Initialize on load
</script>

</body>
</html>
