<?php
// Admin Admins Management - CRUD quản lý quản trị viên

$pageTitle = 'Quản lý admin cơ sở y tế';
require_once '../config.php';
include 'admin-header.php';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $admin_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM facility_admins WHERE admin_id = $admin_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-admins.php');
    exit();
}

// Thêm/sửa admin cơ sở y tế
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $facility_id = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;
    
    if (isset($_POST['admin_id']) && is_numeric($_POST['admin_id'])) {
        // Update admin hiện có
        $admin_id = intval($_POST['admin_id']);
        
        // Kiểm tra email trùng (trừ chính admin đang sửa)
        $check_email = "SELECT admin_id FROM facility_admins WHERE email = '$email' AND admin_id != $admin_id";
        $result_check = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result_check) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        // Kiểm tra email không trùng với admin chính
        $check_email_admin = "SELECT admin_id FROM admins WHERE email = '$email'";
        $result_check_admin = mysqli_query($conn, $check_email_admin);
        if (mysqli_num_rows($result_check_admin) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        // Update password nếu có, nếu không thì giữ nguyên
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_hash = mysqli_real_escape_string($conn, $password_hash);
            $sql_update = "UPDATE facility_admins SET fullname = '$fullname', email = '$email', password = '$password_hash' WHERE admin_id = $admin_id";
        } else {
            $sql_update = "UPDATE facility_admins SET fullname = '$fullname', email = '$email' WHERE admin_id = $admin_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert admin mới
        if ($facility_id <= 0) {
            header('Location: admin-admins.php?error=facility_required');
            exit();
        }
        
        // Kiểm tra email trùng trong facility_admins
        $check_email = "SELECT admin_id FROM facility_admins WHERE email = '$email'";
        $result_check = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result_check) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        // Kiểm tra email không trùng với admin chính
        $check_email_admin = "SELECT admin_id FROM admins WHERE email = '$email'";
        $result_check_admin = mysqli_query($conn, $check_email_admin);
        if (mysqli_num_rows($result_check_admin) > 0) {
            header('Location: admin-admins.php?error=email_exists');
            exit();
        }
        
        // Password bắt buộc khi tạo mới
        if (empty($password)) {
            header('Location: admin-admins.php?error=password_required');
            exit();
        }
        
        // Hash password trước khi lưu
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash = mysqli_real_escape_string($conn, $password_hash);
        $sql_insert = "INSERT INTO facility_admins (facility_id, fullname, email, password) VALUES ($facility_id, '$fullname', '$email', '$password_hash')";
        mysqli_query($conn, $sql_insert);
    }
    header('Location: admin-admins.php');
    exit();
}

// Lấy tham số tìm kiếm và edit
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id = isset($_GET['edit']) && is_numeric($_GET['edit']) ? intval($_GET['edit']) : 0;
$show_form = isset($_GET['add']) || $edit_id > 0;

$edit_admin = null;
if ($edit_id > 0) {
    $sql_edit = "SELECT fa.*, f.name AS facility_name, f.type AS facility_type
                 FROM facility_admins fa
                 JOIN facilities f ON fa.facility_id = f.facility_id
                 WHERE fa.admin_id = $edit_id";
    $result_edit = mysqli_query($conn, $sql_edit);
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_admin = mysqli_fetch_assoc($result_edit);
    } else {
        $edit_id = 0;
        $show_form = false;
    }
}

// Xây dựng điều kiện WHERE
$where_conditions = [];
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(fa.fullname LIKE '%$search_escaped%' OR fa.email LIKE '%$search_escaped%' OR f.name LIKE '%$search_escaped%')";
}
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$facility_admins = [];
$sql_facility = "SELECT fa.*, f.name AS facility_name, f.type AS facility_type
                 FROM facility_admins fa
                 JOIN facilities f ON fa.facility_id = f.facility_id
                 $where_clause
                 ORDER BY fa.admin_id DESC";
$result_facility = mysqli_query($conn, $sql_facility);
if ($result_facility) {
    while ($row = mysqli_fetch_assoc($result_facility)) {
        $facility_admins[] = $row;
    }
}

