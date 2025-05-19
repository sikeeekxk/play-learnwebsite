<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Make sure the database connection is established
require_once __DIR__ . '/../db_connect.php';

// Check if $pdo is properly defined
if (!isset($pdo) || $pdo === null) {
    try {
        $host = 'localhost';
        $dbname = 'plurn';
        $username = 'root';
        $password = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';

// Display session messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<!-- Content wrapper for sticky footer -->
<div class="content main d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">User Role Management</h3>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT id, username, email, role FROM users");
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>
                                <td>' . htmlspecialchars($row['id']) . '</td>
                                <td>' . htmlspecialchars($row['username']) . '</td>
                                <td>' . htmlspecialchars($row['email']) . '</td>
                                <td>' . htmlspecialchars($row['role']) . '</td>
                                <td>
                                    <form method="post" action="update_user_role.php">
                                        <input type="hidden" name="user_id" value="' . htmlspecialchars($row['id']) . '">
                                        <select name="new_role" class="form-control">
                                            <option value="user"' . ($row['role'] === 'user' ? ' selected' : '') . '>User</option>
                                            <option value="admin"' . ($row['role'] === 'admin' ? ' selected' : '') . '>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary mt-2">Update</button>
                                    </form>
                                </td>
                            </tr>';
                        }
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="5" class="text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer wrapper -->
    <div class="mt-auto">
        <?php include __DIR__ . '/includes/admin_footer.php'; ?>
    </div>
</div>

<!-- Optional: Matching styling for content and buttons -->
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
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
    }
    table.table-bordered th,
    table.table-bordered td {
        padding: 12px;
    }
</style>
