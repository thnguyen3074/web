<?php
// Xử lý đăng nhập - Kiểm tra thông tin đăng nhập và tạo session

session_start();
require_once 'config.php';

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($email) || empty($password)) {
    header('Location: login.php?error=1');
    exit();
}

// Escape email để tránh SQL injection
$email = mysqli_real_escape_string($conn, $email);
$sql = "SELECT user_id, fullname, email, password FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Xác thực mật khẩu bằng password_verify (so sánh với hash trong DB)
if ($user && password_verify($password, $user['password'])) {
    // Tạo session sau khi xác thực thành công
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    header('Location: index.php');
    exit();
} else {
    // Không hiển thị chi tiết lỗi để bảo mật
    header('Location: login.php?error=1');
    exit();
}
?>

