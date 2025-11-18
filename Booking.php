<?php
/**
 * Đặt lịch khám - Medicare
 * Form đặt lịch khám bệnh
 */

$pageTitle = 'Đặt lịch khám';
require_once 'config.php';
include 'header.php';

// Kiểm tra user đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

// Nếu đã đăng nhập, lấy thông tin user
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT fullname, email, phone FROM users WHERE user_id = $user_id";
    $result_user = mysqli_query($conn, $sql_user);
    $user = mysqli_fetch_assoc($result_user);
}

// Lấy facility_id từ URL
$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;

if ($facility_id <= 0) {
    echo "<p>ID cơ sở y tế không hợp lệ.</p>";
    include 'footer.php';
    exit();
}

// Lấy thông tin cơ sở y tế
$sql_facility = "SELECT * FROM facilities WHERE facility_id = $facility_id";
$result_facility = mysqli_query($conn, $sql_facility);
$facility = mysqli_fetch_assoc($result_facility);

if (!$facility) {
    echo "<p>Không tìm thấy cơ sở y tế.</p>";
    include 'footer.php';
    exit();
}

// Lấy danh sách chuyên khoa của cơ sở
$specialties = [];
$sql_specialties = "SELECT s.*
                    FROM specialties s
                    JOIN facility_specialty fs ON s.specialty_id = fs.specialty_id
                    WHERE fs.facility_id = $facility_id
                    ORDER BY s.specialty_name";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $specialties[] = $row;
    }
}
?>

<main class="booking-page">
    <section class="page-hero">
        <h1>Đặt lịch khám</h1>
        <p>Vui lòng chọn thông tin lịch khám và điền thông tin cá nhân của bạn.</p>
    </section>

    <section class="booking-card">
        <form class="booking-form" action="BookingConfirm.php" method="POST" data-validate="true">
            <input type="hidden" name="facility_id" value="<?php echo $facility_id; ?>" />
            
            <div class="booking-section">
                <h3>Thông tin cơ sở y tế</h3>
                <div class="facility-summary">
                    <h4><?php echo htmlspecialchars($facility['name']); ?></h4>
                    <p><?php echo htmlspecialchars($facility['address']); ?></p>
                    <p>Hotline: <?php echo htmlspecialchars($facility['phone']); ?></p>
                </div>
            </div>

            <div class="booking-section">
                <h3>Chọn chuyên khoa & thời gian</h3>
                <div class="form-group">
                    <label for="booking-specialty">Chuyên khoa</label>
                    <select id="booking-specialty" name="specialty_id" required>
                        <option value="">Chọn chuyên khoa</option>
                        <?php foreach ($specialties as $specialty): ?>
                            <option value="<?php echo $specialty['specialty_id']; ?>">
                                <?php echo htmlspecialchars($specialty['specialty_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="booking-date">Ngày khám</label>
                    <input type="date" id="booking-date" name="appointment_date" required />
                </div>
                <div class="form-group">
                    <label for="booking-time">Giờ khám</label>
                    <select id="booking-time" name="appointment_time" required>
                        <option value="">Chọn giờ khám</option>
                        <option value="07:00">07:00</option>
                        <option value="07:30">07:30</option>
                        <option value="08:00">08:00</option>
                        <option value="08:30">08:30</option>
                        <option value="09:00">09:00</option>
                        <option value="09:30">09:30</option>
                        <option value="10:00">10:00</option>
                        <option value="10:30">10:30</option>
                        <option value="11:00">11:00</option>
                        <option value="14:00">14:00</option>
                        <option value="14:30">14:30</option>
                        <option value="15:00">15:00</option>
                        <option value="15:30">15:30</option>
                        <option value="16:00">16:00</option>
                    </select>
                </div>
            </div>

            <div class="booking-section">
                <h3>Thông tin cá nhân</h3>
                <?php if ($isLoggedIn && $user): ?>
                    <!-- User đã đăng nhập - tự động điền thông tin -->
                    <div class="form-group">
                        <label for="booking-name">Họ và tên</label>
                        <input
                            type="text"
                            id="booking-name"
                            name="fullname"
                            value="<?php echo htmlspecialchars($user['fullname']); ?>"
                            readonly
                            style="background: #f5f5f5;"
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-email">Email</label>
                        <input
                            type="email"
                            id="booking-email"
                            name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            readonly
                            style="background: #f5f5f5;"
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-phone">Số điện thoại</label>
                        <input
                            type="tel"
                            id="booking-phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($user['phone']); ?>"
                            readonly
                            style="background: #f5f5f5;"
                        />
                    </div>
                <?php else: ?>
                    <!-- User chưa đăng nhập - yêu cầu nhập thông tin -->
                    <div class="form-group">
                        <label for="booking-name">Họ và tên</label>
                        <input
                            type="text"
                            id="booking-name"
                            name="fullname"
                            placeholder="Nhập họ và tên"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-email">Email</label>
                        <input
                            type="email"
                            id="booking-email"
                            name="email"
                            placeholder="Nhập email"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-phone">Số điện thoại</label>
                        <input
                            type="tel"
                            id="booking-phone"
                            name="phone"
                            placeholder="Nhập số điện thoại"
                            required
                        />
                    </div>
                <?php endif; ?>
            </div>

            <div class="booking-section">
                <h3>Thông tin triệu chứng</h3>
                <div class="form-group">
                    <label for="booking-symptom">Triệu chứng</label>
                    <textarea
                        id="booking-symptom"
                        name="symptoms"
                        rows="4"
                        placeholder="Mô tả triệu chứng của bạn"
                        required
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary booking-submit">
                Xác nhận đặt lịch
            </button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>
