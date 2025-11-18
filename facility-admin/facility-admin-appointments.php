<?php
/**
 * Facility Admin Appointments Management - Medicare
 * Quản lý lịch hẹn của cơ sở y tế
 */

$pageTitle = 'Quản lý lịch hẹn';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);

// Xử lý cập nhật trạng thái
if (isset($_GET['update_status']) && is_numeric($_GET['update_status']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['update_status']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    
    // Kiểm tra appointment thuộc về facility này
    $check_appointment = "SELECT appointment_id FROM appointments WHERE appointment_id = $appointment_id AND facility_id = $facility_id";
    $result_check = mysqli_query($conn, $check_appointment);
    if (mysqli_num_rows($result_check) > 0) {
        $sql_update = "UPDATE appointments SET status = '$status' WHERE appointment_id = $appointment_id AND facility_id = $facility_id";
        mysqli_query($conn, $sql_update);
    }
    header('Location: facility-admin-appointments.php');
    exit();
}

// Lấy danh sách appointments của cơ sở với JOIN
$appointments = [];
$sql = "SELECT a.*, 
               u.fullname AS patient_name,
               u.email AS patient_email,
               u.phone AS patient_phone,
               s.specialty_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
        WHERE a.facility_id = $facility_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Hàm format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y');
}

// Hàm format trạng thái
function formatStatus($status) {
    $status_text = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Đã hoàn thành',
        'canceled' => 'Đã hủy'
    ];
    return isset($status_text[$status]) ? $status_text[$status] : $status;
}

// Hàm format màu trạng thái
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
    <div class="page-header">
        <h1 class="page-title">Quản lý lịch hẹn</h1>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên bệnh nhân</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Chuyên khoa</th>
                    <th>Ngày khám</th>
                    <th>Giờ khám</th>
                    <th>Triệu chứng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr>
                        <td colspan="10">Chưa có lịch hẹn nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo $appointment['appointment_id']; ?></td>
                            <td><?php echo !empty($appointment['patient_name']) ? htmlspecialchars($appointment['patient_name']) : '<span style="color: #999;">Khách (chưa đăng ký)</span>'; ?></td>
                            <td><?php echo !empty($appointment['patient_email']) ? htmlspecialchars($appointment['patient_email']) : '<span style="color: #999;">-</span>'; ?></td>
                            <td><?php echo !empty($appointment['patient_phone']) ? htmlspecialchars($appointment['patient_phone']) : '<span style="color: #999;">-</span>'; ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                            <td><?php echo formatDate($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars(substr($appointment['symptoms'], 0, 50)) . (strlen($appointment['symptoms']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <span class="status-badge <?php echo getStatusClass($appointment['status']); ?>">
                                    <?php echo formatStatus($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] == 'pending'): ?>
                                    <a href="facility-admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=confirmed" class="btn-confirm">Xác nhận</a>
                                <?php endif; ?>
                                <?php if ($appointment['status'] == 'confirmed'): ?>
                                    <a href="facility-admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=completed" class="btn-confirm">Hoàn thành</a>
                                <?php endif; ?>
                                <?php if ($appointment['status'] != 'canceled' && $appointment['status'] != 'completed'): ?>
                                    <a href="facility-admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=canceled" class="btn-cancel-action" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">Hủy</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'facility-admin-footer.php'; ?>

