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
            <p>Chưa có cơ sở y tế nào cho chuyên khoa này.</p>
        <?php else: ?>
            <?php foreach ($facilities as $facility): ?>
                <article class="facility-card reveal">
                    <?php if (!empty($facility['image'])): ?>
                        <img src="<?php echo htmlspecialchars($facility['image']); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                    <?php else: ?>
                        <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($facility['name']); ?></h3>
                        <p><?php echo htmlspecialchars($facility['address']); ?></p>
                        <p>Giờ làm việc: <?php echo htmlspecialchars($facility['working_hours']); ?></p>
                        <a href="FacilityDetail.php?id=<?php echo $facility['facility_id']; ?>" class="btn-secondary">Xem chi tiết</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

