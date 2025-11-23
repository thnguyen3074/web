<?php
/**
 * Kết quả tìm kiếm theo chuyên khoa - Medicare
 * Hiển thị danh sách cơ sở y tế theo chuyên khoa từ MySQL
 */

$pageTitle = 'Cơ sở theo chuyên khoa';
require_once 'config.php';
include 'header.php';

// Lấy specialty_id từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<p>ID chuyên khoa không hợp lệ.</p>";
    include 'footer.php';
    exit();
}

// Lấy tên chuyên khoa
$sql_specialty = "SELECT specialty_name FROM specialties WHERE specialty_id = $id";
$result_specialty = mysqli_query($conn, $sql_specialty);
$specialty = mysqli_fetch_assoc($result_specialty);

if (!$specialty) {
    echo "<p>Không tìm thấy chuyên khoa.</p>";
    include 'footer.php';
    exit();
}

$specialty_name = $specialty['specialty_name'];

// Lấy danh sách cơ sở y tế theo chuyên khoa
$facilities = [];
$sql = "SELECT f.*
        FROM facilities f
        JOIN facility_specialty fs ON f.facility_id = fs.facility_id
        WHERE fs.specialty_id = $id
        ORDER BY f.facility_id";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $facilities[] = $row;
    }
}

// Chuyển đổi type thành tiếng Việt
function getTypeText($type) {
    return ($type == 'hospital') ? 'Bệnh viện' : 'Phòng khám';
}
?>

<main class="page specialty-result-page">
    <section class="page-hero">
        <h1>Kết quả chuyên khoa: <?php echo htmlspecialchars($specialty_name); ?></h1>
        <p>
            Danh sách cơ sở y tế có dịch vụ khám chữa bệnh chuyên khoa <?php echo htmlspecialchars($specialty_name); ?>.
        </p>
    </section>

    <section class="facility-grid">
        <?php if (empty($facilities)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <h3>Chưa có cơ sở y tế nào</h3>
                <p>Hiện tại chưa có cơ sở y tế nào cung cấp dịch vụ chuyên khoa này.</p>
            </div>
        <?php else: ?>
            <?php foreach ($facilities as $facility): ?>
                <a href="FacilityDetail.php?id=<?php echo $facility['facility_id']; ?>" class="facility-card reveal" style="text-decoration: none; color: inherit;">
                    <div class="card-image-wrapper">
                        <?php if (!empty($facility['image'])): ?>
                            <img src="<?php echo htmlspecialchars($facility['image']); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                        <?php else: ?>
                            <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($facility['name']); ?></h3>
                        <div class="card-info">
                            <div class="info-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span><?php echo htmlspecialchars($facility['address']); ?></span>
                            </div>
                            <?php if (!empty($facility['working_hours'])): ?>
                                <div class="info-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span><?php echo htmlspecialchars($facility['working_hours']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

