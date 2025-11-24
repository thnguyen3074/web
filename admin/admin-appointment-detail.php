<?php
// Admin Appointment Detail - Xem chi tiết lịch hẹn

$pageTitle = 'Chi tiết lịch hẹn';
require_once '../config.php';
include 'admin-header.php';

$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($appointment_id == 0) {
    header('Location: admin-appointments.php');
    exit();
}

// Lấy thông tin chi tiết appointment
// COALESCE: ưu tiên thông tin từ appointments, nếu NULL thì lấy từ users (cho lịch hẹn cũ)
$sql = "SELECT a.*, 
               COALESCE(a.patient_name, u.fullname) AS display_name,
               COALESCE(a.patient_email, u.email) AS display_email,
               COALESCE(a.patient_phone, u.phone) AS display_phone,
               s.specialty_name,
               f.name AS facility_name,
               f.address AS facility_address,
               f.phone AS facility_phone,
               f.type AS facility_type
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
        JOIN facilities f ON a.facility_id = f.facility_id
        WHERE a.appointment_id = $appointment_id";
$result = mysqli_query($conn, $sql);
$appointment = mysqli_fetch_assoc($result);

if (!$appointment) {
    header('Location: admin-appointments.php');
    exit();
}

// Format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y');
}

// Format trạng thái
function formatStatus($status) {
    $status_text = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Đã hoàn thành',
        'canceled' => 'Đã hủy'
    ];
    return isset($status_text[$status]) ? $status_text[$status] : $status;
}

// Format màu trạng thái
function getStatusClass($status) {
    $status_classes = [
        'pending' => 'pending',
        'confirmed' => 'confirmed',
        'completed' => 'completed',
        'canceled' => 'canceled'
    ];
    return isset($status_classes[$status]) ? $status_classes[$status] : '';
}
?>

<div class="admin-content">
    <div class="page-header" style="margin-bottom: 20px;">
        <h1 class="page-title">Chi tiết lịch hẹn #<?php echo $appointment['appointment_id']; ?></h1>
        <a href="admin-appointments.php" class="btn-admin-secondary">← Quay lại</a>
    </div>

    <div class="grid">
        <!-- Thông tin lịch hẹn -->
        <div class="card">
            <h2>Thông tin lịch hẹn</h2>
            <table class="info-table">
                <tr>
                    <td>Mã lịch hẹn:</td>
                    <td>#<?php echo $appointment['appointment_id']; ?></td>
                </tr>
                <tr>
                    <td>Ngày khám:</td>
                    <td><?php echo formatDate($appointment['appointment_date']); ?></td>
                </tr>
                <tr>
                    <td>Giờ khám:</td>
                    <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                </tr>
                <tr>
                    <td>Chuyên khoa:</td>
                    <td><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                </tr>
                <tr>
                    <td>Trạng thái:</td>
                    <td>
                        <span class="status-badge <?php echo getStatusClass($appointment['status']); ?>">
                            <?php echo formatStatus($appointment['status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Ngày tạo:</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?></td>
                </tr>
                <tr>
                    <td>Cập nhật lần cuối:</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($appointment['updated_at'])); ?></td>
                </tr>
            </table>
        </div>

        <!-- Thông tin bệnh nhân -->
        <div class="card">
            <h2>Thông tin bệnh nhân</h2>
            <table class="info-table">
                <tr>
                    <td>Họ tên:</td>
                    <td>
                        <?php echo !empty($appointment['display_name']) ? htmlspecialchars($appointment['display_name']) : '<span class="text-muted">Khách (chưa đăng ký)</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <?php echo !empty($appointment['display_email']) ? htmlspecialchars($appointment['display_email']) : '<span class="text-muted">-</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td>
                        <?php echo !empty($appointment['display_phone']) ? htmlspecialchars($appointment['display_phone']) : '<span class="text-muted">-</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Tài khoản:</td>
                    <td>
                        <?php if ($appointment['user_id']): ?>
                            <span class="text-success">Đã đăng ký (User ID: <?php echo $appointment['user_id']; ?>)</span>
                        <?php else: ?>
                            <span class="text-muted">Khách (chưa đăng ký)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Triệu chứng -->
        <div class="card">
            <h2>Triệu chứng / Mô tả</h2>
            <div class="content-box">
                <?php echo htmlspecialchars($appointment['symptoms']); ?>
            </div>
        </div>

        <!-- Thông tin cơ sở y tế -->
        <div class="card">
            <h2>Thông tin cơ sở y tế</h2>
            <table class="info-table">
                <tr>
                    <td>Tên cơ sở:</td>
                    <td><?php echo htmlspecialchars($appointment['facility_name']); ?></td>
                </tr>
                <tr>
                    <td>Loại:</td>
                    <td><?php echo ($appointment['facility_type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?></td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td><?php echo htmlspecialchars($appointment['facility_address']); ?></td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td><?php echo htmlspecialchars($appointment['facility_phone']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Hành động -->
        <div class="card">
            <h2>Hành động</h2>
            <div class="flex-wrap">
                <?php if ($appointment['status'] == 'pending'): ?>
                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=confirmed" class="btn-confirm">Xác nhận lịch hẹn</a>
                <?php endif; ?>
                <?php if ($appointment['status'] != 'canceled' && $appointment['status'] != 'completed'): ?>
                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=canceled" class="btn-cancel-action" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">Hủy lịch hẹn</a>
                <?php endif; ?>
                <a href="admin-appointments.php?delete=<?php echo $appointment['appointment_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa lịch hẹn này?')">Xóa lịch hẹn</a>
                <a href="admin-appointments.php" class="btn-admin-secondary">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>

<?php include 'admin-footer.php'; ?>

