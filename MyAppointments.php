<?php
// Lịch hẹn của tôi - Hiển thị tất cả lịch hẹn của user đã đăng nhập

$pageTitle = 'Lịch hẹn của tôi';
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Hủy lịch hẹn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'], $_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $user_id_escaped = intval($user_id);
    
    // Kiểm tra lịch hẹn thuộc về user này và chỉ cho phép hủy nếu đang pending/confirmed
    $sql_check = "SELECT appointment_id FROM appointments WHERE appointment_id = $appointment_id AND user_id = $user_id_escaped AND status IN ('pending','confirmed')";
    $result_check = mysqli_query($conn, $sql_check);
    
    if ($result_check && mysqli_num_rows($result_check) > 0) {
        // Cập nhật trạng thái thành canceled
        $sql_update = "UPDATE appointments SET status = 'canceled' WHERE appointment_id = $appointment_id AND user_id = $user_id_escaped";
        $result_update = mysqli_query($conn, $sql_update);
        
        if ($result_update && mysqli_affected_rows($conn) > 0) {
            $success_message = 'Lịch hẹn đã được hủy thành công.';
        } else {
            $error_message = 'Đã xảy ra lỗi khi hủy lịch hẹn. Vui lòng thử lại.';
        }
    } else {
        $error_message = 'Không thể hủy lịch hẹn này. Có thể lịch đã được xử lý hoặc không thuộc về bạn.';
    }
}

// Lấy danh sách lịch hẹn của user
$appointments = [];
$sql = "SELECT a.*, f.name AS facility_name, s.specialty_name
        FROM appointments a
        JOIN facilities f ON a.facility_id = f.facility_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
        WHERE a.user_id = $user_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Phân loại lịch hẹn: sắp tới và đã hoàn thành/hủy
$upcoming = [];
$completed = [];

foreach ($appointments as $appointment) {
    $appointment_date = new DateTime($appointment['appointment_date']);
    $today = new DateTime();
    
    // Phân loại: completed/canceled hoặc đã qua ngày → completed, còn lại → upcoming
    if ($appointment['status'] == 'completed' || $appointment['status'] == 'canceled' || $appointment_date < $today) {
        $completed[] = $appointment;
    } else {
        $upcoming[] = $appointment;
    }
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
?>

<main class="page appointments-page">
    <section class="page-hero">
        <h1>Lịch hẹn của tôi</h1>
    </section>

    <section class="appointments-section">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <h2>Lịch hẹn sắp tới</h2>
        <div class="appointments-grid" id="upcoming-appointments">
            <?php if (empty($upcoming)): ?>
                <p>Bạn chưa có lịch hẹn sắp tới.</p>
            <?php else: ?>
                <?php foreach ($upcoming as $appointment): ?>
                    <article class="appointment-card reveal">
                        <div class="appointment-header">
                            <h3><?php echo htmlspecialchars($appointment['facility_name']); ?></h3>
                            <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                <?php echo formatStatus($appointment['status']); ?>
                            </span>
                        </div>
                        <div class="appointment-info">
                            <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($appointment['specialty_name']); ?></p>
                            <p><strong>Ngày khám:</strong> <?php echo formatDate($appointment['appointment_date']); ?></p>
                            <p><strong>Giờ khám:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                            <p style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;"><strong>Triệu chứng:</strong> <span style="white-space: pre-wrap;"><?php echo htmlspecialchars($appointment['symptoms']); ?></span></p>
                        </div>
                        <div class="appointment-actions">
                            <form method="post" action="MyAppointments.php">
                                <input type="hidden" name="appointment_id" value="<?php echo (int) $appointment['appointment_id']; ?>">
                                <button type="submit" name="cancel_appointment" class="btn-primary">Hủy lịch</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="appointments-section">
        <h2>Lịch hẹn đã hoàn thành / Đã hủy</h2>
        <div class="appointments-grid" id="completed-appointments">
            <?php if (empty($completed)): ?>
                <p>Bạn chưa có lịch hẹn đã hoàn thành hoặc đã hủy.</p>
            <?php else: ?>
                <?php foreach ($completed as $appointment): ?>
                    <article class="appointment-card reveal">
                        <div class="appointment-header">
                            <h3><?php echo htmlspecialchars($appointment['facility_name']); ?></h3>
                            <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                <?php echo formatStatus($appointment['status']); ?>
                            </span>
                        </div>
                        <div class="appointment-info">
                            <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($appointment['specialty_name']); ?></p>
                            <p><strong>Ngày khám:</strong> <?php echo formatDate($appointment['appointment_date']); ?></p>
                            <p><strong>Giờ khám:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                            <p style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;"><strong>Triệu chứng:</strong> <span style="white-space: pre-wrap;"><?php echo htmlspecialchars($appointment['symptoms']); ?></span></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

