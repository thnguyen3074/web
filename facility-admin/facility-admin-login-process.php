<?php
/**
 * Xử lý đăng nhập Facility Admin - Medicare
 */

session_start();
require_once '../config.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: facility-admin-login.php');
    exit();
}

// Lấy dữ liệu từ form
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($email) || empty($password)) {
    header('Location: facility-admin-login.php?error=1');
    exit();
}

// Escape email để bảo mật
$email = mysqli_real_escape_string($conn, $email);

// Tìm facility admin theo email
$sql = "SELECT fa.*, f.name AS facility_name, f.type AS facility_type 
        FROM facility_admins fa 
        JOIN facilities f ON fa.facility_id = f.facility_id 
        WHERE fa.email = '$email'";
$result = mysqli_query($conn, $sql);
$facility_admin = mysqli_fetch_assoc($result);

// Kiểm tra facility admin có tồn tại và mật khẩu đúng không
if ($facility_admin && password_verify($password, $facility_admin['password'])) {
    // Kiểm tra facility còn tồn tại không
    $check_facility = "SELECT facility_id FROM facilities WHERE facility_id = " . intval($facility_admin['facility_id']);
    $result_check = mysqli_query($conn, $check_facility);
    if (mysqli_num_rows($result_check) == 0) {
        // Facility đã bị xóa
        header('Location: facility-admin-login.php?error=1');
        exit();
    }
    
    // Tạo session
    $_SESSION['facility_admin_id'] = $facility_admin['admin_id'];
    $_SESSION['facility_id'] = $facility_admin['facility_id'];
    $_SESSION['facility_admin_name'] = $facility_admin['fullname'];
    $_SESSION['facility_name'] = $facility_admin['facility_name'];
    
    // Redirect về dashboard
    header('Location: facility-admin-dashboard.php');
    exit();
} else {
    // Email hoặc mật khẩu không đúng
    header('Location: facility-admin-login.php?error=1');
    exit();
}
?>