$facilities = [];
$sql_facilities = "SELECT f.*, 
                          (SELECT COUNT(*) FROM facility_admins WHERE facility_id = f.facility_id) AS admin_count
                   FROM facilities f 
                   ORDER BY f.name";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    while ($row = mysqli_fetch_assoc($result_facilities)) {
        $facilities[] = $row;
    }
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý admin cơ sở y tế</h1>
        <?php if (!$show_form): ?>
            <a href="admin-admins.php?add=1" class="btn-admin-primary">
                + Thêm admin cơ sở y tế
            </a>
        <?php else: ?>
            <a href="admin-admins.php" class="btn-admin-secondary">
                ← Quay lại
            </a>
        <?php endif; ?>
    </div>

    <?php if ($error == 'email_exists'): ?>
        <div class="alert alert-error">
            Email này đã được sử dụng. Vui lòng chọn email khác.
        </div>
    <?php elseif ($error == 'password_required'): ?>
        <div class="alert alert-error">
            Vui lòng nhập mật khẩu khi tạo admin mới.
        </div>
    <?php elseif ($error == 'facility_required'): ?>
        <div class="alert alert-error">
            Vui lòng chọn cơ sở y tế.
        </div>
    <?php endif; ?>

    <?php if ($show_form): ?>
        <!-- Form thêm/sửa admin cơ sở y tế -->
        <div class="card">
            <h2><?php echo $edit_id > 0 ? 'Chỉnh sửa admin cơ sở y tế' : 'Thêm admin cơ sở y tế mới'; ?></h2>
            <form method="POST" action="admin-admins.php">
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="admin_id" value="<?php echo $edit_id; ?>" />
                <?php endif; ?>
                <div class="form-group">
                    <label for="facility-admin-facility">Cơ sở y tế <span class="text-red">*</span></label>
                <select id="facility-admin-facility" name="facility_id" required class="form-input" <?php echo $edit_id > 0 ? 'disabled' : ''; ?>>
                    <option value="">Chọn cơ sở y tế</option>
                    <?php foreach ($facilities as $facility): ?>
                        <?php if ($edit_id > 0 && $edit_admin['facility_id'] == $facility['facility_id']): ?>
                            <option value="<?php echo $facility['facility_id']; ?>" selected>
                                <?php echo htmlspecialchars($facility['name']); ?> (<?php echo ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?>)
                                <?php if ($facility['admin_count'] > 0): ?>
                                    - Đã có <?php echo $facility['admin_count']; ?> admin
                                <?php endif; ?>
                            </option>
                        <?php else: ?>
                            <option value="<?php echo $facility['facility_id']; ?>">
                                <?php echo htmlspecialchars($facility['name']); ?> (<?php echo ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?>)
                                <?php if ($facility['admin_count'] > 0): ?>
                                    - Đã có <?php echo $facility['admin_count']; ?> admin
                                <?php endif; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="facility_id" value="<?php echo $edit_admin['facility_id']; ?>" />
                    <small>Không thể thay đổi cơ sở y tế khi chỉnh sửa</small>
                <?php else: ?>
                    <small>Bạn có thể tạo nhiều admin cho cùng một cơ sở y tế</small>
                <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="facility-admin-name">Họ và tên <span class="text-red">*</span></label>
                    <input type="text" id="facility-admin-name" name="fullname" value="<?php echo $edit_admin ? htmlspecialchars($edit_admin['fullname']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-admin-email">Email <span class="text-red">*</span></label>
                    <input type="email" id="facility-admin-email" name="email" value="<?php echo $edit_admin ? htmlspecialchars($edit_admin['email']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-admin-password">Mật khẩu</label>
                    <input type="password" id="facility-admin-password" name="password" class="form-input" <?php echo $edit_id == 0 ? 'required' : ''; ?> />
                    <small><?php echo $edit_id > 0 ? 'Để trống nếu không muốn đổi mật khẩu' : 'Bắt buộc khi tạo mới'; ?></small>
                </div>
                <div class="flex-gap" style="margin-top: 20px;">
                    <a href="admin-admins.php" class="btn-cancel">Hủy</a>
                    <button type="submit" class="btn-admin-primary">Lưu</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Form tìm kiếm -->
        <div class="card">
            <form method="GET" action="admin-admins.php" class="form-row">
                <div class="form-field">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên, email, cơ sở y tế..." class="form-input-sm">
                </div>
                <div class="flex-gap">
                    <button type="submit" class="btn-admin-primary">Tìm kiếm</button>
                    <a href="admin-admins.php" class="btn-admin-secondary">Xóa bộ lọc</a>
                </div>
            </form>
        </div>

        <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Cơ sở y tế</th>
                    <th>Loại</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($facility_admins)): ?>
                    <tr>
                        <td colspan="7">Chưa có admin cơ sở y tế nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($facility_admins as $facility_admin): ?>
                        <tr>
                            <td><?php echo $facility_admin['admin_id']; ?></td>
                            <td><?php echo htmlspecialchars($facility_admin['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($facility_admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($facility_admin['facility_name']); ?></td>
                            <td><?php echo ($facility_admin['facility_type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($facility_admin['created_at'])); ?></td>
                            <td>
                                <a href="admin-admins.php?edit=<?php echo $facility_admin['admin_id']; ?>" class="btn-edit btn-sm">Sửa</a>
                                <a href="admin-admins.php?delete=<?php echo $facility_admin['admin_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa admin cơ sở y tế này?')">Xóa</a>
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

