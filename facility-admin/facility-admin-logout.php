<?php
/**
 * Facility Admin Logout - Medicare
 */

session_start();

// Xóa tất cả session của facility admin
unset($_SESSION['facility_admin_id']);
unset($_SESSION['facility_id']);
unset($_SESSION['facility_admin_name']);
unset($_SESSION['facility_name']);

// Hủy session
session_destroy();

// Redirect về trang đăng nhập
header('Location: facility-admin-login.php');
exit();
?>

