<?php
// Database Configuration - Kết nối MySQLi
define('DB_HOST', 'localhost');
define('DB_NAME', 'medicare_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4'); // Hỗ trợ đầy đủ Unicode (emoji, ký tự đặc biệt)

// Tạo kết nối MySQLi (procedural style)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kiểm tra kết nối - dừng script nếu lỗi
if (!$conn) {
    die("Lỗi kết nối database: " . mysqli_connect_error());
}

// Thiết lập charset UTF8MB4 để hỗ trợ đầy đủ Unicode
mysqli_set_charset($conn, DB_CHARSET);
?>

