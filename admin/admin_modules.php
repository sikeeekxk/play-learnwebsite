<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

// Initialize variables
$message = '';
$moduleTitle = '';
$moduleDescription = '';
$moduleOrder = '';

// Handle form submission for adding/editing modules
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_module'])) {
        // Validate inputs
        $moduleTitle = trim($_POST['module_title']);
        $moduleDescription = trim($_POST['module_description']);
        $moduleOrder = (int)$_POST['module_order'];
        
        if (empty($moduleTitle)) {
            $message = '<div class="alert alert-danger">Module title is required</div>';
        } else {
            // Here you would add code to insert the module into your database
            // For example:
            /*
            $sql = "INSERT INTO modules (title, description, display_order) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $moduleTitle, $moduleDescription, $moduleOrder);
            
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Module added successfully!</div>';
                // Clear form fields after successful submission
                $moduleTitle = '';
                $moduleDescription = '';
                $moduleOrder = '';
            } else {
                $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
            }
            */
            
            // For demonstration purposes:
            $message = '<div class="alert alert-success">Module added successfully! (Database connection not implemented)</div>';
            // Clear form fields
            $moduleTitle = '';
            $moduleDescription = '';
            $moduleOrder = '';
        }
    }
}

// You would fetch existing modules here
// For example:
/*
$modules = [];
$sql = "SELECT * FROM modules ORDER BY display_order";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
}
*/

// For demonstration, we'll create sample modules
$modules = [
    ['id' => 1, 'title' => 'Introduction', 'description' => 'Getting started with the basics', 'display_order' => 1],
    ['id' => 2, 'title' => 'Advanced Concepts', 'description' => 'Deeper dive into advanced topics', 'display_order' => 2],
    ['id' => 3, 'title' => 'Practice Exercises', 'description' => 'Hands-on practice sessions', 'display_order' => 3],
];
?>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Manage Modules</h3>
            <p class="text-muted">Create, edit, and organize learning modules.</p>
            
            <?php echo $message; ?>
            
            <!-- Add New Module Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Add New Module</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="module_title" class="form-label">Module Title</label>
                            <input type="text" class="form-control" id="module_title" name="module_title" value="<?php echo htmlspecialchars($moduleTitle); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="module_description" class="form-label">Description</label>
                            <textarea class="form-control" id="module_description" name="module_description" rows="3"><?php echo htmlspecialchars($moduleDescription); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="module_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="module_order" name="module_order" value="<?php echo htmlspecialchars($moduleOrder); ?>" min="1">
                        </div>
                        <button type="submit" name="add_module" class="btn btn-primary">Add Module</button>
                    </form>
                </div>
            </div>
            
            <!-- List of Modules -->
            <div class="card">
                <div class="card-header">
                    <h5>Existing Modules</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($module['display_order']); ?></td>
                                <td><?php echo htmlspecialchars($module['title']); ?></td>
                                <td><?php echo htmlspecialchars($module['description']); ?></td>
                                <td>
                                    <a href="edit_module.php?id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="delete_module.php?id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this module?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($modules)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No modules found.</td>
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