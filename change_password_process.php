<?php
// Xử lý đổi mật khẩu - Kiểm tra và cập nhật mật khẩu mới

session_start();
require_once 'config.php';

// Yêu cầu đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: change_password.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Validate input
if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    header('Location: change_password.php?error=empty');
    exit();
}

// Kiểm tra độ dài mật khẩu mới (tối thiểu 6 ký tự)
if (strlen($new_password) < 6) {
    header('Location: change_password.php?error=password_length');
    exit();
}

// Kiểm tra password mới và confirm password khớp nhau
if ($new_password !== $confirm_password) {
    header('Location: change_password.php?error=password_mismatch');
    exit();
}

// Lấy mật khẩu hiện tại từ database
$sql = "SELECT password FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Xác thực mật khẩu cũ
if (!password_verify($old_password, $user['password'])) {
    header('Location: change_password.php?error=old_password');
    exit();
}

// Hash mật khẩu mới và cập nhật
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$hashed_password = mysqli_real_escape_string($conn, $hashed_password);
$sql_update = "UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id";
$result_update = mysqli_query($conn, $sql_update);

// Xử lý lỗi database
if (!$result_update) {
    error_log("Error changing password: " . mysqli_error($conn));
    header('Location: change_password.php?error=update_failed');
    exit();
}

header('Location: UserProfile.php?success=password');
exit();
?>

