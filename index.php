<?php
/**
 * Trang chủ - Medicare
 * Hiển thị danh sách bệnh viện, phòng khám và chuyên khoa từ MySQL
 */

$pageTitle = 'Trang chủ';
require_once 'config.php';
include 'header.php';

// Lấy danh sách bệnh viện từ database
$hospitals = [];
$sql_hospitals = "SELECT facility_id, name, address, working_hours, description, image FROM facilities WHERE type = 'hospital' ORDER BY facility_id LIMIT 4";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

// Lấy danh sách phòng khám từ database
$clinics = [];
$sql_clinics = "SELECT facility_id, name, address, working_hours, image FROM facilities WHERE type = 'clinic' ORDER BY facility_id LIMIT 4";
$result_clinics = mysqli_query($conn, $sql_clinics);
if ($result_clinics) {
    while ($row = mysqli_fetch_assoc($result_clinics)) {
        $clinics[] = $row;
    }
}

// Lấy danh sách chuyên khoa từ database để hiển thị trên trang chủ
$specialties_home = [];
$sql_specialties = "SELECT * FROM specialties ORDER BY specialty_name LIMIT 8";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $specialties_home[] = $row;
    }
}
?>

<section class="search-bar">
    <div class="search-wrapper">
        <form action="search.php" method="GET" id="search-form" style="display: flex; width: 100%; gap: 10px; position: relative;">
            <input
                type="text"
                name="keyword"
                id="search-input"
                placeholder="Tìm kiếm bệnh viện, phòng khám, chuyên khoa…"
                required
                autocomplete="off"
                style="flex: 1; padding: 12px 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
            />
            <button type="submit" style="padding: 12px 24px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 500;">Tìm kiếm</button>
            <div id="search-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; margin-top: 5px; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
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
        <div class="cards hospital-cards">
            <?php if (empty($hospitals)): ?>
                <p>Chưa có bệnh viện nào trong hệ thống.</p>
            <?php else: ?>
                <?php foreach ($hospitals as $hospital): ?>
                    <article class="card reveal">
                        <a href="FacilityDetail.php?id=<?php echo $hospital['facility_id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php if (!empty($hospital['image'])): ?>
                                <img src="<?php echo htmlspecialchars($hospital['image']); ?>" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                            <?php else: ?>
                                <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($hospital['name']); ?></h3>
                            <p>Địa chỉ: <?php echo htmlspecialchars($hospital['address']); ?></p>
                            <p>Giờ làm việc: <?php echo htmlspecialchars($hospital['working_hours']); ?></p>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
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
        <div class="cards clinic-cards">
            <?php if (empty($clinics)): ?>
                <p>Chưa có phòng khám nào trong hệ thống.</p>
            <?php else: ?>
                <?php foreach ($clinics as $clinic): ?>
                    <article class="card reveal">
                        <a href="FacilityDetail.php?id=<?php echo $clinic['facility_id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php if (!empty($clinic['image'])): ?>
                                <img src="<?php echo htmlspecialchars($clinic['image']); ?>" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                            <?php else: ?>
                                <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($clinic['name']); ?></h3>
                            <p>Địa chỉ: <?php echo htmlspecialchars($clinic['address']); ?></p>
                            <p>Giờ làm việc: <?php echo htmlspecialchars($clinic['working_hours']); ?></p>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
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
        <div class="specialty-grid">
            <?php if (empty($specialties_home)): ?>
                <p>Chưa có chuyên khoa nào trong hệ thống.</p>
            <?php else: ?>
                <?php foreach ($specialties_home as $specialty): ?>
                    <a href="SpecialtyResult.php?id=<?php echo $specialty['specialty_id']; ?>" class="specialty reveal" style="text-decoration: none; color: inherit;">
                        <?php if (!empty($specialty['icon'])): ?>
                            <span><img src="<?php echo htmlspecialchars($specialty['icon']); ?>" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" style="width: 40px; height: 40px;" /></span>
                        <?php else: ?>
                            <span><img src="images/specialties/default.png" alt="<?php echo htmlspecialchars($specialty['specialty_name']); ?>" style="width: 40px; height: 40px;" /></span>
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($specialty['specialty_name']); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

