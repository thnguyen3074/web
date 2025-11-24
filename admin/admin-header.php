<?php
// Admin Header Component - Dùng chung cho tất cả các trang admin

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin - bắt buộc phải đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$current_page = basename($_SERVER['PHP_SELF']); // Để highlight menu item đang active
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Medicare Admin' : 'Medicare Admin'; ?></title>
    <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-logo">Medicare Admin</div>
        <div class="admin-header-actions">
            <span style="margin-right: 16px;">Xin chào, <?php echo htmlspecialchars($admin_name); ?></span>
            <a href="admin-logout.php" class="btn-admin-logout">Đăng xuất</a>
        </div>
    </header>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="admin-dashboard.php" class="nav-item <?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>">
                    <span>📊</span> Tổng quan
                </a>
                <a href="admin-facilities.php" class="nav-item <?php echo ($current_page == 'admin-facilities.php') ? 'active' : ''; ?>">
                    <span>🏥</span> Cơ sở y tế
                </a>
                <a href="admin-specialties.php" class="nav-item <?php echo ($current_page == 'admin-specialties.php') ? 'active' : ''; ?>">
                    <span>⚕️</span> Chuyên khoa
                </a>
                <a href="admin-appointments.php" class="nav-item <?php echo ($current_page == 'admin-appointments.php') ? 'active' : ''; ?>">
                    <span>📅</span> Lịch hẹn
                </a>
                <a href="admin-users.php" class="nav-item <?php echo ($current_page == 'admin-users.php') ? 'active' : ''; ?>">
                    <span>👥</span> Người dùng
                </a>
                <a href="admin-admins.php" class="nav-item <?php echo ($current_page == 'admin-admins.php') ? 'active' : ''; ?>">
                    <span>🔐</span> Quản trị viên
                </a>
                <a href="admin-contact-messages.php" class="nav-item <?php echo ($current_page == 'admin-contact-messages.php' || $current_page == 'admin-contact-detail.php') ? 'active' : ''; ?>">
                    <span>💬</span> Yêu cầu hỗ trợ
                </a>
            </nav>
        </aside>

        <main class="admin-main">

