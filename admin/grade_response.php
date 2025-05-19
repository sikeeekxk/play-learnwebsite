<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Loop through all the responses and update grading
foreach ($_POST as $key => $value) {
    if (strpos($key, 'correct_') === 0) {
        $response_id = str_replace('correct_', '', $key);
        $correct = $value;

        // Update the 'correct' field in quiz_responses
        $sql = "UPDATE quiz_responses SET correct = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $correct, $response_id);
        $stmt->execute();
    }
}

// Redirect to the same page or to another admin page
header("Location: admin_view_responses.php?quiz_id=" . $_GET['quiz_id']);
exit();
