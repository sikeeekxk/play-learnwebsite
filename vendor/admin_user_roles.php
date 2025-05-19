<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['role'], $_POST['user_id']);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT id, username, email, role FROM users");
?>

<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<main class="p-4">
    <h2 class="text-xl font-semibold mb-4">Manage User Roles</h2>
    <table class="table-auto w-full bg-white rounded-xl shadow">
        <thead class="bg-purple-100">
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Change</th></tr>
        </thead>
        <tbody>
        <?php while($user = $result->fetch_assoc()): ?>
            <tr class="border-t">
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= $user['email'] ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="role" class="form-control">
                            <option value="user" <?= $user['role']=='user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-1">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php include '../layout/footer.php'; ?>
