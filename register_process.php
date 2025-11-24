<?php
// Xử lý đăng ký - Kiểm tra và lưu thông tin người dùng mới

session_start();
require_once 'config.php';

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

// Validate input bắt buộc
if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
    header('Location: register.php?error=empty');
    exit();
}

// Kiểm tra password và confirm password khớp nhau
if ($password !== $confirmPassword) {
    header('Location: register.php?error=password');
    exit();
}

// Escape dữ liệu để tránh SQL injection
$fullname = mysqli_real_escape_string($conn, $fullname);
$email = mysqli_real_escape_string($conn, $email);
$phone = mysqli_real_escape_string($conn, $phone);

// Kiểm tra email đã tồn tại chưa
$sql_check = "SELECT user_id FROM users WHERE email = '$email'";
$result_check = mysqli_query($conn, $sql_check);
$existingUser = mysqli_fetch_assoc($result_check);

if ($existingUser) {
    header('Location: register.php?error=email');
    exit();
}

// Hash mật khẩu trước khi lưu vào database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);
$sql_insert = "INSERT INTO users (fullname, email, phone, password) VALUES ('$fullname', '$email', '$phone', '$hashedPassword')";
$result_insert = mysqli_query($conn, $sql_insert);

// Xử lý lỗi database (duplicate email hoặc lỗi khác)
if (!$result_insert) {
    $errorCode = mysqli_errno($conn);
    error_log("Register error: " . mysqli_error($conn));
    
    // MySQL error 1062 = Duplicate entry
    if ($errorCode == 1062) {
        header('Location: register.php?error=email');
    } else {
        header('Location: register.php?error=empty');
    }
    exit();
}

header('Location: login.php?success=1');
exit();
?>

