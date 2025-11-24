<?php
// Chi tiết cơ sở y tế - Hiển thị thông tin chi tiết của một cơ sở y tế

$pageTitle = 'Chi tiết cơ sở y tế';
require_once 'config.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<p>ID không hợp lệ.</p>";
    include 'footer.php';
    exit();
}

$sql = "SELECT * FROM facilities WHERE facility_id = $id";
$result = mysqli_query($conn, $sql);
$facility = mysqli_fetch_assoc($result);

if (!$facility) {
    echo "<p>Không tìm thấy cơ sở y tế.</p>";
    include 'footer.php';
    exit();
}

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

$type_text = ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám';
?>

<main class="facility-detail-page">
    <div class="facility-detail-container">
        <!-- Banner section với ảnh nhỏ bên trái -->
        <section class="facility-banner-card">
            <div class="banner-image">
                <?php if (!empty($facility['image'])): ?>
                    <img src="<?php echo htmlspecialchars($facility['image']); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                <?php else: ?>
                    <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                <?php endif; ?>
            </div>
            <div class="banner-info">
                <h1><?php echo htmlspecialchars($facility['name']); ?></h1>
                <p class="banner-address">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <?php echo htmlspecialchars($facility['address']); ?>
                </p>
                <a href="Booking.php?facility_id=<?php echo $facility['facility_id']; ?>" class="btn-primary banner-booking-btn">Đặt lịch khám</a>
            </div>
        </section>

        <!-- Phần thông tin chi tiết ở dưới -->
        <section class="facility-info-card">
            <div class="facility-title">
                <h2>Thông tin chi tiết</h2>
                <span class="type-badge"><?php echo htmlspecialchars($type_text); ?></span>
            </div>
            
            <div class="facility-meta">
                <div class="meta-item">
                    <h3>Địa chỉ</h3>
                    <p><?php echo htmlspecialchars($facility['address']); ?></p>
                </div>
                <div class="meta-item">
                    <h3>Điện thoại</h3>
                    <p><?php echo htmlspecialchars($facility['phone']); ?></p>
                </div>
                <div class="meta-item">
                    <h3>Thời gian làm việc</h3>
                    <p><?php echo htmlspecialchars($facility['working_hours']); ?></p>
                </div>
                <div class="meta-item">
                    <h3>Chuyên khoa</h3>
                    <?php if (empty($specialties)): ?>
                        <p>Chưa có thông tin chuyên khoa.</p>
                    <?php else: ?>
                        <ul class="specialty-list">
                            <?php foreach ($specialties as $specialty): ?>
                                <li><?php echo htmlspecialchars($specialty); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Phần mô tả -->
        <section class="facility-description-card">
            <h2>Giới thiệu</h2>
            <?php if (!empty($facility['description'])): ?>
                <p><?php echo nl2br(htmlspecialchars($facility['description'])); ?></p>
            <?php else: ?>
                <p>Chưa có thông tin mô tả cho cơ sở y tế này.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

