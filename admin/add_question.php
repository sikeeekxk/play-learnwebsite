<?php
session_start();
require_once '../db_connect.php';

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get quizzes
$quizzes = $pdo->query("SELECT id, title, exam_type FROM quizzes ORDER BY created_at DESC")->fetchAll();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $correct_answer = $_POST['correct_answer'];
    
    // Optional choices
    $option_a = $_POST['option_a'] ?? null;
    $option_b = $_POST['option_b'] ?? null;
    $option_c = $_POST['option_c'] ?? null;
    $option_d = $_POST['option_d'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer]);

    header("Location: add_question.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question - PLURN</title>
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
    <script>
        function updateFields() {
            const type = document.querySelector('#question_type').value;
            document.querySelector('.mcq-options').style.display = (type === 'MCQ') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="admin_quiz_list.php">📝 Manage Quizzes</a>
    <a href="add_question.php">➕ Add Question</a>
    <a href="../logout.php">🔴 Logout</a>
</div>

<div class="header">
    <div>Admin Panel</div>
    <div>Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
</div>

<div class="main">
    <div class="card shadow-sm p-4">
        <h3 class="mb-3">Add Question</h3>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Question added successfully.</div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Quiz</label>
                <select name="quiz_id" class="form-select" required>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?= $quiz['id'] ?>"><?= htmlspecialchars($quiz['title']) ?> (<?= $quiz['exam_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select name="question_type" id="question_type" class="form-select" onchange="updateFields()" required>
                    <option value="MCQ">MCQ</option>
                    <option value="Identification">Identification</option>
                    <option value="True/False">True/False</option>
                    <option value="Essay">Essay</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Text</label>
                <textarea name="question_text" class="form-control" rows="3" required></textarea>
            </div>

            <!-- MCQ Options -->
            <div class="mcq-options" style="display: block;">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Option A</label>
                        <input type="text" name="option_a" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Option B</label>
                        <input type="text" name="option_b" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Option C</label>
                        <input type="text" name="option_c" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Option D</label>
                        <input type="text" name="option_d" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correct Answer</label>
                <input type="text" name="correct_answer" class="form-control" required>
                <small class="form-text text-muted">For MCQ use A/B/C/D. For True/False, type exactly: True or False.</small>
            </div>

            <div class="text-end">
                <a href="admin_quiz_list.php" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Add Question</button>
            </div>
        </form>
    </div>
</div>

<script>
    updateFields(); // Ensure correct display on load
</script>
</body>
</html>
