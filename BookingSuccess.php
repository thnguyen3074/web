<?php
/**
 * Đặt lịch thành công - Medicare
 * Hiển thị thông tin lịch hẹn vừa tạo
 */

$pageTitle = 'Đặt lịch thành công';
require_once 'config.php';
include 'header.php';

// Lấy appointment_id từ URL
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if ($appointment_id <= 0) {
    header('Location: Facility.php');
    exit();
}

// Kiểm tra user đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);

// Lấy thông tin lịch hẹn với JOIN (LEFT JOIN users vì có thể user_id = NULL)
// Cho phép cả user đã đăng nhập và guest xem lịch hẹn của họ
$sql = "SELECT a.*, f.name AS facility_name, s.specialty_name, u.fullname AS patient_name, u.email AS patient_email, u.phone AS patient_phone
        FROM appointments a
        JOIN facilities f ON a.facility_id = f.facility_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
        LEFT JOIN users u ON a.user_id = u.user_id
        WHERE a.appointment_id = $appointment_id";
        
// Nếu đã đăng nhập, chỉ cho phép xem lịch hẹn của chính user đó
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $sql .= " AND a.user_id = $user_id";
} else {
    // Nếu chưa đăng nhập, chỉ cho phép xem lịch hẹn có user_id = NULL (guest booking)
    $sql .= " AND a.user_id IS NULL";
}

$result = mysqli_query($conn, $sql);
$appointment = mysqli_fetch_assoc($result);

if (!$appointment) {
    header('Location: Facility.php');
    exit();
}

// Format ngày để hiển thị
$date_obj = new DateTime($appointment['appointment_date']);
$formatted_date = $date_obj->format('d/m/Y');
?>

<main class="page success-page">
    <section class="success-container">
        <div class="success-icon">✓</div>
        <h1>Đặt lịch thành công!</h1>
        <p class="success-message">
            Cảm ơn bạn! Lịch hẹn của bạn đã được ghi nhận.
        </p>

        <div class="success-card">
            <h3>Thông tin lịch hẹn</h3>
            <div class="success-info">
                <p><strong>Cơ sở y tế:</strong> <?php echo htmlspecialchars($appointment['facility_name']); ?></p>
                <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($appointment['specialty_name']); ?></p>
                <p><strong>Ngày khám:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
                <p><strong>Giờ khám:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                <p><strong>Triệu chứng:</strong> <?php echo nl2br(htmlspecialchars($appointment['symptoms'])); ?></p>
                <p><strong>Trạng thái:</strong> 
                    <?php 
                    $status_text = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Đã hoàn thành',
                        'canceled' => 'Đã hủy'
                    ];
                    echo isset($status_text[$appointment['status']]) ? $status_text[$appointment['status']] : $appointment['status'];
                    ?>
                </p>
            </div>
        </div>

        <div class="success-actions">
            <a href="index.php" class="btn-primary">Về trang chủ</a>
            <?php if ($isLoggedIn): ?>
                <a href="MyAppointments.php" class="btn-secondary">Xem lịch hẹn của tôi</a>
            <?php else: ?>
                <a href="login.php" class="btn-secondary">Đăng nhập để quản lý lịch hẹn</a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

