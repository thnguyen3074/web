<?php
// Admin Dashboard - Hiển thị thống kê tổng quan

$pageTitle = 'Tổng quan';
require_once '../config.php';
include 'admin-header.php';
$sql_users = "SELECT COUNT(*) AS total FROM users";
$result_users = mysqli_query($conn, $sql_users);
$users_count = mysqli_fetch_assoc($result_users)['total'];

// Lấy thống kê tổng quan
$sql_facilities = "SELECT COUNT(*) AS total FROM facilities";
$result_facilities = mysqli_query($conn, $sql_facilities);
$facilities_count = mysqli_fetch_assoc($result_facilities)['total'];

$sql_hospitals = "SELECT COUNT(*) AS total FROM facilities WHERE type = 'hospital'";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
$hospitals_count = mysqli_fetch_assoc($result_hospitals)['total'];

$sql_clinics = "SELECT COUNT(*) AS total FROM facilities WHERE type = 'clinic'";
$result_clinics = mysqli_query($conn, $sql_clinics);
$clinics_count = mysqli_fetch_assoc($result_clinics)['total'];

$sql_specialties = "SELECT COUNT(*) AS total FROM specialties";
$result_specialties = mysqli_query($conn, $sql_specialties);
$specialties_count = mysqli_fetch_assoc($result_specialties)['total'];

$sql_appointments = "SELECT COUNT(*) AS total FROM appointments";
$result_appointments = mysqli_query($conn, $sql_appointments);
$appointments_count = mysqli_fetch_assoc($result_appointments)['total'];

// Thống kê lịch hẹn theo trạng thái
$sql_pending = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'pending'";
$result_pending = mysqli_query($conn, $sql_pending);
$pending_count = mysqli_fetch_assoc($result_pending)['total'];

$sql_confirmed = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'confirmed'";
$result_confirmed = mysqli_query($conn, $sql_confirmed);
$confirmed_count = mysqli_fetch_assoc($result_confirmed)['total'];

$sql_completed = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'completed'";
$result_completed = mysqli_query($conn, $sql_completed);
$completed_count = mysqli_fetch_assoc($result_completed)['total'];

// Thống kê lịch hẹn hôm nay
$today = date('Y-m-d');
$sql_today = "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = '$today'";
$result_today = mysqli_query($conn, $sql_today);
$today_count = mysqli_fetch_assoc($result_today)['total'];

$sql_facility_admins = "SELECT COUNT(*) AS total FROM facility_admins";
$result_facility_admins = mysqli_query($conn, $sql_facility_admins);
$facility_admins_count = mysqli_fetch_assoc($result_facility_admins)['total'];

// Thống kê yêu cầu hỗ trợ
$sql_contact_messages = "SELECT COUNT(*) AS total FROM contact_messages";
$result_contact_messages = mysqli_query($conn, $sql_contact_messages);
$contact_messages_count = mysqli_fetch_assoc($result_contact_messages)['total'];

// Yêu cầu hỗ trợ hôm nay
$sql_contact_today = "SELECT COUNT(*) AS total FROM contact_messages WHERE DATE(created_at) = '$today'";
$result_contact_today = mysqli_query($conn, $sql_contact_today);
$contact_today_count = mysqli_fetch_assoc($result_contact_today)['total'];

// Lấy lịch hẹn gần đây nhất (5 lịch hẹn)
// COALESCE: ưu tiên thông tin từ appointments, nếu NULL thì lấy từ users
$sql_recent = "SELECT a.*, 
               COALESCE(a.patient_name, u.fullname) AS display_name, 
               f.name AS facility_name,
               s.specialty_name 
                FROM appointments a
                LEFT JOIN users u ON a.user_id = u.user_id
                JOIN facilities f ON a.facility_id = f.facility_id
                JOIN specialties s ON a.specialty_id = s.specialty_id
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
    <h1 class="page-title">Tổng quan</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-info">
                <h3>Bệnh viện</h3>
                <p class="stat-number"><?php echo $hospitals_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏨</div>
            <div class="stat-info">
                <h3>Phòng khám</h3>
                <p class="stat-number"><?php echo $clinics_count; ?></p>
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
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3>Lịch hẹn</h3>
                <p class="stat-number"><?php echo $appointments_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Người dùng</h3>
                <p class="stat-number"><?php echo $users_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <h3>Chờ xác nhận</h3>
                <p class="stat-number"><?php echo $pending_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3>Đã xác nhận</h3>
                <p class="stat-number"><?php echo $confirmed_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✔️</div>
            <div class="stat-info">
                <h3>Đã hoàn thành</h3>
                <p class="stat-number"><?php echo $completed_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📆</div>
            <div class="stat-info">
                <h3>Lịch hẹn hôm nay</h3>
                <p class="stat-number"><?php echo $today_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👨‍💼</div>
            <div class="stat-info">
                <h3>Quản trị viên cơ sở y tế</h3>
                <p class="stat-number"><?php echo $facility_admins_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-info">
                <h3>Yêu cầu hỗ trợ</h3>
                <p class="stat-number"><?php echo $contact_messages_count; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📨</div>
            <div class="stat-info">
                <h3>Yêu cầu hôm nay</h3>
                <p class="stat-number"><?php echo $contact_today_count; ?></p>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn gần đây -->
    <div class="card" style="margin-top: 30px;">
        <div class="flex-between" style="margin-bottom: 15px;">
            <h2 style="margin: 0;">Lịch hẹn gần đây</h2>
            <a href="admin-appointments.php" class="btn-admin-secondary">Xem tất cả</a>
        </div>
        <?php if (empty($recent_appointments)): ?>
            <p class="text-muted" style="text-align: center; padding: 20px;">Chưa có lịch hẹn nào.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bệnh nhân</th>
                            <th>Cơ sở y tế</th>
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
                                <td><?php echo !empty($appointment['display_name']) ? htmlspecialchars($appointment['display_name']) : '<span class="text-muted">Khách</span>'; ?></td>
                                <td><?php echo htmlspecialchars($appointment['facility_name']); ?></td>
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
                                    <a href="admin-appointment-detail.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn-admin-secondary btn-sm">Chi tiết</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'admin-footer.php'; ?>

