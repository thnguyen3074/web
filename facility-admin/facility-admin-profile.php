<?php
/**
 * Facility Admin Profile Management - Medicare
 * Quản lý thông tin và đổi mật khẩu
 */

$pageTitle = 'Quản lý tài khoản';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_admin_id = intval($_SESSION['facility_admin_id']);
$facility_id = intval($_SESSION['facility_id']);

$success_message = '';
$error_message = '';

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    
    if (!empty($fullname)) {
        $fullname_escaped = mysqli_real_escape_string($conn, $fullname);
        $sql_update = "UPDATE facility_admins SET fullname = '$fullname_escaped' WHERE admin_id = $facility_admin_id";
        if (mysqli_query($conn, $sql_update)) {
            $_SESSION['facility_admin_name'] = $fullname;
            $success_message = 'Cập nhật thông tin thành công!';
        } else {
            $error_message = 'Có lỗi xảy ra khi cập nhật thông tin.';
        }
    } else {
        $error_message = 'Vui lòng nhập đầy đủ thông tin.';
    }
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    } else {
        // Lấy mật khẩu hiện tại
        $sql_get = "SELECT password FROM facility_admins WHERE admin_id = $facility_admin_id";
        $result_get = mysqli_query($conn, $sql_get);
        $admin = mysqli_fetch_assoc($result_get);
        
        if ($admin && password_verify($current_password, $admin['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE facility_admins SET password = '$new_password_hash' WHERE admin_id = $facility_admin_id";
            if (mysqli_query($conn, $sql_update)) {
                $success_message = 'Đổi mật khẩu thành công!';
            } else {
                $error_message = 'Có lỗi xảy ra khi đổi mật khẩu.';
            }
        } else {
            $error_message = 'Mật khẩu hiện tại không đúng.';
        }
    }
}

// Lấy thông tin facility admin
$sql = "SELECT fa.*, f.name AS facility_name 
        FROM facility_admins fa 
        JOIN facilities f ON fa.facility_id = f.facility_id 
        WHERE fa.admin_id = $facility_admin_id";
$result = mysqli_query($conn, $sql);
$admin_info = mysqli_fetch_assoc($result);
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý tài khoản</h1>
    </div>

    <?php if (!empty($success_message)): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; gap: 20px;">
        <!-- Thông tin tài khoản -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Thông tin tài khoản</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; font-weight: bold; width: 200px; border-bottom: 1px solid #eee;">Họ tên:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($admin_info['fullname']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Email:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($admin_info['email']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Cơ sở y tế:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($admin_info['facility_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">Ngày tạo:</td>
                    <td style="padding: 10px;"><?php echo date('d/m/Y H:i', strtotime($admin_info['created_at'])); ?></td>
                </tr>
            </table>
        </div>

        <!-- Cập nhật thông tin -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Cập nhật thông tin</h2>
            <form method="POST" action="facility-admin-profile.php">
                <input type="hidden" name="action" value="update_info">
                <div style="margin-bottom: 15px;">
                    <label for="fullname" style="display: block; margin-bottom: 5px; font-weight: 500;">Họ tên</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($admin_info['fullname']); ?>" required style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" class="btn-admin-primary">Cập nhật thông tin</button>
            </form>
        </div>

        <!-- Đổi mật khẩu -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Đổi mật khẩu</h2>
            <form method="POST" action="facility-admin-profile.php">
                <input type="hidden" name="action" value="change_password">
                <div style="margin-bottom: 15px;">
                    <label for="current_password" style="display: block; margin-bottom: 5px; font-weight: 500;">Mật khẩu hiện tại</label>
                    <input type="password" id="current_password" name="current_password" required style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="new_password" style="display: block; margin-bottom: 5px; font-weight: 500;">Mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6" style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #666; display: block; margin-top: 5px;">Mật khẩu phải có ít nhất 6 ký tự</small>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="confirm_password" style="display: block; margin-bottom: 5px; font-weight: 500;">Xác nhận mật khẩu mới</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6" style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" class="btn-admin-primary">Đổi mật khẩu</button>
            </form>
        </div>
    </div>
</div>

<?php include 'facility-admin-footer.php'; ?>

