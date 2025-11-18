<?php
/**
 * Trang đăng ký - Medicare
 * Giao diện form đăng ký
 */

$pageTitle = 'Đăng ký';
require_once 'config.php';
include 'header.php';

// Kiểm tra nếu đã đăng nhập thì redirect về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Lấy thông báo lỗi từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="auth-page">
    <div class="auth-card">
        <h1>Tạo tài khoản</h1>
        <p class="auth-desc">Đăng ký để đặt lịch khám nhanh chóng trên Medicare.</p>
        
        <?php if ($error == 'email'): ?>
            <div class="error-message" style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Email này đã được sử dụng. Vui lòng chọn email khác.
            </div>
        <?php elseif ($error == 'password'): ?>
            <div class="error-message" style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Mật khẩu xác nhận không khớp. Vui lòng thử lại.
            </div>
        <?php elseif ($error == 'empty'): ?>
            <div class="error-message" style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Vui lòng điền đầy đủ thông tin.
            </div>
        <?php endif; ?>
        
        <form class="auth-form" action="register_process.php" method="POST" data-validate="true" id="registerForm">
            <div class="form-group">
                <label for="register-name">Họ và tên</label>
                <input
                    type="text"
                    id="register-name"
                    name="fullname"
                    placeholder="Nhập họ và tên"
                    required
                />
            </div>
            <div class="form-group">
                <label for="register-email">Email</label>
                <input
                    type="email"
                    id="register-email"
                    name="email"
                    placeholder="Nhập email"
                    required
                />
            </div>
            <div class="form-group">
                <label for="register-phone">Số điện thoại</label>
                <input
                    type="tel"
                    id="register-phone"
                    name="phone"
                    placeholder="Nhập số điện thoại"
                    required
                />
            </div>
            <div class="form-group">
                <label for="register-password">Mật khẩu</label>
                <input
                    type="password"
                    id="register-password"
                    name="password"
                    placeholder="Tạo mật khẩu"
                    required
                />
            </div>
            <div class="form-group">
                <label for="register-confirm">Xác nhận mật khẩu</label>
                <input
                    type="password"
                    id="register-confirm"
                    name="confirmPassword"
                    placeholder="Nhập lại mật khẩu"
                    required
                />
            </div>
            <button type="submit" class="btn-primary">Tạo tài khoản</button>
        </form>
        <p class="form-note">
            Đã có tài khoản?
            <a href="login.php">Đăng nhập</a>
        </p>
    </div>
</main>

<script>
// Validate password trùng nhau trước khi submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp. Vui lòng thử lại.');
        return false;
    }
});
</script>

<?php include 'footer.php'; ?>

