<?php
// Facility Admin Specialties Management - Quản lý chuyên khoa của cơ sở y tế

$pageTitle = 'Quản lý chuyên khoa';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);

// Thêm chuyên khoa vào cơ sở
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $specialty_id = intval($_POST['specialty_id']);
    
    // Kiểm tra chuyên khoa chưa tồn tại trong cơ sở (tránh duplicate)
    $check_exists = "SELECT id FROM facility_specialty WHERE facility_id = $facility_id AND specialty_id = $specialty_id";
    $result_check = mysqli_query($conn, $check_exists);
    if (mysqli_num_rows($result_check) == 0) {
        $sql_insert = "INSERT INTO facility_specialty (facility_id, specialty_id) VALUES ($facility_id, $specialty_id)";
        mysqli_query($conn, $sql_insert);
    }
    header('Location: facility-admin-specialties.php');
    exit();
}

// Xóa chuyên khoa khỏi cơ sở (yêu cầu xác nhận)
$confirm_remove = isset($_GET['confirm_remove']) && is_numeric($_GET['confirm_remove']);
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $specialty_id = intval($_GET['remove']);
    
    // Yêu cầu xác nhận trước khi xóa
    if (!$confirm_remove) {
        $show_remove_confirm = true;
        $remove_specialty_id = $specialty_id;
    } else {
        // Xóa liên kết giữa facility và specialty
        $sql_delete = "DELETE FROM facility_specialty WHERE facility_id = $facility_id AND specialty_id = $specialty_id";
        mysqli_query($conn, $sql_delete);
        header('Location: facility-admin-specialties.php');
        exit();
    }
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

// Lấy danh sách chuyên khoa chưa được thêm vào cơ sở (để thêm mới)
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

    <?php if (isset($show_remove_confirm) && $show_remove_confirm): ?>
        <!-- Form xác nhận xóa chuyên khoa -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; max-width: 500px;">
            <h2 style="margin-bottom: 15px; color: var(--admin-danger);">Xác nhận xóa chuyên khoa</h2>
            <p style="margin-bottom: 20px;">Bạn có chắc muốn xóa chuyên khoa này khỏi cơ sở?</p>
            <div style="display: flex; gap: 10px;">
                <a href="facility-admin-specialties.php?remove=<?php echo $remove_specialty_id; ?>&confirm_remove=1" class="btn-delete" style="text-decoration: none; padding: 10px 20px; display: inline-block;">Xác nhận xóa</a>
                <a href="facility-admin-specialties.php" class="btn-admin-secondary" style="text-decoration: none; padding: 10px 20px; display: inline-block;">Hủy bỏ</a>
            </div>
        </div>
    <?php endif; ?>

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
                                    <a href="facility-admin-specialties.php?remove=<?php echo $specialty['specialty_id']; ?>" class="btn-delete" style="text-decoration: none; padding: 6px 12px; display: inline-block;">Xóa</a>
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

