<?php
/**
 * Header Component - Dùng chung cho tất cả các trang
 * Bao gồm logic kiểm tra session và hiển thị menu phù hợp
 */

// Bắt đầu session nếu chưa bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra user đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);
$userFullname = $isLoggedIn ? $_SESSION['fullname'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Medicare' : 'Medicare'; ?></title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header class="header">
        <div class="logo">Medicare</div>
        <nav class="nav">
            <a href="index.php">Trang chủ</a>
            <a href="Facility.php">Cơ sở y tế</a>
            <a href="Specialty.php">Chuyên khoa</a>
            <a href="About.php">Giới thiệu</a>
            <a href="Contact.php">Liên hệ</a>
        </nav>
        <div class="auth">
            <?php if ($isLoggedIn): ?>
                <a href="MyAppointments.php" class="auth-link">Lịch hẹn của tôi</a>
                <a href="UserProfile.php" class="auth-link"><?php echo htmlspecialchars($userFullname); ?></a>
                <a href="logout.php" class="auth-link signup">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php" class="auth-link">Đăng nhập</a>
                <a href="register.php" class="auth-link signup">Đăng ký</a>
            <?php endif; ?>
        </div>
        <button class="menu-toggle" aria-label="Mở menu">☰</button>
    </header>

