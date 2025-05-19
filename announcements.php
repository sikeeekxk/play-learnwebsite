<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "plurn");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all announcements
$announcements = [];
$sql = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>PLURN – Announcements</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f9;
    }
    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .content {
        flex: 1;
        display: flex;
    }
    .header {
        height: 60px;
        background: #6f42c1;
        color: #fff;
        display: flex;
        align-items: center;
        padding: 0 20px;
        z-index: 1100;
    }
    .toggle-btn {
        background: none;
        border: 0;
        color: #fff;
        font-size: 24px;
        margin-right: 20px;
        cursor: pointer;
    }
    .header img.logo {
        height: 40px;
        margin-right: 10px;
    }
    .sidebar {
        width: 220px;
        background: #343a40;
        padding-top: 10px;
        color: #fff;
        transition: transform 0.3s ease;
        overflow-y: auto;
    }
    .sidebar a {
        display: block;
        color: #fff;
        padding: 12px 20px;
        text-decoration: none;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .main-content {
        flex: 1;
        padding: 20px;
    }
    .announcement-card {
        background: #fff;
        border-left: 5px solid #6f42c1;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .announcement-title {
        color: #6f42c1;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .announcement-date {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 10px;
    }
    .footer {
        background: #6f42c1;
        color: #fff;
        text-align: center;
        padding: 15px 20px;
    }
    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }
    }
</style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <img src="icslogo.png" alt="ICS Logo" class="logo" />
        <span class="home-title fw-bold">PLURN</span>

        <div class="dropdown ms-auto">
            <a class="btn btn-sm text-white dropdown-toggle" href="#" id="userDrop" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i> <span><?= htmlspecialchars($_SESSION['username']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDrop">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-badge me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
            <a href="home.php"><i class="fas fa-home me-2"></i>Home</a>
            <a href="module.php"><i class="fas fa-book me-2"></i>Modules</a>
            <a href="quizzes.php"><i class="fas fa-pen me-2"></i>Quizzes</a>
            <a href="calendar.php"><i class="fas fa-calendar-alt me-2"></i>Calendar</a>
            <a href="history.php"><i class="fas fa-history me-2"></i>History</a>
            <a href="announcements.php"><i class="fas fa-bullhorn me-2"></i>Announcements</a>
        </div>


        <!-- Main -->
        <div class="main-content" id="main-content">
            <h3 class="mb-4"><i class="fas fa-bullhorn me-2"></i>Announcements</h3>

            <?php if (empty($announcements)): ?>
                <p>No announcements available at the moment.</p>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-card">
                        <div class="announcement-date">
                            <i class="far fa-calendar me-1"></i> 
                            <?= date('F d, Y - h:i A', strtotime($announcement['created_at'])) ?>
                        </div>
                        <h5 class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></h5>
                        <div class="announcement-content">
                            <?= nl2br(htmlspecialchars($announcement['content'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; <?= date('Y') ?> PLURN. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    sidebar.style.display = (sidebar.style.display === 'none') ? 'block' : 'none';
}
</script>
</body>
</html>
