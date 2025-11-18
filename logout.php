<?php
/**
 * Xử lý đăng xuất - Medicare
 * Hủy session và redirect về trang chủ
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

// Redirect về trang chủ
header('Location: index.php');
exit();
?>

