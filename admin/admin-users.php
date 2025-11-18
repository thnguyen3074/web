<?php
/**
 * Admin Users Management - Medicare
 * CRUD quản lý người dùng
 */

$pageTitle = 'Quản lý người dùng';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM users WHERE user_id = $user_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-users.php');
    exit();
}

// Xử lý cập nhật user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $user_id = intval($_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $sql_update = "UPDATE users SET fullname = '$fullname', phone = '$phone' WHERE user_id = $user_id";
    mysqli_query($conn, $sql_update);
    header('Location: admin-users.php');
    exit();
}

// Lấy danh sách users
$users = [];
$sql = "SELECT * FROM users ORDER BY user_id DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý người dùng</h1>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6">Chưa có người dùng nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['fullname'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['phone'], ENT_QUOTES); ?>')">Edit</button>
                                <a href="admin-users.php?delete=<?php echo $user['user_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal chỉnh sửa user -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Chỉnh sửa người dùng</h2>
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        </div>
        <form class="modal-form" method="POST" action="admin-users.php">
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="user_id" id="edit-user-id" />
            <div class="form-group">
                <label for="edit-fullname">Họ và tên</label>
                <input type="text" id="edit-fullname" name="fullname" required />
            </div>
            <div class="form-group">
                <label for="edit-phone">Số điện thoại</label>
                <input type="tel" id="edit-phone" name="phone" required />
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('editUserModal')">Hủy</button>
                <button type="submit" class="btn-admin-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, fullname, phone) {
    document.getElementById('edit-user-id').value = id;
    document.getElementById('edit-fullname').value = fullname;
    document.getElementById('edit-phone').value = phone;
    document.getElementById('editUserModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include 'admin-footer.php'; ?>

