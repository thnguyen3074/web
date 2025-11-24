<?php
// Xử lý đăng nhập Facility Admin

session_start();
require_once '../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: facility-admin-login.php');
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    header('Location: facility-admin-login.php?error=1');
    exit();
}

$email = mysqli_real_escape_string($conn, $email);

$sql = "SELECT fa.*, f.name AS facility_name, f.type AS facility_type 
        FROM facility_admins fa 
        JOIN facilities f ON fa.facility_id = f.facility_id 
        WHERE fa.email = '$email'";
$result = mysqli_query($conn, $sql);
$facility_admin = mysqli_fetch_assoc($result);

if ($facility_admin && password_verify($password, $facility_admin['password'])) {
    $check_facility = "SELECT facility_id FROM facilities WHERE facility_id = " . intval($facility_admin['facility_id']);
    $result_check = mysqli_query($conn, $check_facility);
    if (mysqli_num_rows($result_check) == 0) {
        header('Location: facility-admin-login.php?error=1');
        exit();
    }
    
    $_SESSION['facility_admin_id'] = $facility_admin['admin_id'];
    $_SESSION['facility_id'] = $facility_admin['facility_id'];
    $_SESSION['facility_admin_name'] = $facility_admin['fullname'];
    $_SESSION['facility_name'] = $facility_admin['facility_name'];
    header('Location: facility-admin-dashboard.php');
    exit();
} else {
    header('Location: facility-admin-login.php?error=1');
    exit();
}
?>

