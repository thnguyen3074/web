<?php
/**
 * Admin Admins Management - Medicare
 * CRUD quản lý quản trị viên
 */

$pageTitle = 'Quản lý quản trị viên';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa admin
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $admin_id = intval($_GET['delete']);
    
    // Không cho phép xóa chính mình
    if ($admin_id == $_SESSION['admin_id']) {
        header('Location: admin-admins.php?error=self_delete');
        exit();
    }
    
    $sql_delete = "DELETE FROM admins WHERE admin_id = $admin_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-admins.php');
    exit();
}

// Xử lý thêm/sửa admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (isset($_POST['admin_id']) && is_numeric($_POST['admin_id'])) {
        // Update
        $admin_id = intval($_POST['admin_id']);
        
        // Kiểm tra email trùng (trừ chính admin đang sửa)
        $check_email = "SELECT admin_id FROM admins WHERE email = '$email' AND admin_id != $admin_id";
        $result_check = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result_check) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        if (!empty($password)) {
            // Cập nhật cả mật khẩu
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_hash = mysqli_real_escape_string($conn, $password_hash);
            $sql_update = "UPDATE admins SET name = '$name', email = '$email', password = '$password_hash' WHERE admin_id = $admin_id";
        } else {
            // Giữ nguyên mật khẩu cũ
            $sql_update = "UPDATE admins SET name = '$name', email = '$email' WHERE admin_id = $admin_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert
        // Kiểm tra email trùng
        $check_email = "SELECT admin_id FROM admins WHERE email = '$email'";
        $result_check = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result_check) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        if (empty($password)) {
            header('Location: admin-admins.php?error=password_required');
            exit();
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash = mysqli_real_escape_string($conn, $password_hash);
        $sql_insert = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password_hash')";
        mysqli_query($conn, $sql_insert);
    }
    header('Location: admin-admins.php');
    exit();
}

// Lấy danh sách admins
$admins = [];
$sql = "SELECT * FROM admins ORDER BY admin_id DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
}

// Lấy thông báo lỗi từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý quản trị viên</h1>
        <button class="btn-admin-primary" onclick="openModal('adminModal')">
            + Thêm quản trị viên
        </button>
    </div>

    <?php if ($error == 'email_exists'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Email này đã được sử dụng. Vui lòng chọn email khác.
        </div>
    <?php elseif ($error == 'password_required'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Vui lòng nhập mật khẩu khi tạo admin mới.
        </div>
    <?php elseif ($error == 'self_delete'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Bạn không thể xóa chính tài khoản của mình.
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="5">Chưa có quản trị viên nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo $admin['admin_id']; ?></td>
                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($admin['created_at'])); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editAdmin(<?php echo $admin['admin_id']; ?>, '<?php echo htmlspecialchars($admin['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($admin['email'], ENT_QUOTES); ?>')">Edit</button>
                                <?php if ($admin['admin_id'] != $_SESSION['admin_id']): ?>
                                    <a href="admin-admins.php?delete=<?php echo $admin['admin_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa quản trị viên này?')">Delete</a>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 14px;">(Tài khoản của bạn)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal thêm/sửa admin -->
<div id="adminModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Thêm quản trị viên</h2>
            <span class="close" onclick="closeModal('adminModal')">&times;</span>
        </div>
        <form class="modal-form" method="POST" action="admin-admins.php">
            <input type="hidden" name="admin_id" id="admin-id" />
            <div class="form-group">
                <label for="admin-name">Tên quản trị viên</label>
                <input type="text" id="admin-name" name="name" required />
            </div>
            <div class="form-group">
                <label for="admin-email">Email</label>
                <input type="email" id="admin-email" name="email" required />
            </div>
            <div class="form-group">
                <label for="admin-password">Mật khẩu</label>
                <input type="password" id="admin-password" name="password" />
                <small id="password-hint">Để trống nếu không muốn đổi mật khẩu (khi sửa)</small>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('adminModal')">Hủy</button>
                <button type="submit" class="btn-admin-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.getElementById('admin-id').value = '';
    document.getElementById('modal-title').textContent = 'Thêm quản trị viên';
    document.getElementById('admin-name').value = '';
    document.getElementById('admin-email').value = '';
    document.getElementById('admin-password').value = '';
    document.getElementById('admin-password').required = true;
    document.getElementById('password-hint').style.display = 'none';
}

function editAdmin(id, name, email) {
    document.getElementById('admin-id').value = id;
    document.getElementById('modal-title').textContent = 'Chỉnh sửa quản trị viên';
    document.getElementById('admin-name').value = name;
    document.getElementById('admin-email').value = email;
    document.getElementById('admin-password').value = '';
    document.getElementById('admin-password').required = false;
    document.getElementById('password-hint').style.display = 'block';
    document.getElementById('adminModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include 'admin-footer.php'; ?>

