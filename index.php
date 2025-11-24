<?php
// Trang chủ - Hiển thị danh sách bệnh viện, phòng khám và chuyên khoa

$pageTitle = 'Trang chủ';
require_once 'config.php';
include 'header.php';

$hospitals = [];
$sql_hospitals = "SELECT facility_id, name, address, working_hours, description, image FROM facilities WHERE type = 'hospital' ORDER BY facility_id";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

$clinics = [];
$sql_clinics = "SELECT facility_id, name, address, working_hours, image FROM facilities WHERE type = 'clinic' ORDER BY facility_id";
$result_clinics = mysqli_query($conn, $sql_clinics);
if ($result_clinics) {
    while ($row = mysqli_fetch_assoc($result_clinics)) {
        $clinics[] = $row;
    }
}

$specialties_home = [];
$sql_specialties = "SELECT * FROM specialties ORDER BY specialty_name";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $specialties_home[] = $row;
    }
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
                required
                autocomplete="off"
            />
            <button type="submit">Tìm kiếm</button>
            <div id="search-suggestions"></div>
        </form>
    </div>
</section>

<main>
    <section class="section hospitals">
        <div class="section-header">
            <div>
                <h2>Danh sách bệnh viện</h2>
                <p>Khám phá các bệnh viện uy tín trên toàn quốc.</p>
            </div>
            <a href="Facility.php?tab=hospital" class="see-more">Xem thêm &gt;</a>
        </div>
        <div class="slider-container">
            <button class="slider-btn slider-btn-left" aria-label="Cuộn trái">
                <span>&lt;</span>
            </button>
            <div class="slider-wrapper">
                <div class="slider-track">
                    <?php if (empty($hospitals)): ?>
                        <p>Chưa có bệnh viện nào trong hệ thống.</p>
                    <?php else: ?>
                        <?php foreach ($hospitals as $hospital): ?>
                            <a href="FacilityDetail.php?id=<?php echo $hospital['facility_id']; ?>" class="slider-card reveal" style="text-decoration: none; color: inherit;">
                                <div class="card-image-wrapper">
                                    <?php if (!empty($hospital['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($hospital['image']); ?>" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                                    <?php else: ?>
                                        <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h3><?php echo htmlspecialchars($hospital['name']); ?></h3>
                                    <div class="card-info">
                                        <div class="info-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?php echo htmlspecialchars($hospital['address']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span><?php echo htmlspecialchars($hospital['working_hours']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="slider-btn slider-btn-right" aria-label="Cuộn phải">
                <span>&gt;</span>
            </button>
        </div>
    </section>

    <section class="section clinics">
        <div class="section-header">
            <div>
                <h2>Danh sách phòng khám</h2>
                <p>Lựa chọn phòng khám phù hợp với nhu cầu của bạn.</p>
            </div>
            <a href="Facility.php?tab=clinic" class="see-more">Xem thêm &gt;</a>
        </div>
        <div class="slider-container">
            <button class="slider-btn slider-btn-left" aria-label="Cuộn trái">
                <span>&lt;</span>
            </button>
            <div class="slider-wrapper">
                <div class="slider-track">
                    <?php if (empty($clinics)): ?>
                        <p>Chưa có phòng khám nào trong hệ thống.</p>
                    <?php else: ?>
                        <?php foreach ($clinics as $clinic): ?>
                            <a href="FacilityDetail.php?id=<?php echo $clinic['facility_id']; ?>" class="slider-card reveal" style="text-decoration: none; color: inherit;">
                                <div class="card-image-wrapper">
                                    <?php if (!empty($clinic['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($clinic['image']); ?>" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                                    <?php else: ?>
                                        <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h3><?php echo htmlspecialchars($clinic['name']); ?></h3>
                                    <div class="card-info">
                                        <div class="info-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?php echo htmlspecialchars($clinic['address']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span><?php echo htmlspecialchars($clinic['working_hours']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="slider-btn slider-btn-right" aria-label="Cuộn phải">
                <span>&gt;</span>
            </button>
        </div>
    </section>

    <section class="section specialties">
        <div class="section-header">
            <div>
                <h2>Danh sách chuyên khoa</h2>
                <p>Chọn chuyên khoa để đặt lịch khám nhanh chóng.</p>
            </div>
            <a href="Specialty.php" class="see-more">Xem thêm &gt;</a>
        </div>
        <div class="slider-container">
            <button class="slider-btn slider-btn-left" aria-label="Cuộn trái">
                <span>&lt;</span>
            </button>
            <div class="slider-wrapper">
                <div class="slider-track">
                    <?php if (empty($specialties_home)): ?>
                        <p>Chưa có chuyên khoa nào trong hệ thống.</p>
                    <?php else: ?>
                        <?php foreach ($specialties_home as $specialty): ?>
                            <a href="SpecialtyResult.php?id=<?php echo $specialty['specialty_id']; ?>" class="slider-card slider-card-specialty reveal" style="text-decoration: none; color: inherit;">
                                <?php if (!empty($specialty['icon'])): ?>
                                    <span class="specialty-icon"><img src="<?php echo htmlspecialchars($specialty['icon']); ?>" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" /></span>
                                <?php else: ?>
                                    <span class="specialty-icon"><img src="images/specialties/default.png" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" /></span>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($specialty['specialty_name']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="slider-btn slider-btn-right" aria-label="Cuộn phải">
                <span>&gt;</span>
            </button>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

