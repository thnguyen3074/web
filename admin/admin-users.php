<?php
// Admin Users Management - CRUD quản lý người dùng

$pageTitle = 'Quản lý người dùng';
require_once '../config.php';
include 'admin-header.php';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM users WHERE user_id = $user_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-users.php');
    exit();
}

// Cập nhật thông tin user (chỉ fullname và phone, không cho đổi email/password)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $user_id = intval($_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $sql_update = "UPDATE users SET fullname = '$fullname', phone = '$phone' WHERE user_id = $user_id";
    mysqli_query($conn, $sql_update);
    header('Location: admin-users.php');
    exit();
}

// Lấy tham số tìm kiếm và edit
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id = isset($_GET['edit']) && is_numeric($_GET['edit']) ? intval($_GET['edit']) : 0;
$show_form = $edit_id > 0;

// Lấy thông tin user để sửa
$edit_user = null;
if ($edit_id > 0) {
    $sql_edit = "SELECT * FROM users WHERE user_id = $edit_id";
    $result_edit = mysqli_query($conn, $sql_edit);
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_user = mysqli_fetch_assoc($result_edit);
    } else {
        $edit_id = 0;
        $show_form = false;
    }
}

// Xây dựng điều kiện WHERE động cho tìm kiếm
$where_conditions = [];
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(fullname LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR phone LIKE '%$search_escaped%')";
}
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Lấy danh sách users với filter
$users = [];
$sql = "SELECT * FROM users $where_clause ORDER BY user_id DESC";
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
        <?php if ($show_form): ?>
            <a href="admin-users.php" class="btn-admin-secondary">
                ← Quay lại
            </a>
        <?php endif; ?>
    </div>

    <?php if ($show_form): ?>
        <!-- Form chỉnh sửa user -->
        <div class="card">
            <h2>Chỉnh sửa người dùng</h2>
            <form method="POST" action="admin-users.php">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="user_id" value="<?php echo $edit_id; ?>" />
                <div class="form-group">
                    <label for="edit-fullname">Họ và tên <span class="text-red">*</span></label>
                    <input type="text" id="edit-fullname" name="fullname" value="<?php echo $edit_user ? htmlspecialchars($edit_user['fullname']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="edit-phone">Số điện thoại <span class="text-red">*</span></label>
                    <input type="tel" id="edit-phone" name="phone" value="<?php echo $edit_user ? htmlspecialchars($edit_user['phone']) : ''; ?>" required class="form-input" />
                </div>
                <div class="flex-gap" style="margin-top: 20px;">
                    <a href="admin-users.php" class="btn-cancel">Hủy</a>
                    <button type="submit" class="btn-admin-primary">Lưu</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Form tìm kiếm -->
        <div class="card">
            <form method="GET" action="admin-users.php" class="form-row">
                <div class="form-field">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên, email, số điện thoại..." class="form-input-sm">
                </div>
                <div class="flex-gap">
                    <button type="submit" class="btn-admin-primary">Tìm kiếm</button>
                    <a href="admin-users.php" class="btn-admin-secondary">Xóa bộ lọc</a>
                </div>
            </form>
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
                                <a href="admin-users.php?edit=<?php echo $user['user_id']; ?>" class="btn-edit btn-sm">Sửa</a>
                                <a href="admin-users.php?delete=<?php echo $user['user_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'admin-footer.php'; ?>

