<?php
// Xử lý form liên hệ - Lưu tin nhắn liên hệ vào database

session_start();
require_once 'config.php';

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Contact.php');
    exit();
}

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input bắt buộc
if (empty($fullname) || empty($email) || empty($subject) || empty($message)) {
    header('Location: Contact.php?error=empty');
    exit();
}

// Validate định dạng email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: Contact.php?error=invalid_email');
    exit();
}

// Escape dữ liệu để tránh SQL injection
$fullname = mysqli_real_escape_string($conn, $fullname);
$email = mysqli_real_escape_string($conn, $email);
$subject = mysqli_real_escape_string($conn, $subject);
$message = mysqli_real_escape_string($conn, $message);

// Tự động tạo bảng nếu chưa tồn tại (auto-migration)
$check_table = "SHOW TABLES LIKE 'contact_messages'";
$table_exists = mysqli_query($conn, $check_table);
if (mysqli_num_rows($table_exists) == 0) {
    $create_table = "CREATE TABLE contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    mysqli_query($conn, $create_table);
}

$sql = "INSERT INTO contact_messages (fullname, email, subject, message) VALUES ('$fullname', '$email', '$subject', '$message')";
$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Error saving contact message: " . mysqli_error($conn));
    header('Location: Contact.php?error=send_failed');
    exit();
}

header('Location: Contact.php?success=1');
exit();
?>

