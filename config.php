<?php
/**
 * Database Configuration File
 * Kết nối MySQL sử dụng MySQLi
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'medicare_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Tạo kết nối MySQLi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kiểm tra kết nối
if (!$conn) {
    die("Lỗi kết nối database: " . mysqli_connect_error());
}

// Thiết lập charset UTF8MB4
mysqli_set_charset($conn, DB_CHARSET);
?>

