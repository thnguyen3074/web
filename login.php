<?php
/**
 * Trang đăng nhập - Medicare
 * Giao diện form đăng nhập
 */

$pageTitle = 'Đăng nhập';
require_once 'config.php';
include 'header.php';

// Kiểm tra nếu đã đăng nhập thì redirect về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Lấy thông báo lỗi từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<main class="auth-page">
    <div class="auth-card">
        <h1>Đăng nhập</h1>
        <p class="auth-desc">Chào mừng bạn trở lại với Medicare.</p>
        
        <?php if ($error == '1'): ?>
            <div class="error-message" style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                Email hoặc mật khẩu không đúng. Vui lòng thử lại.
            </div>
        <?php endif; ?>
        
        <?php if ($success == '1'): ?>
            <div class="success-message" style="background: #efe; color: #3c3; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #cfc;">
                Đăng ký thành công! Vui lòng đăng nhập.
            </div>
        <?php endif; ?>
        
        <form class="auth-form" action="login_process.php" method="POST" data-validate="true">
            <div class="form-group">
                <label for="login-email">Email</label>
                <input
                    type="email"
                    id="login-email"
                    name="email"
                    placeholder="Nhập email của bạn"
                    required
                />
            </div>
            <div class="form-group">
                <label for="login-password">Mật khẩu</label>
                <input
                    type="password"
                    id="login-password"
                    name="password"
                    placeholder="Nhập mật khẩu"
                    required
                />
            </div>
            <button type="submit" class="btn-primary">Đăng nhập</button>
        </form>
        <p class="form-note">
            Chưa có tài khoản?
            <a href="register.php">Đăng ký ngay</a>
        </p>
    </div>
</main>

<?php include 'footer.php'; ?>

