<?php
/**
 * Xác nhận đặt lịch - Medicare
 * Hiển thị lại thông tin để xác nhận trước khi lưu
 */

$pageTitle = 'Xác nhận đặt lịch';
require_once 'config.php';
include 'header.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Facility.php');
    exit();
}

// Kiểm tra user đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);

// Lấy dữ liệu từ form
$facility_id = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;
$specialty_id = isset($_POST['specialty_id']) ? intval($_POST['specialty_id']) : 0;
$appointment_date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$appointment_time = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
$symptoms = isset($_POST['symptoms']) ? trim($_POST['symptoms']) : '';

// Lấy thông tin cá nhân (nếu user chưa đăng nhập)
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Validate input
if ($facility_id <= 0 || $specialty_id <= 0 || empty($appointment_date) || empty($appointment_time) || empty($symptoms)) {
    header('Location: Facility.php');
    exit();
}

// Nếu chưa đăng nhập, phải có đầy đủ thông tin cá nhân
if (!$isLoggedIn && (empty($fullname) || empty($email) || empty($phone))) {
    header('Location: Booking.php?facility_id=' . $facility_id);
    exit();
}

// Lấy thông tin cơ sở y tế
$sql_facility = "SELECT name FROM facilities WHERE facility_id = $facility_id";
$result_facility = mysqli_query($conn, $sql_facility);
$facility = mysqli_fetch_assoc($result_facility);

if (!$facility) {
    header('Location: Facility.php');
    exit();
}

// Lấy thông tin chuyên khoa
$sql_specialty = "SELECT specialty_name FROM specialties WHERE specialty_id = $specialty_id";
$result_specialty = mysqli_query($conn, $sql_specialty);
$specialty = mysqli_fetch_assoc($result_specialty);

if (!$specialty) {
    header('Location: Facility.php');
    exit();
}

// Format ngày để hiển thị
$date_obj = new DateTime($appointment_date);
$formatted_date = $date_obj->format('d/m/Y');
?>

<main class="page booking-confirm-page">
    <section class="page-hero">
        <h1>Xác nhận thông tin đặt lịch</h1>
        <p>Vui lòng kiểm tra lại thông tin trước khi xác nhận.</p>
    </section>

    <section class="confirm-card">
        <div class="confirm-section">
            <h3>Thông tin cơ sở y tế</h3>
            <div class="confirm-info">
                <p><strong>Cơ sở y tế:</strong> <?php echo htmlspecialchars($facility['name']); ?></p>
                <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($specialty['specialty_name']); ?></p>
            </div>
        </div>

        <div class="confirm-section">
            <h3>Thời gian khám</h3>
            <div class="confirm-info">
                <p><strong>Ngày khám:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
                <p><strong>Giờ khám:</strong> <?php echo htmlspecialchars($appointment_time); ?></p>
            </div>
        </div>

        <div class="confirm-section">
            <h3>Thông tin cá nhân</h3>
            <div class="confirm-info">
                <?php if ($isLoggedIn): ?>
                    <?php
                    // Lấy thông tin user từ session
                    $user_id = $_SESSION['user_id'];
                    $sql_user = "SELECT fullname, email, phone FROM users WHERE user_id = $user_id";
                    $result_user = mysqli_query($conn, $sql_user);
                    $user_info = mysqli_fetch_assoc($result_user);
                    ?>
                    <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($user_info['fullname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user_info['phone']); ?></p>
                <?php else: ?>
                    <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($fullname); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($phone); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="confirm-section">
            <h3>Thông tin triệu chứng</h3>
            <div class="confirm-info">
                <p><strong>Triệu chứng:</strong> <?php echo nl2br(htmlspecialchars($symptoms)); ?></p>
            </div>
        </div>

        <div class="confirm-actions">
            <a href="Booking.php?facility_id=<?php echo $facility_id; ?>" class="btn-secondary">Quay lại chỉnh sửa</a>
            <form action="booking_process.php" method="POST" style="display: inline;">
                <input type="hidden" name="facility_id" value="<?php echo $facility_id; ?>" />
                <input type="hidden" name="specialty_id" value="<?php echo $specialty_id; ?>" />
                <input type="hidden" name="appointment_date" value="<?php echo htmlspecialchars($appointment_date); ?>" />
                <input type="hidden" name="appointment_time" value="<?php echo htmlspecialchars($appointment_time); ?>" />
                <input type="hidden" name="symptoms" value="<?php echo htmlspecialchars($symptoms); ?>" />
                <?php if (!$isLoggedIn): ?>
                    <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" />
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>" />
                <?php endif; ?>
                <button type="submit" class="btn-primary">Xác nhận đặt lịch</button>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

