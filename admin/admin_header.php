<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PLURN Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
        }
        .header {
            height: 60px;
            background-color: #6f42c1;
            color: white;
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }
        .content {
            margin-left: 240px;
            padding: 80px 20px 20px 20px;
        }
        .username {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <div><strong>Admin Panel</strong></div>
    <div>Hello, <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span></div>
</div>
