<?php
/**
 * Admin Specialties Management - Medicare
 * CRUD quản lý chuyên khoa
 */

$pageTitle = 'Quản lý chuyên khoa';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa specialty
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $specialty_id = intval($_GET['delete']);
    // Xóa các liên kết facility_specialty trước
    $sql_delete_links = "DELETE FROM facility_specialty WHERE specialty_id = $specialty_id";
    mysqli_query($conn, $sql_delete_links);
    // Xóa specialty
    $sql_delete = "DELETE FROM specialties WHERE specialty_id = $specialty_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-specialties.php');
    exit();
}

// Xử lý thêm/sửa specialty
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $specialty_name = mysqli_real_escape_string($conn, $_POST['specialty_name']);
    
    // Xử lý upload icon
    $icon_path = '';
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
        $file = $_FILES['icon'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Kiểm tra loại file
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('specialty_', true) . '.' . $file_ext;
            $upload_dir = '../images/specialties/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $icon_path = 'images/specialties/' . $new_filename;
            }
        }
    }
    
    if (isset($_POST['specialty_id']) && is_numeric($_POST['specialty_id'])) {
        // Update
        $specialty_id = intval($_POST['specialty_id']);
        
        // Nếu có upload icon mới, cập nhật icon
        if (!empty($icon_path)) {
            // Lấy icon cũ để xóa
            $sql_old = "SELECT icon FROM specialties WHERE specialty_id = $specialty_id";
            $result_old = mysqli_query($conn, $sql_old);
            if ($result_old) {
                $old_specialty = mysqli_fetch_assoc($result_old);
                if (!empty($old_specialty['icon']) && file_exists('../' . $old_specialty['icon'])) {
                    unlink('../' . $old_specialty['icon']);
                }
            }
            $sql_update = "UPDATE specialties SET specialty_name = '$specialty_name', icon = '$icon_path' WHERE specialty_id = $specialty_id";
        } else {
            // Giữ nguyên icon cũ
            $sql_update = "UPDATE specialties SET specialty_name = '$specialty_name' WHERE specialty_id = $specialty_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert
        if (empty($icon_path)) {
            $sql_insert = "INSERT INTO specialties (specialty_name, icon) VALUES ('$specialty_name', NULL)";
        } else {
            $sql_insert = "INSERT INTO specialties (specialty_name, icon) VALUES ('$specialty_name', '$icon_path')";
        }
        mysqli_query($conn, $sql_insert);
    }
    header('Location: admin-specialties.php');
    exit();
}

// Lấy danh sách chuyên khoa
$specialties = [];
$sql = "SELECT * FROM specialties ORDER BY specialty_name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $specialties[] = $row;
    }
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý chuyên khoa</h1>
        <button class="btn-admin-primary" onclick="openModal('specialtyModal')">
            + Thêm chuyên khoa
        </button>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên chuyên khoa</th>
                    <th>Icon</th>
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($specialties)): ?>
                    <tr>
                        <td colspan="4">Chưa có chuyên khoa nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($specialties as $specialty): ?>
                        <tr>
                            <td><?php echo $specialty['specialty_id']; ?></td>
                            <td><?php echo htmlspecialchars($specialty['specialty_name']); ?></td>
                            <td>
                                <?php if (!empty($specialty['icon'])): ?>
                                    <img src="<?php echo htmlspecialchars($specialty['icon']); ?>" alt="Icon" class="specialty-icon" />
                                <?php else: ?>
                                    <span>Chưa có icon</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="editSpecialty(<?php echo $specialty['specialty_id']; ?>, '<?php echo htmlspecialchars($specialty['specialty_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($specialty['icon'], ENT_QUOTES); ?>')">Edit</button>
                                <a href="admin-specialties.php?delete=<?php echo $specialty['specialty_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa chuyên khoa này?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal thêm/sửa chuyên khoa -->
<div id="specialtyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Thêm chuyên khoa</h2>
            <span class="close" onclick="closeModal('specialtyModal')">&times;</span>
        </div>
        <form class="modal-form" method="POST" action="admin-specialties.php" enctype="multipart/form-data">
            <input type="hidden" name="specialty_id" id="specialty-id" />
            <div class="form-group">
                <label for="specialty-name">Tên chuyên khoa</label>
                <input type="text" id="specialty-name" name="specialty_name" required />
            </div>
            <div class="form-group">
                <label for="specialty-icon">Icon chuyên khoa</label>
                <input type="file" id="specialty-icon" name="icon" accept="image/jpeg,image/jpg,image/png" />
                <small>Chỉ chấp nhận file JPG, PNG (tối đa 5MB)</small>
                <div id="current-icon-preview" style="margin-top: 10px;"></div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('specialtyModal')">Hủy</button>
                <button type="submit" class="btn-admin-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.getElementById('specialty-id').value = '';
    document.getElementById('modal-title').textContent = 'Thêm chuyên khoa';
    document.getElementById('specialty-name').value = '';
    document.getElementById('specialty-icon').value = '';
    document.getElementById('current-icon-preview').innerHTML = '';
}

function editSpecialty(id, name, icon) {
    document.getElementById('specialty-id').value = id;
    document.getElementById('modal-title').textContent = 'Chỉnh sửa chuyên khoa';
    document.getElementById('specialty-name').value = name;
    document.getElementById('specialty-icon').value = '';
    
    // Hiển thị icon hiện tại
    const preview = document.getElementById('current-icon-preview');
    if (icon && icon.trim() !== '') {
        preview.innerHTML = '<p>Icon hiện tại:</p><img src="../' + icon + '" alt="Current icon" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px;" />';
    } else {
        preview.innerHTML = '<p>Chưa có icon</p>';
    }
    
    document.getElementById('specialtyModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include 'admin-footer.php'; ?>

