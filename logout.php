<?php
// Xử lý đăng xuất - Hủy session và redirect về trang chủ

session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
header('Location: index.php');
exit();
?>

