<?php
// Xử lý đặt lịch - Lưu lịch hẹn vào database

session_start();
require_once 'config.php';

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Facility.php');
    exit();
}

// Kiểm tra user đã đăng nhập chưa (cho phép guest booking)
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 'NULL';

// Lấy dữ liệu từ form
$facility_id = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;
$specialty_id = isset($_POST['specialty_id']) ? intval($_POST['specialty_id']) : 0;
$appointment_date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$appointment_time = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
$symptoms = isset($_POST['symptoms']) ? trim($_POST['symptoms']) : '';

// Lấy thông tin cá nhân từ form (luôn yêu cầu, kể cả khi đã đăng nhập)
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Validate dữ liệu bắt buộc
if ($facility_id <= 0 || $specialty_id <= 0 || empty($appointment_date) || empty($appointment_time) || empty($symptoms)) {
    header('Location: Facility.php');
    exit();
}

// Validate thông tin cá nhân
if (empty($fullname) || empty($email) || empty($phone)) {
    header('Location: Booking.php?facility_id=' . $facility_id);
    exit();
}

// Validate date/time - thời gian khám phải sau thời gian hiện tại
$appointment_datetime = $appointment_date . ' ' . $appointment_time . ':00';
$current_datetime = date('Y-m-d H:i:s');

// Ngăn chặn đặt lịch trong quá khứ
if (strtotime($appointment_datetime) <= strtotime($current_datetime)) {
    header('Location: Booking.php?facility_id=' . $facility_id . '&error=invalid_time');
    exit();
}

// Escape dữ liệu để bảo mật
$appointment_date = mysqli_real_escape_string($conn, $appointment_date);
$appointment_time = mysqli_real_escape_string($conn, $appointment_time);
$symptoms = mysqli_real_escape_string($conn, $symptoms);
$fullname = mysqli_real_escape_string($conn, $fullname);
$email = mysqli_real_escape_string($conn, $email);
$phone = mysqli_real_escape_string($conn, $phone);

// Lưu lịch hẹn vào database (user_id có thể NULL nếu chưa đăng nhập - guest booking)
$sql = "INSERT INTO appointments (user_id, patient_name, patient_email, patient_phone, facility_id, specialty_id, appointment_date, appointment_time, symptoms, status)
        VALUES ($user_id, '$fullname', '$email', '$phone', $facility_id, $specialty_id, '$appointment_date', '$appointment_time', '$symptoms', 'pending')";
$result = mysqli_query($conn, $sql);

// Xử lý lỗi database
if (!$result) {
    error_log("Booking error: " . mysqli_error($conn));
    header('Location: Booking.php?facility_id=' . $facility_id . '&error=1');
    exit();
}

// Lấy appointment_id vừa tạo
$appointment_id = mysqli_insert_id($conn);

// Redirect sang trang thành công
header('Location: BookingSuccess.php?appointment_id=' . $appointment_id);
exit();
?>

