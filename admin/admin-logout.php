<?php
/**
 * Xử lý đăng xuất admin - Medicare
 */

session_start();

// Hủy tất cả session variables
$_SESSION = array();

// Hủy session cookie nếu có
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Hủy session
session_destroy();

// Redirect về trang đăng nhập
header('Location: admin-login.php');
exit();
?>

