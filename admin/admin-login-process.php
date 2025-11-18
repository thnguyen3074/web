<?php
/**
 * Xử lý đăng nhập admin - Medicare
 */

session_start();
require_once '../config.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-login.php');
    exit();
}

// Lấy dữ liệu từ form
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($email) || empty($password)) {
    header('Location: admin-login.php?error=1');
    exit();
}

// Escape email để bảo mật
$email = mysqli_real_escape_string($conn, $email);

// Kiểm tra bảng admins có tồn tại không, nếu không thì tạo
$check_table = "SHOW TABLES LIKE 'admins'";
$table_exists = mysqli_query($conn, $check_table);
if (mysqli_num_rows($table_exists) == 0) {
    // Tạo bảng admins
    $create_table = "CREATE TABLE admins (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    mysqli_query($conn, $create_table);
    
    // Tạo admin mặc định
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $default_password = mysqli_real_escape_string($conn, $default_password);
    $insert_admin = "INSERT INTO admins (email, password, name) VALUES ('admin@medicare.vn', '$default_password', 'Administrator')";
    mysqli_query($conn, $insert_admin);
}

// Tìm admin theo email
$sql = "SELECT * FROM admins WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);

// Kiểm tra admin có tồn tại và mật khẩu đúng không
if ($admin && password_verify($password, $admin['password'])) {
    // Tạo session
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    
    // Redirect về dashboard
    header('Location: admin-dashboard.php');
    exit();
} else {
    // Email hoặc mật khẩu không đúng
    header('Location: admin-login.php?error=1');
    exit();
}
?>

