<?php
/**
 * Hồ sơ cá nhân - Medicare
 * Hiển thị và quản lý thông tin tài khoản người dùng
 */

$pageTitle = 'Hồ sơ cá nhân';
require_once 'config.php';
include 'header.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin user từ database
$sql = "SELECT fullname, email, phone, created_at FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Format ngày tạo tài khoản
$created_date = '';
if (!empty($user['created_at'])) {
    $date_obj = new DateTime($user['created_at']);
    $created_date = $date_obj->format('d/m/Y');
}

// Lấy thông báo từ URL
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="page user-profile-page">
    <section class="page-hero">
        <h1>Hồ sơ cá nhân</h1>
    </section>

    <?php if ($success == 'password'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #c3e6cb;">
            Đổi mật khẩu thành công!
        </div>
    <?php endif; ?>

    <?php if ($error == 'old_password'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #fcc;">
            Mật khẩu cũ không đúng.
        </div>
    <?php elseif ($error == 'password_length'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #fcc;">
            Mật khẩu mới phải có ít nhất 6 ký tự.
        </div>
    <?php elseif ($error == 'password_mismatch'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #fcc;">
            Mật khẩu mới và xác nhận không khớp.
        </div>
    <?php endif; ?>

    <section class="profile-card">
        <div class="profile-avatar">
            <img src="images/users/default.png" alt="Avatar" onerror="this.src='https://via.placeholder.com/120'" />
        </div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
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
            <a href="change_password.php" class="btn-primary" style="text-decoration: none; display: inline-block; text-align: center;">
                Đổi mật khẩu
            </a>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

