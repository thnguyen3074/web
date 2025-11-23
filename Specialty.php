<?php
/**
 * Danh sách chuyên khoa - Medicare
 * Hiển thị danh sách các chuyên khoa từ MySQL
 */

$pageTitle = 'Chuyên khoa';
require_once 'config.php';
include 'header.php';

// Lấy danh sách chuyên khoa từ database
$specialties = [];
$sql = "SELECT * FROM specialties ORDER BY specialty_name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $specialties[] = $row;
    }
}
?>

<main class="page specialty-page">
    <section class="page-hero">
        <h1>Chuyên khoa</h1>
        <p>Lựa chọn chuyên khoa phù hợp để đặt lịch khám.</p>
    </section>

    <section class="specialty-grid-large">
        <?php if (empty($specialties)): ?>
            <h3>Chưa có chuyên khoa nào</h3>
            <p>Hiện tại chưa có chuyên khoa nào trong hệ thống.</p>
        <?php else: ?>
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
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

