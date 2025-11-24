<?php
// Đổi mật khẩu - Form đổi mật khẩu cho user

$pageTitle = 'Đổi mật khẩu';
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="page auth-page">
    <div class="auth-card">
        <h1>Đổi mật khẩu</h1>
        <p class="auth-desc">Vui lòng nhập mật khẩu cũ và mật khẩu mới của bạn.</p>        
        <form class="auth-form" action="change_password_process.php" method="POST" id="changePasswordForm">
            <div class="form-group">
                <label for="old-password">Mật khẩu cũ</label>
                <input
                    type="password"
                    id="old-password"
                    name="old_password"
                    placeholder="Nhập mật khẩu cũ"
                    required
                />
            </div>
            <div class="form-group">
                <label for="new-password">Mật khẩu mới</label>
                <input
                    type="password"
                    id="new-password"
                    name="new_password"
                    placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                    required
                    minlength="6"
                />
            </div>
            <div class="form-group">
                <label for="confirm-password">Xác nhận mật khẩu mới</label>
                <input
                    type="password"
                    id="confirm-password"
                    name="confirm_password"
                    placeholder="Nhập lại mật khẩu mới"
                    required
                    minlength="6"
                />
            </div>
            <button type="submit" class="btn-primary">Đổi mật khẩu</button>
        </form>
        
        <p class="form-note">
            <a href="UserProfile.php">Quay lại hồ sơ</a>
        </p>
    </div>
</main>

<?php include 'footer.php'; ?>

