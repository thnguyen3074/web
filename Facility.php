<?php
// Danh sách cơ sở y tế - Hiển thị danh sách bệnh viện và phòng khám

$pageTitle = 'Cơ sở y tế';
require_once 'config.php';
include 'header.php';

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'hospital';
if ($active_tab != 'hospital' && $active_tab != 'clinic') {
    $active_tab = 'hospital';
}

$hospitals = [];
$sql_hospitals = "SELECT * FROM facilities WHERE type='hospital' ORDER BY facility_id";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

$clinics = [];
$sql_clinics = "SELECT * FROM facilities WHERE type='clinic' ORDER BY facility_id";
$result_clinics = mysqli_query($conn, $sql_clinics);
if ($result_clinics) {
    while ($row = mysqli_fetch_assoc($result_clinics)) {
        $clinics[] = $row;
    }
}
?>

<main class="facility-page">
    <section class="page-hero">
        <h1>Cơ sở y tế</h1>
        <p>Tìm kiếm và lựa chọn bệnh viện hoặc phòng khám phù hợp.</p>
    </section>

    <section class="facility-tabs">
        <button class="tab-button <?php echo ($active_tab == 'hospital') ? 'active' : ''; ?>" data-tab-target="hospital">
            Bệnh viện
        </button>
        <button class="tab-button <?php echo ($active_tab == 'clinic') ? 'active' : ''; ?>" data-tab-target="clinic">
            Phòng khám
        </button>
    </section>

    <section
        class="tab-panel facility-grid <?php echo ($active_tab == 'hospital') ? 'active default-show' : ''; ?>"
        data-tab-panel="hospital"
        data-display="grid"
        id="hospital-tab"
    >
        <?php if (empty($hospitals)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <h3>Chưa có bệnh viện nào</h3>
                <p>Hiện tại chưa có bệnh viện nào trong hệ thống.</p>
            </div>
        <?php else: ?>
            <?php foreach ($hospitals as $hospital): ?>
                <a href="FacilityDetail.php?id=<?php echo $hospital['facility_id']; ?>" class="facility-card reveal" style="text-decoration: none; color: inherit;">
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
    </section>

    <section
        class="tab-panel facility-grid <?php echo ($active_tab == 'clinic') ? 'active' : ''; ?>"
        data-tab-panel="clinic"
        data-display="grid"
        id="clinic-tab"
    >
        <?php if (empty($clinics)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <h3>Chưa có phòng khám nào</h3>
                <p>Hiện tại chưa có phòng khám nào trong hệ thống.</p>
            </div>
        <?php else: ?>
            <?php foreach ($clinics as $clinic): ?>
                <a href="FacilityDetail.php?id=<?php echo $clinic['facility_id']; ?>" class="facility-card reveal" style="text-decoration: none; color: inherit;">
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
    </section>
</main>

<?php include 'footer.php'; ?>

