<?php
// Trang tìm kiếm - Tìm kiếm bệnh viện, phòng khám và chuyên khoa

$pageTitle = 'Tìm kiếm';
require_once 'config.php';
include 'header.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword)) {
    header('Location: index.php');
    exit();
}

// Escape keyword để tránh SQL injection
$keyword = mysqli_real_escape_string($conn, $keyword);
$search_term = '%' . $keyword . '%'; // Thêm wildcard cho LIKE query

// Tìm kiếm trong bảng facilities (tên và địa chỉ)
$facilities = [];
$sql_facilities = "SELECT * FROM facilities WHERE name LIKE '$search_term' OR address LIKE '$search_term' ORDER BY type, name";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    while ($row = mysqli_fetch_assoc($result_facilities)) {
        $facilities[] = $row;
    }
}

// Tìm kiếm trong bảng specialties (tên chuyên khoa)
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

<section class="search-bar">
    <div class="search-wrapper">
        <form action="search.php" method="GET" id="search-form">
            <input
                type="text"
                name="keyword"
                id="search-input"
                placeholder="Tìm kiếm bệnh viện, phòng khám, chuyên khoa, địa chỉ…"
                value="<?php echo htmlspecialchars($keyword); ?>"
                required
                autocomplete="off"
            />
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>
</section>

<main class="page search-page">
    <section class="page-hero">
        <h1>Kết quả tìm kiếm</h1>
        <p>Từ khóa: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong></p>
    </section>

    <?php if (empty($facilities) && empty($specialties)): ?>
        <section class="search-results">
            <div class="no-results">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <h3>Không tìm thấy kết quả</h3>
                <p>Không tìm thấy kết quả phù hợp với từ khóa "<strong><?php echo htmlspecialchars($keyword); ?></strong>".</p>
                <p>Vui lòng thử lại với từ khóa khác.</p>
                <a href="index.php" class="btn-primary">Về trang chủ</a>
            </div>
        </section>
    <?php else: ?>
        <!-- Kết quả tìm thấy cơ sở y tế -->
        <?php if (!empty($facilities)): ?>
            <section class="search-results">
                <h2 class="results-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    Cơ sở y tế <span class="results-count">(<?php echo count($facilities); ?> kết quả)</span>
                </h2>
                <div class="facility-grid">
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
                </div>
            </section>
        <?php endif; ?>

        <!-- Kết quả tìm thấy chuyên khoa -->
        <?php if (!empty($specialties)): ?>
            <section class="search-results">
                <h2 class="results-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    Chuyên khoa <span class="results-count">(<?php echo count($specialties); ?> kết quả)</span>
                </h2>
                <div class="specialty-grid-large">
                    <?php foreach ($specialties as $specialty): ?>
                        <a class="specialty-card reveal" href="SpecialtyResult.php?id=<?php echo $specialty['specialty_id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="specialty-icon-wrapper">
                                <?php if (!empty($specialty['icon'])): ?>
                                    <img src="<?php echo htmlspecialchars($specialty['icon']); ?>" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" />
                                <?php else: ?>
                                    <img src="images/specialties/default.png" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" />
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($specialty['specialty_name']); ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

