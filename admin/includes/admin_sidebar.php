<div class="sidebar">
    <a href="admin_home.php">🏠 Dashboard</a>
    <a href="admin_quiz_list.php">🧠 Manage Quizzes</a>
    
    <!-- View Quiz Responses - Allow quiz selection -->
    <a href="admin_quiz_response.php">📝 View Quiz Responses</a>
    
    <!-- Only show this link if quiz_id and user_id are set in the GET parameters -->
    <?php if (isset($_GET['quiz_id']) && isset($_GET['user_id'])): ?>
        <a href="admin_view_response.php?quiz_id=<?= htmlspecialchars($_GET['quiz_id']) ?>&user_id=<?= htmlspecialchars($_GET['user_id']) ?>">👤 View User Response</a>
    <?php endif; ?>

    <!-- Updated to use emoji for consistency -->
    <a href="admin_grade_essay.php">🖊️ Grade Essays</a>

    <a href="manage_modules.php">📚 Manage Modules</a>
    <a href="admin_announcement_form.php">📢 Manage Announcements</a>
    <a href="admin_user_roles.php">🧑‍💼 User Roles</a>
    <a href="../logout.php">📕 Logout</a>
</div>
