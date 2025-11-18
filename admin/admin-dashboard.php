<?php
/**
 * Admin Dashboard - Medicare
 * Hiển thị thống kê tổng quan
 */

$pageTitle = 'Dashboard';
require_once '../config.php';
include 'admin-header.php';

// Lấy số lượng users
$sql_users = "SELECT COUNT(*) AS total FROM users";
$result_users = mysqli_query($conn, $sql_users);
$users_count = mysqli_fetch_assoc($result_users)['total'];

// Lấy số lượng facilities
$sql_facilities = "SELECT COUNT(*) AS total FROM facilities";
$result_facilities = mysqli_query($conn, $sql_facilities);
$facilities_count = mysqli_fetch_assoc($result_facilities)['total'];

// Lấy số lượng bệnh viện
$sql_hospitals = "SELECT COUNT(*) AS total FROM facilities WHERE type = 'hospital'";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
$hospitals_count = mysqli_fetch_assoc($result_hospitals)['total'];

// Lấy số lượng phòng khám
$sql_clinics = "SELECT COUNT(*) AS total FROM facilities WHERE type = 'clinic'";
$result_clinics = mysqli_query($conn, $sql_clinics);
$clinics_count = mysqli_fetch_assoc($result_clinics)['total'];

// Lấy số lượng chuyên khoa
$sql_specialties = "SELECT COUNT(*) AS total FROM specialties";
$result_specialties = mysqli_query($conn, $sql_specialties);
$specialties_count = mysqli_fetch_assoc($result_specialties)['total'];

// Lấy số lượng lịch hẹn
$sql_appointments = "SELECT COUNT(*) AS total FROM appointments";
$result_appointments = mysqli_query($conn, $sql_appointments);
$appointments_count = mysqli_fetch_assoc($result_appointments)['total'];
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
    </div>
</div>

<?php include 'admin-footer.php'; ?>

