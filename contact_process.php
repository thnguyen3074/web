<?php
/**
 * Xử lý form liên hệ - Medicare
 * Lưu tin nhắn liên hệ vào database
 */

session_start();
require_once 'config.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Contact.php');
    exit();
}

// Lấy dữ liệu từ form
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($fullname) || empty($email) || empty($subject) || empty($message)) {
    header('Location: Contact.php?error=empty');
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: Contact.php?error=invalid_email');
    exit();
}

// Escape dữ liệu để bảo mật
$fullname = mysqli_real_escape_string($conn, $fullname);
$email = mysqli_real_escape_string($conn, $email);
$subject = mysqli_real_escape_string($conn, $subject);
$message = mysqli_real_escape_string($conn, $message);

// Kiểm tra bảng contact_messages có tồn tại không, nếu không thì tạo
$check_table = "SHOW TABLES LIKE 'contact_messages'";
$table_exists = mysqli_query($conn, $check_table);
if (mysqli_num_rows($table_exists) == 0) {
    // Tạo bảng contact_messages
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

// Lưu tin nhắn vào database
$sql = "INSERT INTO contact_messages (fullname, email, subject, message) VALUES ('$fullname', '$email', '$subject', '$message')";
$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Error saving contact message: " . mysqli_error($conn));
    header('Location: Contact.php?error=send_failed');
    exit();
}

// Redirect về trang liên hệ với thông báo thành công
header('Location: Contact.php?success=1');
exit();
?>

