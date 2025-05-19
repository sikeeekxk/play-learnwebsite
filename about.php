<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - Thynx</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fce4ec;
            padding-top: 100px;
        }

        header {
            background: #f06292;
            color: white;
            padding: 15px 20px;
            font-size: 24px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #f06292;
        }

        p {
            font-size: 16px;
            line-height: 1.8;
        }
    </style>
</head>
<body>

<header>
    <a href="home.php" class="text-white text-decoration-none">PLURN</a>
    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
</header>

<div class="container mt-5">
    <h2>About PLURN</h2>
    <p>
        Welcome to <strong>PLURN</strong> — an interactive and engaging learning platform designed to enhance your skills through modules, quizzes, games, and personalized progress tracking.
    </p>
    <p>
        Our mission is to make learning fun, accessible, and effective by combining education with technology. Whether you're a student, educator, or lifelong learner, Plurn provides tools and content to help you succeed.
    </p>
    <p>
        With features like gamified modules, calendar-based progress, and a vibrant user community, we strive to create an environment that motivates continuous growth.
    </p>
    <p>
        Thank you for being part of the Plurn learning journey!
    </p>
</div>

</body>
</html>
