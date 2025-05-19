<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../db_connect.php';
include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

// Make sure we have a connection - use $conn from db_connect.php
if (!isset($conn)) {
    die("Database connection not established. Check db_connect.php file.");
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid announcement ID";
    header("Location: admin_announcement_form.php");
    exit();
}

$announcement_id = $_GET['id'];

// Handle form submission for updating announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "Title and content are required!";
    } else {
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $announcement_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement updated successfully!";
            header("Location: admin_announcement_form.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch announcement details
$stmt = $conn->prepare("SELECT title, content FROM announcements WHERE id = ?");
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();
$stmt->close();

if (!$announcement) {
    $_SESSION['error'] = "Announcement not found";
    header("Location: admin_announcement_form.php");
    exit();
}

// Display session messages
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">Edit Announcement</h3>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">Update Announcement</button>
                    <a href="admin_announcement_form.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>