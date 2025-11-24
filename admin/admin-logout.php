<?php
// Xử lý đăng xuất admin

session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
header('Location: admin-login.php');
exit();
?>

