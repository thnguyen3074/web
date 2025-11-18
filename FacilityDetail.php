<?php
/**
 * Chi tiết cơ sở y tế - Medicare
 * Hiển thị thông tin chi tiết của một cơ sở y tế từ MySQL
 */

$pageTitle = 'Chi tiết cơ sở y tế';
require_once 'config.php';
include 'header.php';

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<p>ID không hợp lệ.</p>";
    include 'footer.php';
    exit();
}

// Lấy thông tin cơ sở y tế
$sql = "SELECT * FROM facilities WHERE facility_id = $id";
$result = mysqli_query($conn, $sql);
$facility = mysqli_fetch_assoc($result);

if (!$facility) {
    echo "<p>Không tìm thấy cơ sở y tế.</p>";
    include 'footer.php';
    exit();
}

// Lấy danh sách chuyên khoa của cơ sở
$specialties = [];
$sql_specialties = "SELECT s.specialty_name 
                   FROM specialties s 
                   JOIN facility_specialty fs ON fs.specialty_id = s.specialty_id 
                   WHERE fs.facility_id = $id";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $specialties[] = $row['specialty_name'];
    }
}

// Chuyển đổi type thành tiếng Việt
$type_text = ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám';
?>

<main class="facility-detail-page">
    <section class="facility-image">
        <?php if (!empty($facility['image'])): ?>
            <img src="<?php echo htmlspecialchars($facility['image']); ?>" alt="Banner <?php echo htmlspecialchars($facility['name']); ?>" />
        <?php else: ?>
            <img src="images/facilities/default.jpg" alt="Banner <?php echo htmlspecialchars($facility['name']); ?>" />
        <?php endif; ?>
    </section>

    <section class="facility-info">
        <div class="facility-title">
            <h1><?php echo htmlspecialchars($facility['name']); ?></h1>
            <p>
                Địa chỉ: <?php echo htmlspecialchars($facility['address']); ?> | Hotline: <?php echo htmlspecialchars($facility['phone']); ?>
            </p>
        </div>
        <div class="facility-meta">
            <div>
                <h3>Loại</h3>
                <p><?php echo htmlspecialchars($type_text); ?></p>
            </div>
            <div>
                <h3>Địa chỉ</h3>
                <p><?php echo htmlspecialchars($facility['address']); ?></p>
            </div>
            <div>
                <h3>Điện thoại</h3>
                <p><?php echo htmlspecialchars($facility['phone']); ?></p>
            </div>
            <div>
                <h3>Thời gian làm việc</h3>
                <p><?php echo htmlspecialchars($facility['working_hours']); ?></p>
            </div>
            <div>
                <h3>Chuyên khoa</h3>
                <?php if (empty($specialties)): ?>
                    <p>Chưa có thông tin chuyên khoa.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($specialties as $specialty): ?>
                            <li><?php echo htmlspecialchars($specialty); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <a href="Booking.php?facility_id=<?php echo $facility['facility_id']; ?>" class="btn-primary detail-booking-btn">Đặt lịch khám</a>
    </section>

    <section class="facility-description">
        <h2>Giới thiệu</h2>
        <?php if (!empty($facility['description'])): ?>
            <p><?php echo nl2br(htmlspecialchars($facility['description'])); ?></p>
        <?php else: ?>
            <p>Chưa có thông tin mô tả cho cơ sở y tế này.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

