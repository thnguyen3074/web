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

// Lấy facility_id từ URL hoặc POST (khi quay lại từ BookingConfirm)
$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : (isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0);

if ($facility_id <= 0) {
    echo "<p>ID cơ sở y tế không hợp lệ.</p>";
    include 'footer.php';
    exit();
}

// Lấy thông tin đã nhập từ POST (khi quay lại từ BookingConfirm)
$prev_specialty_id = isset($_POST['specialty_id']) ? intval($_POST['specialty_id']) : 0;
$prev_appointment_date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$prev_appointment_time = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
$prev_symptoms = isset($_POST['symptoms']) ? trim($_POST['symptoms']) : '';
$prev_fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$prev_email = isset($_POST['email']) ? trim($_POST['email']) : '';
$prev_phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

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
        <form id="booking-form" class="booking-form" action="BookingConfirm.php" method="POST" data-validate="true">
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
                            <option value="<?php echo $specialty['specialty_id']; ?>" <?php echo ($prev_specialty_id == $specialty['specialty_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($specialty['specialty_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="booking-date">Ngày khám</label>
                    <input type="date" id="booking-date" name="appointment_date" value="<?php echo htmlspecialchars($prev_appointment_date); ?>" required />
                </div>
                <div class="form-group">
                    <label for="booking-time">Giờ khám</label>
                    <select id="booking-time" name="appointment_time" required>
                        <option value="">Chọn giờ khám</option>
                        <?php
                        $time_slots = ['07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '14:00', '14:30', '15:00', '15:30', '16:00'];
                        foreach ($time_slots as $time):
                        ?>
                            <option value="<?php echo $time; ?>" <?php echo ($prev_appointment_time == $time) ? 'selected' : ''; ?>>
                                <?php echo $time; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="booking-section">
                <h3>Thông tin cá nhân</h3>
                <?php if ($isLoggedIn && $user): ?>
                    <!-- User đã đăng nhập - tự động điền thông tin từ database, cho phép chỉnh sửa -->
                    <div class="form-group">
                        <label for="booking-name">Họ và tên</label>
                        <input
                            type="text"
                            id="booking-name"
                            name="fullname"
                            value="<?php echo htmlspecialchars(!empty($prev_fullname) ? $prev_fullname : $user['fullname']); ?>"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-email">Email</label>
                        <input
                            type="email"
                            id="booking-email"
                            name="email"
                            value="<?php echo htmlspecialchars(!empty($prev_email) ? $prev_email : $user['email']); ?>"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="booking-phone">Số điện thoại</label>
                        <input
                            type="tel"
                            id="booking-phone"
                            name="phone"
                            value="<?php echo htmlspecialchars(!empty($prev_phone) ? $prev_phone : $user['phone']); ?>"
                            required
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
                            value="<?php echo htmlspecialchars($prev_fullname); ?>"
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
                            value="<?php echo htmlspecialchars($prev_email); ?>"
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
                            value="<?php echo htmlspecialchars($prev_phone); ?>"
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
                    ><?php echo htmlspecialchars($prev_symptoms); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary booking-submit">
                Xác nhận đặt lịch
            </button>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking-date');
    const timeSelect = document.getElementById('booking-time');
    
    // Cho phép chọn ngày hôm nay - sử dụng timezone local
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const minDate = `${year}-${month}-${day}`;
    dateInput.setAttribute('min', minDate);
    
    // Lưu lại đối tượng Date của hôm nay để so sánh
    const todayDate = new Date(year, today.getMonth(), today.getDate());

    // Hàm disable các giờ đã qua nếu chọn hôm nay
    function updateTimeSlots() {
        const selectedDate = dateInput.value;
        const now = new Date();
        const currentTime = now.toTimeString().slice(0, 5); // dạng HH:MM

        // Reset tất cả option về trạng thái enabled
        for (let option of timeSelect.options) {
            option.disabled = false;
        }

        // Nếu không chọn ngày → không kiểm tra
        if (!selectedDate) return;

        // Parse ngày đã chọn (format YYYY-MM-DD)
        const [yearSelected, monthSelected, daySelected] = selectedDate.split('-').map(Number);
        const chosenDate = new Date(yearSelected, monthSelected - 1, daySelected);

        // Nếu chọn đúng ngày hôm nay → disable giờ đã qua
        if (chosenDate.getTime() === todayDate.getTime()) {
            for (let option of timeSelect.options) {
                if (option.value && option.value < currentTime) {
                    option.disabled = true;
                }
            }
        }
    }

    // Khi thay đổi ngày → cập nhật giờ
    dateInput.addEventListener('change', updateTimeSlots);

    // Cập nhật ngay khi vào trang nếu ngày đã có sẵn
    updateTimeSlots();

    // Kiểm tra khi submit
    document.getElementById('booking-form').addEventListener('submit', function(e) {
        const selectedDate = dateInput.value;
        const selectedTime = timeSelect.value;

        if (!selectedDate || !selectedTime) return;

        const now = new Date();
        const selectedDateTime = new Date(selectedDate + 'T' + selectedTime);

        if (selectedDateTime <= now) {
            e.preventDefault();
            alert('Vui lòng chọn thời gian khám sau thời gian hiện tại.');
        }
    });
});

</script>

<?php include 'footer.php'; ?>
