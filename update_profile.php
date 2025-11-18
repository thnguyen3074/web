<?php
/**
 * Xử lý cập nhật thông tin profile - Medicare
 * Cập nhật fullname và phone của user
 */

session_start();
require_once 'config.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: UserProfile.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu từ form
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Validate input
if (empty($fullname) || empty($phone)) {
    header('Location: UserProfile.php?error=empty');
    exit();
}

// Escape dữ liệu để bảo mật
$fullname = mysqli_real_escape_string($conn, $fullname);
$phone = mysqli_real_escape_string($conn, $phone);

// Cập nhật thông tin user
$sql = "UPDATE users SET fullname = '$fullname', phone = '$phone' WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Error updating profile: " . mysqli_error($conn));
    header('Location: UserProfile.php?error=update_failed');
    exit();
}

// Cập nhật session fullname
$_SESSION['fullname'] = $fullname;

// Redirect về trang profile với thông báo thành công
header('Location: UserProfile.php?success=profile');
exit();
?>

