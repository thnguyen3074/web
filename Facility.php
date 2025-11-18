<?php
/**
 * Danh sách cơ sở y tế - Medicare
 * Hiển thị danh sách bệnh viện và phòng khám từ MySQL
 */

$pageTitle = 'Cơ sở y tế';
require_once 'config.php';
include 'header.php';

// Lấy tab từ URL để tự động mở đúng tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'hospital';
if ($active_tab != 'hospital' && $active_tab != 'clinic') {
    $active_tab = 'hospital';
}

// Lấy danh sách bệnh viện từ database
$hospitals = [];
$sql_hospitals = "SELECT * FROM facilities WHERE type='hospital' ORDER BY facility_id";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

// Lấy danh sách phòng khám từ database
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
        class="tab-panel facility-grid hospital-list <?php echo ($active_tab == 'hospital') ? 'active default-show' : ''; ?>"
        data-tab-panel="hospital"
        data-display="grid"
        id="hospital-tab"
    >
        <?php if (empty($hospitals)): ?>
            <p>Chưa có bệnh viện nào trong hệ thống.</p>
        <?php else: ?>
            <?php foreach ($hospitals as $hospital): ?>
                <article class="facility-card reveal">
                    <?php if (!empty($hospital['image'])): ?>
                        <img src="<?php echo htmlspecialchars($hospital['image']); ?>" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                    <?php else: ?>
                        <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($hospital['name']); ?>" />
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($hospital['name']); ?></h3>
                        <p><?php echo htmlspecialchars($hospital['address']); ?></p>
                        <p>Giờ làm việc: <?php echo htmlspecialchars($hospital['working_hours']); ?></p>
                        <a href="FacilityDetail.php?id=<?php echo $hospital['facility_id']; ?>" class="btn-secondary">Xem chi tiết</a>
                    </div>
                </article>
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
            <p>Chưa có phòng khám nào trong hệ thống.</p>
        <?php else: ?>
            <?php foreach ($clinics as $clinic): ?>
                <article class="facility-card reveal">
                    <?php if (!empty($clinic['image'])): ?>
                        <img src="<?php echo htmlspecialchars($clinic['image']); ?>" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                    <?php else: ?>
                        <img src="images/facilities/default.jpg" alt="<?php echo htmlspecialchars($clinic['name']); ?>" />
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($clinic['name']); ?></h3>
                        <p><?php echo htmlspecialchars($clinic['address']); ?></p>
                        <p>Giờ làm việc: <?php echo htmlspecialchars($clinic['working_hours']); ?></p>
                        <a href="FacilityDetail.php?id=<?php echo $clinic['facility_id']; ?>" class="btn-secondary">Xem chi tiết</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<script>
// Khởi tạo tab khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = '<?php echo $active_tab; ?>';
    if (activeTab) {
        switchTab(activeTab, false);
    }
});

function switchTab(tab, updateUrl = true) {
    // Ẩn tất cả tab panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active', 'default-show');
    });
    
    // Xóa active từ tất cả buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Hiển thị tab được chọn
    const tabPanel = document.getElementById(tab + '-tab');
    const tabButton = document.querySelector('[data-tab-target="' + tab + '"]');
    
    if (tabPanel) {
        tabPanel.classList.add('active');
    }
    if (tabButton) {
        tabButton.classList.add('active');
    }
    
    // Cập nhật URL nếu cần
    if (updateUrl) {
        window.history.pushState({}, '', 'Facility.php?tab=' + tab);
    }
}

// Xử lý click vào tab button
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab-target');
        if (tab) {
            switchTab(tab, true);
        }
    });
});
</script>

<?php include 'footer.php'; ?>

