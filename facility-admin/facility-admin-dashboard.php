<?php
// Facility Admin Dashboard - Hiển thị thống kê của cơ sở y tế

$pageTitle = 'Tổng quan';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);

// Lấy thông tin cơ sở y tế
$sql_facility = "SELECT * FROM facilities WHERE facility_id = $facility_id";
$result_facility = mysqli_query($conn, $sql_facility);
$facility = mysqli_fetch_assoc($result_facility);

// Thống kê lịch hẹn của cơ sở
$sql_appointments_total = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id";
$result_appointments_total = mysqli_query($conn, $sql_appointments_total);
$appointments_total = mysqli_fetch_assoc($result_appointments_total)['total'];

// Thống kê lịch hẹn theo trạng thái
$sql_appointments_pending = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'pending'";
$result_appointments_pending = mysqli_query($conn, $sql_appointments_pending);
$appointments_pending = mysqli_fetch_assoc($result_appointments_pending)['total'];

$sql_appointments_confirmed = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'confirmed'";
$result_appointments_confirmed = mysqli_query($conn, $sql_appointments_confirmed);
$appointments_confirmed = mysqli_fetch_assoc($result_appointments_confirmed)['total'];

$sql_appointments_completed = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'completed'";
$result_appointments_completed = mysqli_query($conn, $sql_appointments_completed);
$appointments_completed = mysqli_fetch_assoc($result_appointments_completed)['total'];

$sql_specialties = "SELECT COUNT(*) AS total FROM facility_specialty WHERE facility_id = $facility_id";
$result_specialties = mysqli_query($conn, $sql_specialties);
$specialties_count = mysqli_fetch_assoc($result_specialties)['total'];

// Thống kê lịch hẹn theo thời gian
$today = date('Y-m-d');
$sql_appointments_today = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND appointment_date = '$today'";
$result_appointments_today = mysqli_query($conn, $sql_appointments_today);
$appointments_today = mysqli_fetch_assoc($result_appointments_today)['total'];

// Lịch hẹn trong tuần này (thứ 2 đến chủ nhật)
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$sql_appointments_week = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND appointment_date BETWEEN '$week_start' AND '$week_end'";
$result_appointments_week = mysqli_query($conn, $sql_appointments_week);
$appointments_week = mysqli_fetch_assoc($result_appointments_week)['total'];

// Lịch hẹn trong tháng này
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$sql_appointments_month = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND appointment_date BETWEEN '$month_start' AND '$month_end'";
$result_appointments_month = mysqli_query($conn, $sql_appointments_month);
$appointments_month = mysqli_fetch_assoc($result_appointments_month)['total'];

// Lịch hẹn sắp tới (7 ngày tới, chỉ pending và confirmed)
$next_week = date('Y-m-d', strtotime('+7 days'));
$sql_upcoming = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND appointment_date BETWEEN '$today' AND '$next_week' AND status IN ('pending', 'confirmed')";
$result_upcoming = mysqli_query($conn, $sql_upcoming);
$appointments_upcoming = mysqli_fetch_assoc($result_upcoming)['total'];

// Lấy lịch hẹn gần đây nhất (5 lịch hẹn)
// COALESCE: ưu tiên thông tin từ appointments, nếu NULL thì lấy từ users
$sql_recent = "SELECT a.*, 
               COALESCE(a.patient_name, u.fullname) AS display_name, 
               s.specialty_name 
                FROM appointments a
                LEFT JOIN users u ON a.user_id = u.user_id
                JOIN specialties s ON a.specialty_id = s.specialty_id
                WHERE a.facility_id = $facility_id
                ORDER BY a.created_at DESC
                LIMIT 5";
$result_recent = mysqli_query($conn, $sql_recent);
$recent_appointments = [];
if ($result_recent) {
    while ($row = mysqli_fetch_assoc($result_recent)) {
        $recent_appointments[] = $row;
    }
}

// Format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y');
}
?>

<div class="admin-content">
    <h1 class="page-title">Tổng quan - <?php echo htmlspecialchars($facility['name']); ?></h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3>Tổng lịch hẹn</h3>
                <p class="stat-number"><?php echo $appointments_total; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <h3>Chờ xác nhận</h3>
                <p class="stat-number"><?php echo $appointments_pending; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3>Đã xác nhận</h3>
                <p class="stat-number"><?php echo $appointments_confirmed; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✔️</div>
            <div class="stat-info">
                <h3>Đã hoàn thành</h3>
                <p class="stat-number"><?php echo $appointments_completed; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚕️</div>
            <div class="stat-info">
                <h3>Chuyên khoa</h3>
                <p class="stat-number"><?php echo $specialties_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📆</div>
            <div class="stat-info">
                <h3>Hôm nay</h3>
                <p class="stat-number"><?php echo $appointments_today; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3>Tuần này</h3>
                <p class="stat-number"><?php echo $appointments_week; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🗓️</div>
            <div class="stat-info">
                <h3>Tháng này</h3>
                <p class="stat-number"><?php echo $appointments_month; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏰</div>
            <div class="stat-info">
                <h3>Sắp tới (7 ngày)</h3>
                <p class="stat-number"><?php echo $appointments_upcoming; ?></p>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 15px;">Thông tin cơ sở y tế</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; font-weight: bold; width: 200px;">Tên cơ sở:</td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($facility['name']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Loại:</td>
                <td style="padding: 8px;"><?php echo ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Địa chỉ:</td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($facility['address']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Số điện thoại:</td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($facility['phone']); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Giờ làm việc:</td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($facility['working_hours']); ?></td>
            </tr>
        </table>
    </div>

    <!-- Lịch hẹn gần đây -->
    <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin: 0;">Lịch hẹn gần đây</h2>
            <a href="facility-admin-appointments.php" class="btn-admin-secondary" style="text-decoration: none; padding: 8px 16px;">Xem tất cả</a>
        </div>
        <?php if (empty($recent_appointments)): ?>
            <p style="color: #999; text-align: center; padding: 20px;">Chưa có lịch hẹn nào.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bệnh nhân</th>
                            <th>Chuyên khoa</th>
                            <th>Ngày khám</th>
                            <th>Giờ khám</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['appointment_id']; ?></td>
                                <td><?php echo !empty($appointment['display_name']) ? htmlspecialchars($appointment['display_name']) : '<span style="color: #999;">Khách</span>'; ?></td>
                                <td><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                                <td><?php echo formatDate($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $appointment['status']; ?>">
                                        <?php
                                        $status_text = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'completed' => 'Đã hoàn thành',
                                            'canceled' => 'Đã hủy'
                                        ];
                                        echo isset($status_text[$appointment['status']]) ? $status_text[$appointment['status']] : $appointment['status'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="facility-admin-appointment-detail.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn-admin-secondary" style="padding: 4px 8px; font-size: 12px; text-decoration: none;">Chi tiết</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'facility-admin-footer.php'; ?>

