<?php
session_start();
require_once '../db_connect.php';

// Redirect to login if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $exam_code = $_POST['exam_code'];
    $title = $_POST['title'];
    $quiz_type = $_POST['quiz_type'];
    $exam_type = $_POST['exam_type'];

    $stmt = $pdo->prepare("INSERT INTO quizzes (exam_code, title, quiz_type, exam_type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$exam_code, $title, $quiz_type, $exam_type]);

    header("Location: admin_quiz_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quiz - PLURN</title>
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
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="admin_quiz_list.php">📝 Manage Quizzes</a>
    <a href="admin_user_roles.php">🧑‍🏫 User Roles</a>
    <a href="../logout.php">🔴 Logout</a>
</div>

<div class="header">
    <div>Admin Panel</div>
    <div>Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
</div>

<div class="main">
    <div class="card shadow-sm p-4 mb-4">
        <h3 class="mb-3">Add New Quiz</h3>
        <form method="POST" action="">
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
                        <option value="pre-test">Pre-Test</option>
                        <option value="post-test">Post-Test</option>
                        <option value="practice">Practice</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Exam Type</label>
                    <select name="exam_type" class="form-select" required>
                        <option value="MCQ">MCQ</option>
                        <option value="Identification">Identification</option>
                        <option value="True/False">True/False</option>
                        <option value="Essay">Essay</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <a href="admin_quiz_list.php" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Save Quiz</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
