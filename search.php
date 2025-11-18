<?php
/**
 * Trang tìm kiếm - Medicare
 * Tìm kiếm bệnh viện, phòng khám và chuyên khoa
 */

$pageTitle = 'Tìm kiếm';
require_once 'config.php';
include 'header.php';

// Lấy từ khóa tìm kiếm từ GET
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Nếu không có từ khóa, redirect về trang chủ
if (empty($keyword)) {
    header('Location: index.php');
    exit();
}

// Escape từ khóa để bảo mật
$keyword = mysqli_real_escape_string($conn, $keyword);
$search_term = '%' . $keyword . '%';

// Tìm kiếm trong bảng facilities (bệnh viện và phòng khám)
$facilities = [];
$sql_facilities = "SELECT * FROM facilities WHERE name LIKE '$search_term' ORDER BY type, name";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    while ($row = mysqli_fetch_assoc($result_facilities)) {
        $facilities[] = $row;
    }
}

// Tìm kiếm trong bảng specialties (chuyên khoa)
$specialties = [];
$sql_specialties = "SELECT * FROM specialties WHERE specialty_name LIKE '$search_term' ORDER BY specialty_name";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $specialties[] = $row;
    }
}

// Hàm chuyển đổi type thành tiếng Việt
function getTypeText($type) {
    return ($type == 'hospital') ? 'Bệnh viện' : 'Phòng khám';
}
?>

<main class="page search-page">
    <section class="page-hero">
        <h1>Kết quả tìm kiếm</h1>
        <p>Từ khóa: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong></p>
    </section>

    <?php if (empty($facilities) && empty($specialties)): ?>
        <section class="search-results">
            <div class="no-results">
                <p>Không tìm thấy kết quả phù hợp với từ khóa "<strong><?php echo htmlspecialchars($keyword); ?></strong>".</p>
                <p>Vui lòng thử lại với từ khóa khác.</p>
                <a href="index.php" class="btn-primary">Về trang chủ</a>
            </div>
        </section>
    <?php else: ?>
        <!-- Kết quả tìm thấy cơ sở y tế -->
        <?php if (!empty($facilities)): ?>
            <section class="search-results">
                <h2>Cơ sở y tế (<?php echo count($facilities); ?> kết quả)</h2>
                <div class="facility-grid">
                    <?php foreach ($facilities as $facility): ?>
                        <article class="facility-card reveal">
                            <?php if (!empty($facility['image'])): ?>
                                <img src="<?php echo htmlspecialchars($facility['image']); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                            <?php else: ?>
                                <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($facility['name']); ?>" />
                            <?php endif; ?>
                            <div class="card-body">
                                <h3><?php echo htmlspecialchars($facility['name']); ?></h3>
                                <p><strong>Loại:</strong> <?php echo getTypeText($facility['type']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($facility['address']); ?></p>
                                <?php if (!empty($facility['working_hours'])): ?>
                                    <p><strong>Giờ làm việc:</strong> <?php echo htmlspecialchars($facility['working_hours']); ?></p>
                                <?php endif; ?>
                                <a href="FacilityDetail.php?id=<?php echo $facility['facility_id']; ?>" class="btn-secondary">Xem chi tiết</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Kết quả tìm thấy chuyên khoa -->
        <?php if (!empty($specialties)): ?>
            <section class="search-results">
                <h2>Chuyên khoa (<?php echo count($specialties); ?> kết quả)</h2>
                <div class="specialty-grid-large">
                    <?php foreach ($specialties as $specialty): ?>
                        <a class="specialty-card reveal" href="SpecialtyResult.php?id=<?php echo $specialty['specialty_id']; ?>">
                            <?php if (!empty($specialty['icon'])): ?>
                                <img src="<?php echo htmlspecialchars($specialty['icon']); ?>" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" />
                            <?php else: ?>
                                <img src="images/specialties/default.png" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" />
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($specialty['specialty_name']); ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

