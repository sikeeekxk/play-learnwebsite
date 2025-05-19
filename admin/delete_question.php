<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$question_id = $_GET['id'] ?? null;
$quiz_id = $_GET['quiz_id'] ?? null;

if ($question_id && $quiz_id) {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
}

header("Location: edit_quiz.php?id=$quiz_id");
exit();
