<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connection.php';

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

$message = '';
$moduleTitle = '';
$moduleOrder = '';
$filePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_module'])) {
    $moduleTitle = trim($_POST['module_title']);
    $moduleOrder = (int)$_POST['module_order'];

    if (empty($moduleTitle)) {
        $message = '<div class="alert alert-danger">Module title is required.</div>';
    } else {
        // File upload to /admin/uploads/modules/
        $uploadDir = __DIR__ . '/uploads/modules';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (isset($_FILES['module_file']) && $_FILES['module_file']['error'] === UPLOAD_ERR_OK) {
            $originalName = basename($_FILES['module_file']['name']);
            $safeName = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $originalName);
            $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

            if (move_uploaded_file($_FILES['module_file']['tmp_name'], $fullPath)) {
                chmod($fullPath, 0644);
                $filePath = $safeName;
            } else {
                $message = '<div class="alert alert-danger">Failed to upload the file.</div>';
            }
        }

        $stmt = $conn->prepare("INSERT INTO modules (title, display_order, file_path, created_by) VALUES (?, ?, ?, ?)");
        $createdBy = $_SESSION['username'];
        $stmt->bind_param("siss", $moduleTitle, $moduleOrder, $filePath, $createdBy);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ Module added successfully!</div>';
            $moduleTitle = '';
            $moduleOrder = '';
        } else {
            $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($conn->error) . '</div>';
        }
    }
}

$modules = [];
$result = $conn->query("SELECT * FROM modules ORDER BY display_order ASC");
while ($row = $result->fetch_assoc()) {
    $modules[] = $row;
}
?>

<!-- Admin Content -->
<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">📁 Manage Modules</h3>
            <?= $message ?>

            <!-- Add Module -->
            <div class="card mb-4">
                <div class="card-header"><h5>Add New Module</h5></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="module_title" class="form-label">Module Title</label>
                            <input type="text" class="form-control" id="module_title" name="module_title" value="<?= htmlspecialchars($moduleTitle) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="module_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="module_order" name="module_order" value="<?= htmlspecialchars($moduleOrder) ?>" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="module_file" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="module_file" name="module_file" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg">
                        </div>
                        <button type="submit" name="add_module" class="btn btn-primary">Add Module</button>
                    </form>
                </div>
            </div>

            <!-- Module List -->
            <div class="card">
                <div class="card-header"><h5>Existing Modules</h5></div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($module['display_order']) ?></td>
                                        <td><?= htmlspecialchars($module['title']) ?></td>
                                        <td>
                                            <?php if (!empty($module['file_path'])): ?>
                                                <a href="uploads/modules/<?= urlencode($module['file_path']) ?>" target="_blank">View File</a>
                                            <?php else: ?>
                                                <em>No file</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit_module.php?id=<?= $module['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="delete_module.php?id=<?= $module['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this module?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No modules found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

<!-- Styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
    }
    .content.main {
        margin-left: 240px;
        padding: 80px 30px 30px 30px;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .card-header {
        background-color: #6c757d;
        color: white;
        font-weight: bold;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
    .form-control {
        border-radius: 0.25rem;
        padding: 0.75rem;
        font-size: 1rem;
    }
    .table th, .table td {
        padding: 12px;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-outline-danger:hover,
    .btn-outline-primary:hover {
        color: white;
    }
</style>
