<?php
/**
 * Xử lý đăng nhập - Medicare
 * Kiểm tra thông tin đăng nhập và tạo session
 */

session_start();
require_once 'config.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Lấy dữ liệu từ form
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($email) || empty($password)) {
    header('Location: login.php?error=1');
    exit();
}

// Tìm user theo email - dùng mysqli_real_escape_string để bảo mật
$email = mysqli_real_escape_string($conn, $email);
$sql = "SELECT user_id, fullname, email, password FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Kiểm tra user có tồn tại và mật khẩu đúng không
if ($user && password_verify($password, $user['password'])) {
    // Tạo session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    
    // Redirect về trang chủ
    header('Location: index.php');
    exit();
} else {
    // Email hoặc mật khẩu không đúng
    header('Location: login.php?error=1');
    exit();
}
?>

