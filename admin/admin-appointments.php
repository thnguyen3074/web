<?php
/**
 * Admin Appointments Management - Medicare
 * Quản lý lịch hẹn
 */

$pageTitle = 'Quản lý lịch hẹn';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa appointment
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $appointment_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM appointments WHERE appointment_id = $appointment_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-appointments.php');
    exit();
}

// Xử lý cập nhật trạng thái
if (isset($_GET['update_status']) && is_numeric($_GET['update_status']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['update_status']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $sql_update = "UPDATE appointments SET status = '$status' WHERE appointment_id = $appointment_id";
    mysqli_query($conn, $sql_update);
    header('Location: admin-appointments.php');
    exit();
}

// Lấy danh sách appointments với JOIN (LEFT JOIN users để hiển thị cả guest bookings)
$appointments = [];
$sql = "SELECT a.*, u.fullname AS patient_name, u.email AS patient_email, u.phone AS patient_phone, f.name AS facility_name, s.specialty_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        JOIN facilities f ON a.facility_id = f.facility_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
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
        'pending' => 'Chờ',
        'confirmed' => 'Xác nhận',
        'completed' => 'Đã hoàn thành',
        'canceled' => 'Hủy'
    ];
    return isset($status_text[$status]) ? $status_text[$status] : $status;
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
                    <th>Cơ sở y tế</th>
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
                        <td colspan="9">Chưa có lịch hẹn nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo $appointment['appointment_id']; ?></td>
                            <td>
                                <?php if (!empty($appointment['patient_name'])): ?>
                                    <?php echo htmlspecialchars($appointment['patient_name']); ?>
                                    <?php if (!empty($appointment['patient_email'])): ?>
                                        <br><small style="color: #666;"><?php echo htmlspecialchars($appointment['patient_email']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #999;">Khách (chưa đăng ký)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($appointment['facility_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                            <td><?php echo formatDate($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars(substr($appointment['symptoms'], 0, 50)) . (strlen($appointment['symptoms']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <span class="status-badge <?php echo $appointment['status']; ?>">
                                    <?php echo formatStatus($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] == 'pending'): ?>
                                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=confirmed" class="btn-confirm">Xác nhận</a>
                                <?php endif; ?>
                                <?php if ($appointment['status'] != 'canceled' && $appointment['status'] != 'completed'): ?>
                                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=canceled" class="btn-cancel-action" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">Hủy</a>
                                <?php endif; ?>
                                <a href="admin-appointments.php?delete=<?php echo $appointment['appointment_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa lịch hẹn này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'admin-footer.php'; ?>

