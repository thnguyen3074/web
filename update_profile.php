<?php
// Xử lý cập nhật thông tin profile - Cập nhật fullname và phone của user

session_start();
require_once 'config.php';

// Yêu cầu đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: UserProfile.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Validate input bắt buộc
if (empty($fullname) || empty($phone)) {
    header('Location: UserProfile.php?error=empty');
    exit();
}

// Escape dữ liệu để tránh SQL injection
$fullname = mysqli_real_escape_string($conn, $fullname);
$phone = mysqli_real_escape_string($conn, $phone);

// Cập nhật thông tin user
$sql = "UPDATE users SET fullname = '$fullname', phone = '$phone' WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

// Xử lý lỗi database
if (!$result) {
    error_log("Error updating profile: " . mysqli_error($conn));
    header('Location: UserProfile.php?error=update_failed');
    exit();
}

// Cập nhật session để hiển thị tên mới ngay lập tức
$_SESSION['fullname'] = $fullname;
header('Location: UserProfile.php?success=profile');
exit();
?>

