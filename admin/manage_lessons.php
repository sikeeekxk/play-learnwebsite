<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../config.php';
include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

// Check if module ID is provided
if (!isset($_GET['module_id']) || !is_numeric($_GET['module_id'])) {
    header("Location: manage_modules.php");
    exit();
}

$moduleId = (int)$_GET['module_id'];
$module = null;

// Get module information
try {
    $moduleSql = "SELECT * FROM modules WHERE id = ?";
    $moduleStmt = $conn->prepare($moduleSql);
    $moduleStmt->bind_param("i", $moduleId);
    $moduleStmt->execute();
    $moduleResult = $moduleStmt->get_result();
    
    if ($moduleResult->num_rows === 1) {
        $module = $moduleResult->fetch_assoc();
    } else {
        // Module not found
        header("Location: manage_modules.php");
        exit();
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

// Initialize variables
$message = '';
$lessonTitle = '';
$lessonDescription = '';
$lessonContent = '';
$lessonOrder = '';

// Handle form submission for adding lessons
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_lesson'])) {
        // Validate inputs
        $lessonTitle = trim($_POST['lesson_title']);
        $lessonDescription = trim($_POST['lesson_description']);
        $lessonContent = trim($_POST['lesson_content']);
        $lessonOrder = (int)$_POST['lesson_order'];
        
        if (empty($lessonTitle)) {
            $message = '<div class="alert alert-danger">Lesson title is required</div>';
        } else {
            try {
                // Insert the lesson into the database
                $sql = "INSERT INTO module_lessons (module_id, title, description, content, lesson_order, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssss", $moduleId, $lessonTitle, $lessonDescription, $lessonContent, $lessonOrder, $_SESSION['username']);
                
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Lesson added successfully!</div>';
                    // Clear form fields after successful submission
                    $lessonTitle = '';
                    $lessonDescription = '';
                    $lessonContent = '';
                    $lessonOrder = '';
                } else {
                    $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        }
    }
}

// Fetch existing lessons for this module
$lessons = [];
try {
    $sql = "SELECT l.*, u.username as creator 
            FROM module_lessons l
            LEFT JOIN users u ON l.created_by = u.username 
            WHERE l.module_id = ?
            ORDER BY l.lesson_order";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $lessons[] = $row;
        }
    }
} catch (Exception $e) {
    $message = '<div class="alert alert-danger">Error fetching lessons: ' . $e->getMessage() . '</div>';
}
?>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Manage Lessons</h3>
                    <p class="text-muted">Module: <?php echo htmlspecialchars($module['title']); ?></p>
                </div>
                <a href="manage_modules.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Modules
                </a>
            </div>
            
            <?php echo $message; ?>
            
            <!-- Add New Lesson Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Add New Lesson</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="lesson_title" class="form-label">Lesson Title</label>
                            <input type="text" class="form-control" id="lesson_title" name="lesson_title" value="<?php echo htmlspecialchars($lessonTitle); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="lesson_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="lesson_description" name="lesson_description" rows="2"><?php echo htmlspecialchars($lessonDescription); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lesson_content" class="form-label">Lesson Content</label>
                            <textarea class="form-control" id="lesson_content" name="lesson_content" rows="6"><?php echo htmlspecialchars($lessonContent); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lesson_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="lesson_order" name="lesson_order" value="<?php echo htmlspecialchars($lessonOrder); ?>" min="1">
                        </div>
                        <button type="submit" name="add_lesson" class="btn btn-primary">Add Lesson</button>
                    </form>
                </div>
            </div>
            
            <!-- List of Lessons -->
            <div class="card">
                <div class="card-header">
                    <h5>Existing Lessons</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lesson['lesson_order']); ?></td>
                                <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                <td><?php echo htmlspecialchars($lesson['description']); ?></td>
                                <td><?php echo htmlspecialchars($lesson['creator']); ?></td>
                                <td>
                                    <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>&module_id=<?php echo $moduleId; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this lesson?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($lessons)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No lessons found for this module.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>