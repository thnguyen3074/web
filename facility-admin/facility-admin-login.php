<?php
// Facility Admin Login - Trang đăng nhập cho quản trị viên cơ sở y tế

session_start();

if (isset($_SESSION['facility_admin_id'])) {
    header('Location: facility-admin-dashboard.php');
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicare Facility Admin - Đăng nhập</title>
    <link rel="stylesheet" href="../admin/admin.css" />
</head>
<body class="admin-login-page">
    <div class="login-container">
        <div class="login-card">
            <h1>Medicare Facility Admin</h1>
            <p class="login-subtitle">Đăng nhập vào hệ thống quản trị cơ sở y tế</p>
            
            <?php if ($error == '1'): ?>
                <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
                    Email hoặc mật khẩu không đúng.
                </div>
            <?php endif; ?>
            
            <form class="admin-login-form" action="facility-admin-login-process.php" method="POST">
                <div class="form-group">
                    <label for="facility-admin-email">Email</label>
                    <input
                        type="email"
                        id="facility-admin-email"
                        name="email"
                        placeholder="Nhập email"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="facility-admin-password">Mật khẩu</label>
                    <input
                        type="password"
                        id="facility-admin-password"
                        name="password"
                        placeholder="Nhập mật khẩu"
                        required
                    />
                </div>
                <button type="submit" class="btn-admin-primary">Đăng nhập</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="../admin/admin-login.php" style="color: #0d6efd; text-decoration: none;">Đăng nhập Admin tổng</a>
            </div>
        </div>
    </div>
</body>
</html>

