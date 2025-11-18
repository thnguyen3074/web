<?php
/**
 * Facility Admin Specialties Management - Medicare
 * Quản lý chuyên khoa của cơ sở y tế
 */

$pageTitle = 'Quản lý chuyên khoa';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);

// Xử lý thêm chuyên khoa vào cơ sở
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $specialty_id = intval($_POST['specialty_id']);
    
    // Kiểm tra chuyên khoa đã tồn tại trong cơ sở chưa
    $check_exists = "SELECT id FROM facility_specialty WHERE facility_id = $facility_id AND specialty_id = $specialty_id";
    $result_check = mysqli_query($conn, $check_exists);
    if (mysqli_num_rows($result_check) == 0) {
        $sql_insert = "INSERT INTO facility_specialty (facility_id, specialty_id) VALUES ($facility_id, $specialty_id)";
        mysqli_query($conn, $sql_insert);
    }
    header('Location: facility-admin-specialties.php');
    exit();
}

// Xử lý xóa chuyên khoa khỏi cơ sở
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $specialty_id = intval($_GET['remove']);
    $sql_delete = "DELETE FROM facility_specialty WHERE facility_id = $facility_id AND specialty_id = $specialty_id";
    mysqli_query($conn, $sql_delete);
    header('Location: facility-admin-specialties.php');
    exit();
}

// Lấy danh sách chuyên khoa hiện có của cơ sở
$facility_specialties = [];
$sql_facility_specialties = "SELECT s.specialty_id, s.specialty_name, s.icon
                             FROM specialties s
                             JOIN facility_specialty fs ON s.specialty_id = fs.specialty_id
                             WHERE fs.facility_id = $facility_id
                             ORDER BY s.specialty_name";
$result_facility_specialties = mysqli_query($conn, $sql_facility_specialties);
if ($result_facility_specialties) {
    while ($row = mysqli_fetch_assoc($result_facility_specialties)) {
        $facility_specialties[] = $row;
    }
}

// Lấy danh sách tất cả chuyên khoa để thêm vào
$all_specialties = [];
$sql_all_specialties = "SELECT s.specialty_id, s.specialty_name, s.icon
                        FROM specialties s
                        WHERE s.specialty_id NOT IN (
                            SELECT specialty_id FROM facility_specialty WHERE facility_id = $facility_id
                        )
                        ORDER BY s.specialty_name";
$result_all_specialties = mysqli_query($conn, $sql_all_specialties);
if ($result_all_specialties) {
    while ($row = mysqli_fetch_assoc($result_all_specialties)) {
        $all_specialties[] = $row;
    }
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý chuyên khoa</h1>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="margin-bottom: 15px;">Chuyên khoa hiện có</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên chuyên khoa</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($facility_specialties)): ?>
                        <tr>
                            <td colspan="3">Chưa có chuyên khoa nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($facility_specialties as $specialty): ?>
                            <tr>
                                <td><?php echo $specialty['specialty_id']; ?></td>
                                <td><?php echo htmlspecialchars($specialty['specialty_name']); ?></td>
                                <td>
                                    <a href="facility-admin-specialties.php?remove=<?php echo $specialty['specialty_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa chuyên khoa này khỏi cơ sở?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($all_specialties)): ?>
        <div>
            <h2 style="margin-bottom: 15px;">Thêm chuyên khoa mới</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên chuyên khoa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_specialties as $specialty): ?>
                            <tr>
                                <td><?php echo $specialty['specialty_id']; ?></td>
                                <td><?php echo htmlspecialchars($specialty['specialty_name']); ?></td>
                                <td>
                                    <form method="POST" action="facility-admin-specialties.php" style="display: inline;">
                                        <input type="hidden" name="action" value="add" />
                                        <input type="hidden" name="specialty_id" value="<?php echo $specialty['specialty_id']; ?>" />
                                        <button type="submit" class="btn-admin-primary">Thêm</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div style="background: #e7f3ff; padding: 15px; border-radius: 4px; margin-top: 20px;">
            <p>Tất cả chuyên khoa đã được thêm vào cơ sở này.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'facility-admin-footer.php'; ?>

