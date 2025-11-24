<?php
// Admin Login - Trang đăng nhập admin

session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: admin-dashboard.php');
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicare Admin - Đăng nhập</title>
    <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-login-page">
    <div class="login-container">
        <div class="login-card">
            <h1>Medicare Admin</h1>
            <p class="login-subtitle">Đăng nhập vào hệ thống quản trị</p>
            
            <?php if ($error == '1'): ?>
                <div class="alert alert-error">
                    Email hoặc mật khẩu không đúng.
                </div>
            <?php endif; ?>
            
            <form class="admin-login-form" action="admin-login-process.php" method="POST" data-validate="true">
                <div class="form-group">
                    <label for="admin-email">Email</label>
                    <input
                        type="email"
                        id="admin-email"
                        name="email"
                        placeholder="Nhập email"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="admin-password">Mật khẩu</label>
                    <input
                        type="password"
                        id="admin-password"
                        name="password"
                        placeholder="Nhập mật khẩu"
                        required
                    />
                </div>
                <button type="submit" class="btn-admin-primary">Đăng nhập</button>
            </form>
        </div>
    </div>
    <script src="admin.js"></script>
</body>
</html>

