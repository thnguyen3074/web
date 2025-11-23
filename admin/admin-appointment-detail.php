<?php
/**
 * Admin Appointment Detail - Medicare
 * Xem chi tiết lịch hẹn
 */

$pageTitle = 'Chi tiết lịch hẹn';
require_once '../config.php';
include 'admin-header.php';

$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($appointment_id == 0) {
    header('Location: admin-appointments.php');
    exit();
}

// Lấy thông tin chi tiết lịch hẹn
// Ưu tiên hiển thị thông tin từ appointments (patient_name, patient_email, patient_phone)
// Nếu không có thì mới lấy từ users (cho các lịch hẹn cũ)
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
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="page-title">Chi tiết lịch hẹn #<?php echo $appointment['appointment_id']; ?></h1>
        <a href="admin-appointments.php" class="btn-admin-secondary" style="text-decoration: none; padding: 8px 16px;">← Quay lại</a>
    </div>

    <div style="display: grid; gap: 20px;">
        <!-- Thông tin lịch hẹn -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Thông tin lịch hẹn</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; font-weight: bold; width: 200px; border-bottom: 1px solid #eee;">Mã lịch hẹn:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">#<?php echo $appointment['appointment_id']; ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Ngày khám:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo formatDate($appointment['appointment_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Giờ khám:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Chuyên khoa:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Trạng thái:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <span class="status-badge <?php echo getStatusClass($appointment['status']); ?>">
                            <?php echo formatStatus($appointment['status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Ngày tạo:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">Cập nhật lần cuối:</td>
                    <td style="padding: 10px;"><?php echo date('d/m/Y H:i', strtotime($appointment['updated_at'])); ?></td>
                </tr>
            </table>
        </div>

        <!-- Thông tin bệnh nhân -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Thông tin bệnh nhân</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; font-weight: bold; width: 200px; border-bottom: 1px solid #eee;">Họ tên:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <?php echo !empty($appointment['display_name']) ? htmlspecialchars($appointment['display_name']) : '<span style="color: #999;">Khách (chưa đăng ký)</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Email:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <?php echo !empty($appointment['display_email']) ? htmlspecialchars($appointment['display_email']) : '<span style="color: #999;">-</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Số điện thoại:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <?php echo !empty($appointment['display_phone']) ? htmlspecialchars($appointment['display_phone']) : '<span style="color: #999;">-</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">Tài khoản:</td>
                    <td style="padding: 10px;">
                        <?php if ($appointment['user_id']): ?>
                            <span style="color: #28a745;">Đã đăng ký (User ID: <?php echo $appointment['user_id']; ?>)</span>
                        <?php else: ?>
                            <span style="color: #999;">Khách (chưa đăng ký)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Triệu chứng -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Triệu chứng / Mô tả</h2>
            <div style="padding: 15px; background: #f8f9fa; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">
                <?php echo htmlspecialchars($appointment['symptoms']); ?>
            </div>
        </div>

        <!-- Thông tin cơ sở y tế -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Thông tin cơ sở y tế</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; font-weight: bold; width: 200px; border-bottom: 1px solid #eee;">Tên cơ sở:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($appointment['facility_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Loại:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo ($appointment['facility_type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Địa chỉ:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($appointment['facility_address']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">Số điện thoại:</td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($appointment['facility_phone']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Hành động -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Hành động</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <?php if ($appointment['status'] == 'pending'): ?>
                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=confirmed" class="btn-confirm" style="text-decoration: none; padding: 10px 20px;">Xác nhận lịch hẹn</a>
                <?php endif; ?>
                <?php if ($appointment['status'] != 'canceled' && $appointment['status'] != 'completed'): ?>
                    <a href="admin-appointments.php?update_status=<?php echo $appointment['appointment_id']; ?>&status=canceled" class="btn-cancel-action" style="text-decoration: none; padding: 10px 20px;" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">Hủy lịch hẹn</a>
                <?php endif; ?>
                <a href="admin-appointments.php?delete=<?php echo $appointment['appointment_id']; ?>" class="btn-delete" style="text-decoration: none; padding: 10px 20px;" onclick="return confirm('Bạn có chắc muốn xóa lịch hẹn này?')">Xóa lịch hẹn</a>
                <a href="admin-appointments.php" class="btn-admin-secondary" style="text-decoration: none; padding: 10px 20px;">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>

<?php include 'admin-footer.php'; ?>

