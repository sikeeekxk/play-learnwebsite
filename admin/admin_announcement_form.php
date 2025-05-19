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

// Display session messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Handle form submission for new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "Title and content are required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement published successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Redirect to refresh the page and show message
    header("Location: admin_announcement_form.php");
    exit();
}
?>

<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Create New Announcement</h3>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>
                <button type="submit" name="create_announcement" class="btn btn-primary">Publish Announcement</button>
            </form>
        </div>
        
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">Manage Announcements</h3>
            
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                <td>' . htmlspecialchars($row['title']) . '</td>
                                <td>' . htmlspecialchars(substr($row['content'], 0, 100)) . (strlen($row['content']) > 100 ? '...' : '') . '</td>
                                <td>' . date('M d, Y h:i A', strtotime($row['created_at'])) . '</td>
                                <td>
                                    <a href="edit_announcement.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_announcement.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" 
                                       onclick="return confirm(\'Are you sure you want to delete this announcement?\');">Delete</a>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center">No announcements available</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
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
