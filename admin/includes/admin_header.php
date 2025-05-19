<!-- admin_header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - PLURN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
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
            padding: 0 25px;
            z-index: 1000;
        }
        .sidebar {
            width: 240px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            background-color: #343a40;
            color: white;
            padding-top: 60px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 240px;
            padding: 80px 25px 20px 25px;
            background-color: #f4f4f4;
            min-height: 100vh;
        }
        .logo-img {
            height: 35px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="d-flex align-items-center">
        <img src="icslogo.png" alt="ICS Logo" class="logo-img">
        <strong>Admin Panel</strong>
    </div>
    <div>Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
</div>
