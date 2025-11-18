<?php
/**
 * Đổi mật khẩu - Medicare
 * Form đổi mật khẩu cho user
 */

$pageTitle = 'Đổi mật khẩu';
require_once 'config.php';
include 'header.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông báo lỗi từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="page auth-page">
    <div class="auth-card">
        <h1>Đổi mật khẩu</h1>
        <p class="auth-desc">Vui lòng nhập mật khẩu cũ và mật khẩu mới của bạn.</p>
        
        <?php if ($error == 'old_password'): ?>
            <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Mật khẩu cũ không đúng. Vui lòng thử lại.
            </div>
        <?php elseif ($error == 'password_length'): ?>
            <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Mật khẩu mới phải có ít nhất 6 ký tự.
            </div>
        <?php elseif ($error == 'password_mismatch'): ?>
            <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Mật khẩu mới và xác nhận không khớp. Vui lòng thử lại.
            </div>
        <?php elseif ($error == 'empty'): ?>
            <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Vui lòng điền đầy đủ thông tin.
            </div>
        <?php endif; ?>
        
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

<script>
// Validate password trùng nhau trước khi submit
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('Mật khẩu mới phải có ít nhất 6 ký tự.');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu mới và xác nhận không khớp. Vui lòng thử lại.');
        return false;
    }
});
</script>

<?php include 'footer.php'; ?>

