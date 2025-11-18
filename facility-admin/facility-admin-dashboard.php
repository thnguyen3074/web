<?php
/**
 * Facility Admin Dashboard - Medicare
 * Hiển thị thống kê của cơ sở y tế
 */

$pageTitle = 'Dashboard';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);

// Lấy thông tin cơ sở y tế
$sql_facility = "SELECT * FROM facilities WHERE facility_id = $facility_id";
$result_facility = mysqli_query($conn, $sql_facility);
$facility = mysqli_fetch_assoc($result_facility);

// Lấy số lượng lịch hẹn của cơ sở
$sql_appointments_total = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id";
$result_appointments_total = mysqli_query($conn, $sql_appointments_total);
$appointments_total = mysqli_fetch_assoc($result_appointments_total)['total'];

// Lấy số lượng lịch hẹn chờ xác nhận
$sql_appointments_pending = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'pending'";
$result_appointments_pending = mysqli_query($conn, $sql_appointments_pending);
$appointments_pending = mysqli_fetch_assoc($result_appointments_pending)['total'];

// Lấy số lượng lịch hẹn đã xác nhận
$sql_appointments_confirmed = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'confirmed'";
$result_appointments_confirmed = mysqli_query($conn, $sql_appointments_confirmed);
$appointments_confirmed = mysqli_fetch_assoc($result_appointments_confirmed)['total'];

// Lấy số lượng lịch hẹn đã hoàn thành
$sql_appointments_completed = "SELECT COUNT(*) AS total FROM appointments WHERE facility_id = $facility_id AND status = 'completed'";
$result_appointments_completed = mysqli_query($conn, $sql_appointments_completed);
$appointments_completed = mysqli_fetch_assoc($result_appointments_completed)['total'];

// Lấy số lượng chuyên khoa của cơ sở
$sql_specialties = "SELECT COUNT(*) AS total FROM facility_specialty WHERE facility_id = $facility_id";
$result_specialties = mysqli_query($conn, $sql_specialties);
$specialties_count = mysqli_fetch_assoc($result_specialties)['total'];
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
</div>

<?php include 'facility-admin-footer.php'; ?>

