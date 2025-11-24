<?php
// Hồ sơ cá nhân - Hiển thị và quản lý thông tin tài khoản người dùng

$pageTitle = 'Hồ sơ cá nhân';
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT fullname, email, phone, created_at FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: login.php');
    exit();
}

$created_date = '';
if (!empty($user['created_at'])) {
    $date_obj = new DateTime($user['created_at']);
    $created_date = $date_obj->format('d/m/Y');
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';
?>

<main class="page user-profile-page">
    <section class="page-hero">
        <h1>Hồ sơ cá nhân</h1>
    </section>

    <?php if ($success == 'password'): ?>
        <div class="alert alert-success">Đổi mật khẩu thành công!</div>
    <?php elseif ($success == 'profile'): ?>
        <div class="alert alert-success">Cập nhật thông tin thành công!</div>
    <?php endif; ?>

    <?php if ($error == 'old_password'): ?>
        <div class="alert alert-error">Mật khẩu cũ không đúng.</div>
    <?php elseif ($error == 'password_length'): ?>
        <div class="alert alert-error">Mật khẩu mới phải có ít nhất 6 ký tự.</div>
    <?php elseif ($error == 'password_mismatch'): ?>
        <div class="alert alert-error">Mật khẩu mới và xác nhận không khớp.</div>
    <?php elseif ($error == 'empty'): ?>
        <div class="alert alert-error">Vui lòng điền đầy đủ thông tin.</div>
    <?php elseif ($error == 'update_failed'): ?>
        <div class="alert alert-error">Có lỗi xảy ra khi cập nhật thông tin.</div>
    <?php endif; ?>

    <section class="profile-card">
        <?php if (!$edit_mode): ?>
            <!-- Chế độ xem -->
            <div class="profile-avatar">
                <img src="images/users/default.png" alt="Avatar" onerror="this.src='https://via.placeholder.com/120'" />
            </div>
            
            <div class="profile-header">
                <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
            </div>
            
            <div class="profile-info">
                <div class="info-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Số điện thoại:</strong>
                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <?php if (!empty($created_date)): ?>
                    <div class="info-item">
                        <strong>Ngày tạo tài khoản:</strong>
                        <span><?php echo $created_date; ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-actions">
                <a href="?edit=1" class="btn-primary">Chỉnh sửa</a>
                <a href="change_password.php" class="btn-primary">Đổi mật khẩu</a>
            </div>
        <?php else: ?>
            <!-- Chế độ chỉnh sửa -->
            <div class="profile-header">
                <h2>Chỉnh sửa thông tin</h2>
            </div>

            <form action="update_profile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="fullname">Họ và tên</label>
                    <input 
                        type="text" 
                        id="fullname" 
                        name="fullname" 
                        value="<?php echo htmlspecialchars($user['fullname']); ?>" 
                        required 
                    />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($user['email']); ?>" 
                        disabled
                        style="background: #f5f5f5; cursor: not-allowed;"
                    />
                    <small>Email không thể thay đổi</small>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?php echo htmlspecialchars($user['phone']); ?>" 
                        required 
                    />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    <a href="UserProfile.php" class="btn-cancel">Hủy</a>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

