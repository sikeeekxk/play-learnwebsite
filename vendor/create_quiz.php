<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_code = $_POST['exam_code'];
    $quiz_type = $_POST['quiz_type'];
    $title = $_POST['title'];
    $description = $_POST['description']; // optional

    $stmt = $conn->prepare("INSERT INTO quizzes (exam_code, quiz_type, title) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $exam_code, $quiz_type, $title);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;
    $stmt->close();

    foreach ($_POST['questions'] as $q) {
        $stmt = $conn->prepare("INSERT INTO questions 
            (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issssss",
            $quiz_id,
            $q['text'],
            $q['a'],
            $q['b'],
            $q['c'],
            $q['d'],
            $q['correct']
        );
        $stmt->execute();
    }
    $stmt->close();
    header("Location: quizzes.php?success=1");
    exit();
}
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Create New Quiz</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Exam Code</label>
            <input type="text" name="exam_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quiz Type</label>
            <select name="quiz_type" class="form-select" required>
                <option value="">Select Type</option>
                <option value="Pre-Test">Pre-Test</option>
                <option value="Post-Test">Post-Test</option>
                <option value="Activity">Activity</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quiz Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <h4>Questions</h4>
        <div id="questions-container"></div>
        <button type="button" class="btn btn-outline-primary mb-3" onclick="addQuestion()">Add Question</button>

        <div>
            <button type="submit" class="btn btn-success">Save Quiz</button>
        </div>
    </form>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-block border p-3 mb-3 rounded">
            <div class="mb-2">
                <label>Question</label>
                <textarea name="questions[${questionCount}][text]" class="form-control" required></textarea>
            </div>
            <div class="row mb-2">
                <div class="col"><input type="text" name="questions[${questionCount}][a]" class="form-control" placeholder="Option A" required></div>
                <div class="col"><input type="text" name="questions[${questionCount}][b]" class="form-control" placeholder="Option B" required></div>
                <div class="col"><input type="text" name="questions[${questionCount}][c]" class="form-control" placeholder="Option C" required></div>
                <div class="col"><input type="text" name="questions[${questionCount}][d]" class="form-control" placeholder="Option D" required></div>
            </div>
            <div>
                <label>Correct Option</label>
                <select name="questions[${questionCount}][correct]" class="form-select w-auto" required>
                    <option value="">Select</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    questionCount++;
}
</script>
</body>
</html>
