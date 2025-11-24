<?php
// Facility Admin Header Component - Dùng chung cho tất cả các trang facility admin

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập facility admin - bắt buộc phải đăng nhập
if (!isset($_SESSION['facility_admin_id'])) {
    header('Location: facility-admin-login.php');
    exit();
}

// Kiểm tra facility_id có tồn tại không (nếu facility bị xóa thì đăng xuất)
if (isset($_SESSION['facility_id']) && !isset($conn)) {
    require_once '../config.php';
    $check_facility = "SELECT facility_id FROM facilities WHERE facility_id = " . intval($_SESSION['facility_id']);
    $result_check = mysqli_query($conn, $check_facility);
    if (mysqli_num_rows($result_check) == 0) {
        // Facility đã bị xóa → hủy session và đăng xuất
        session_destroy();
        header('Location: facility-admin-login.php');
        exit();
    }
}

$facility_admin_name = isset($_SESSION['facility_admin_name']) ? $_SESSION['facility_admin_name'] : 'Admin';
$facility_name = isset($_SESSION['facility_name']) ? $_SESSION['facility_name'] : 'Cơ sở y tế';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Medicare Facility Admin' : 'Medicare Facility Admin'; ?></title>
    <link rel="stylesheet" href="../admin/admin.css" />
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-logo">Medicare Facility Admin</div>
        <div class="admin-header-actions">
            <span style="margin-right: 16px;"><?php echo htmlspecialchars($facility_name); ?> - Xin chào, <?php echo htmlspecialchars($facility_admin_name); ?></span>
            <a href="../facility-admin-logout.php" class="btn-admin-logout">Đăng xuất</a>
        </div>
    </header>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="facility-admin-dashboard.php" class="nav-item <?php echo ($current_page == 'facility-admin-dashboard.php') ? 'active' : ''; ?>">
                    <span>📊</span> Tổng quan
                </a>
                <a href="facility-admin-appointments.php" class="nav-item <?php echo ($current_page == 'facility-admin-appointments.php') ? 'active' : ''; ?>">
                    <span>📅</span> Lịch hẹn
                </a>
                <a href="facility-admin-specialties.php" class="nav-item <?php echo ($current_page == 'facility-admin-specialties.php') ? 'active' : ''; ?>">
                    <span>⚕️</span> Chuyên khoa
                </a>
                <a href="facility-admin-facility.php" class="nav-item <?php echo ($current_page == 'facility-admin-facility.php') ? 'active' : ''; ?>">
                    <span>🏥</span> Thông tin cơ sở
                </a>
                <a href="facility-admin-profile.php" class="nav-item <?php echo ($current_page == 'facility-admin-profile.php') ? 'active' : ''; ?>">
                    <span>👤</span> Tài khoản
                </a>
            </nav>
        </aside>

        <main class="admin-main">

