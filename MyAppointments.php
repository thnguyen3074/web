<?php
/**
 * Lịch hẹn của tôi - Medicare
 * Hiển thị tất cả lịch hẹn của user đã đăng nhập
 */

$pageTitle = 'Lịch hẹn của tôi';
require_once 'config.php';
include 'header.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Xử lý hủy lịch hẹn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'], $_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = 'canceled' WHERE appointment_id = ? AND user_id = ? AND status IN ('pending','confirmed')");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $appointment_id, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $success_message = 'Lịch hẹn đã được hủy thành công.';
            } else {
                $error_message = 'Không thể hủy lịch hẹn này. Có thể lịch đã được xử lý.';
            }
        } else {
            $error_message = 'Đã xảy ra lỗi khi hủy lịch hẹn. Vui lòng thử lại.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = 'Không thể chuẩn bị yêu cầu hủy lịch.';
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

// Phân loại lịch hẹn
$upcoming = [];
$completed = [];

foreach ($appointments as $appointment) {
    $appointment_date = new DateTime($appointment['appointment_date']);
    $today = new DateTime();
    
    if ($appointment['status'] == 'completed' || $appointment['status'] == 'canceled' || $appointment_date < $today) {
        $completed[] = $appointment;
    } else {
        $upcoming[] = $appointment;
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
                            <p><strong>Triệu chứng:</strong> <?php echo htmlspecialchars($appointment['symptoms']); ?></p>
                        </div>
                        <div class="appointment-actions">
                            <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?');">
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
                            <p><strong>Triệu chứng:</strong> <?php echo htmlspecialchars($appointment['symptoms']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

