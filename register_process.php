<?php
/**
 * Xử lý đăng ký - Medicare
 * Kiểm tra và lưu thông tin người dùng mới
 */

session_start();
require_once 'config.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// Lấy dữ liệu từ form
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

// Validate input
if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
    header('Location: register.php?error=empty');
    exit();
}

// Kiểm tra password trùng nhau
if ($password !== $confirmPassword) {
    header('Location: register.php?error=password');
    exit();
}

// Escape các giá trị input để bảo mật
$fullname = mysqli_real_escape_string($conn, $fullname);
$email = mysqli_real_escape_string($conn, $email);
$phone = mysqli_real_escape_string($conn, $phone);

// Kiểm tra email đã tồn tại chưa
$sql_check = "SELECT user_id FROM users WHERE email = '$email'";
$result_check = mysqli_query($conn, $sql_check);
$existingUser = mysqli_fetch_assoc($result_check);

if ($existingUser) {
    // Email đã tồn tại
    header('Location: register.php?error=email');
    exit();
}

// Hash mật khẩu
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);

// Thêm user mới vào database
$sql_insert = "INSERT INTO users (fullname, email, phone, password) VALUES ('$fullname', '$email', '$phone', '$hashedPassword')";
$result_insert = mysqli_query($conn, $sql_insert);

if (!$result_insert) {
    // Lỗi database
    $errorCode = mysqli_errno($conn);
    error_log("Register error: " . mysqli_error($conn));
    
    // Kiểm tra nếu lỗi do duplicate email (MySQL error code 1062)
    if ($errorCode == 1062) {
        header('Location: register.php?error=email');
    } else {
        header('Location: register.php?error=empty');
    }
    exit();
}

// Đăng ký thành công, redirect về trang đăng nhập
header('Location: login.php?success=1');
exit();
?>

