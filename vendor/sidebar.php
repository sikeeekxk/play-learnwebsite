<div class="sidebar position-fixed top-0 start-0 pt-5 px-3" id="sidebar" style="width: 220px;">
  <h5 class="text-white">Navigation</h5>
  <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="/plurn/admin/admin_home.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/plurn/admin/admin_quiz_create.php"><i class="bi bi-plus-square"></i> Create Quiz</a>
    <a href="/plurn/admin/admin_quiz_list.php"><i class="bi bi-list-ul"></i> View Quizzes</a>
    <a href="/plurn/admin/admin_user_roles.php"><i class="bi bi-people"></i> Manage Users</a>
  <?php else: ?>
    <a href="/plurn/home.php"><i class="bi bi-house"></i> Home</a>
    <a href="/plurn/quizzes.php"><i class="bi bi-journal-text"></i> Quizzes</a>
    <a href="/plurn/calendar.php"><i class="bi bi-calendar3"></i> Calendar</a>
    <a href="/plurn/history.php"><i class="bi bi-clock-history"></i> History</a>
  <?php endif; ?>
</div>

<div class="content">
