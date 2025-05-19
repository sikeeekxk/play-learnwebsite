<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "plurn"; // Change this

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$moduleTitle = '';
$moduleOrder = '';

// Fetch current module
if ($moduleId > 0) {
    $stmt = $conn->prepare("SELECT * FROM modules WHERE id = ?");
    $stmt->bind_param("i", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $moduleTitle = $row['title'];
        $moduleOrder = $row['display_order'];
    } else {
        die("Module not found.");
    }
}

// Update module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_module'])) {
    $moduleTitle = trim($_POST['module_title']);
    $moduleOrder = (int)$_POST['module_order'];

    if (empty($moduleTitle)) {
        $message = '<div class="alert alert-danger">Title is required</div>';
    } else {
        $stmt = $conn->prepare("UPDATE modules SET title = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sii", $moduleTitle, $moduleOrder, $moduleId);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Module updated successfully.</div>';
        } else {
            $message = '<div class="alert alert-danger">Update failed: ' . $conn->error . '</div>';
        }
    }
}
?>

<div class="content">
    <div class="container-fluid">
        <div class="card p-4">
            <h3>Edit Module</h3>
            <?php echo $message; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="module_title" class="form-label">Module Title</label>
                    <input type="text" class="form-control" id="module_title" name="module_title" value="<?php echo htmlspecialchars($moduleTitle); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="module_order" class="form-label">Display Order</label>
                    <input type="number" class="form-control" id="module_order" name="module_order" value="<?php echo htmlspecialchars($moduleOrder); ?>" min="1">
                </div>
                <button type="submit" name="update_module" class="btn btn-success">Update Module</button>
                <a href="manage_modules.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
