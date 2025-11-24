<?php
// Admin Specialties Management - CRUD quản lý chuyên khoa

$pageTitle = 'Quản lý chuyên khoa';
require_once '../config.php';
include 'admin-header.php';

// Xóa specialty
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $specialty_id = intval($_GET['delete']);
    $sql_delete_links = "DELETE FROM facility_specialty WHERE specialty_id = $specialty_id";
    mysqli_query($conn, $sql_delete_links);
    $sql_delete = "DELETE FROM specialties WHERE specialty_id = $specialty_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-specialties.php');
    exit();
}

// Thêm/sửa specialty
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $specialty_name = mysqli_real_escape_string($conn, $_POST['specialty_name']);
    
    // Upload icon - validate và lưu file
    $icon_path = '';
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
        $file = $_FILES['icon'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png']; // Chỉ cho phép JPG, PNG
        $max_size = 5 * 1024 * 1024; // Tối đa 5MB
        
        // Kiểm tra loại file và kích thước
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('specialty_', true) . '.' . $file_ext; // Tên file unique
            $upload_dir = '../images/specialties/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            // Di chuyển file từ temp sang thư mục đích
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $icon_path = 'images/specialties/' . $new_filename;
            }
        }
    }
    
    // Update hoặc Insert specialty
    if (isset($_POST['specialty_id']) && is_numeric($_POST['specialty_id'])) {
        // Update specialty hiện có
        $specialty_id = intval($_POST['specialty_id']);
        
        if (!empty($icon_path)) {
            // Xóa icon cũ nếu có upload icon mới
            $sql_old = "SELECT icon FROM specialties WHERE specialty_id = $specialty_id";
            $result_old = mysqli_query($conn, $sql_old);
            if ($result_old) {
                $old_specialty = mysqli_fetch_assoc($result_old);
                if (!empty($old_specialty['icon']) && file_exists('../' . $old_specialty['icon'])) {
                    unlink('../' . $old_specialty['icon']); // Xóa file icon cũ
                }
            }
            $sql_update = "UPDATE specialties SET specialty_name = '$specialty_name', icon = '$icon_path' WHERE specialty_id = $specialty_id";
        } else {
            // Giữ nguyên icon cũ
            $sql_update = "UPDATE specialties SET specialty_name = '$specialty_name' WHERE specialty_id = $specialty_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert specialty mới
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

// Lấy tham số tìm kiếm và edit
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id = isset($_GET['edit']) && is_numeric($_GET['edit']) ? intval($_GET['edit']) : 0;
$show_form = isset($_GET['add']) || $edit_id > 0;

$edit_specialty = null;
if ($edit_id > 0) {
    $sql_edit = "SELECT * FROM specialties WHERE specialty_id = $edit_id";
    $result_edit = mysqli_query($conn, $sql_edit);
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_specialty = mysqli_fetch_assoc($result_edit);
    } else {
        $edit_id = 0;
        $show_form = false;
    }
}

// Xây dựng điều kiện WHERE
$where_conditions = [];
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "specialty_name LIKE '%$search_escaped%'";
}
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$specialties = [];
$sql = "SELECT * FROM specialties $where_clause ORDER BY specialty_name";
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
        <?php if (!$show_form): ?>
            <a href="admin-specialties.php?add=1" class="btn-admin-primary">
                + Thêm chuyên khoa
            </a>
        <?php else: ?>
            <a href="admin-specialties.php" class="btn-admin-secondary">
                ← Quay lại
            </a>
        <?php endif; ?>
    </div>

    <?php if ($show_form): ?>
        <!-- Form thêm/sửa chuyên khoa -->
        <div class="card">
            <h2><?php echo $edit_id > 0 ? 'Chỉnh sửa chuyên khoa' : 'Thêm chuyên khoa mới'; ?></h2>
            <form method="POST" action="admin-specialties.php" enctype="multipart/form-data">
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="specialty_id" value="<?php echo $edit_id; ?>" />
                <?php endif; ?>
                <div class="form-group">
                    <label for="specialty-name">Tên chuyên khoa <span class="text-red">*</span></label>
                    <input type="text" id="specialty-name" name="specialty_name" value="<?php echo $edit_specialty ? htmlspecialchars($edit_specialty['specialty_name']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="specialty-icon">Icon chuyên khoa</label>
                    <input type="file" id="specialty-icon" name="icon" accept="image/jpeg,image/jpg,image/png" />
                    <small>Chỉ chấp nhận file JPG, PNG (tối đa 5MB)</small>
                    <?php if ($edit_specialty && !empty($edit_specialty['icon'])): ?>
                        <?php 
                        $icon_path = str_replace('\\', '/', $edit_specialty['icon']);
                        if (strpos($icon_path, '../') !== 0 && strpos($icon_path, 'http') !== 0) {
                            $icon_path = '../' . $icon_path;
                        }
                        ?>
                        <div style="margin-top: 10px;">
                            <p>Icon hiện tại:</p>
                            <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="Current icon" class="img-preview-sm" onerror="this.src='../images/specialties/default.png'; this.onerror=null;" />
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-gap" style="margin-top: 20px;">
                    <a href="admin-specialties.php" class="btn-cancel">Hủy</a>
                    <button type="submit" class="btn-admin-primary">Lưu</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Form tìm kiếm -->
        <div class="card">
            <form method="GET" action="admin-specialties.php" class="form-row">
                <div class="form-field">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên chuyên khoa..." class="form-input-sm">
                </div>
                <div class="flex-gap">
                    <button type="submit" class="btn-admin-primary">Tìm kiếm</button>
                    <a href="admin-specialties.php" class="btn-admin-secondary">Xóa bộ lọc</a>
                </div>
            </form>
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
                                    <?php 
                                    // Sửa đường dẫn icon (thay backslash thành forward slash và thêm ../ nếu cần)
                                    $icon_path = str_replace('\\', '/', $specialty['icon']);
                                    // Nếu đường dẫn không bắt đầu bằng ../ thì thêm vào
                                    if (strpos($icon_path, '../') !== 0 && strpos($icon_path, 'http') !== 0) {
                                        $icon_path = '../' . $icon_path;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="Icon" class="specialty-icon" onerror="this.src='../images/specialties/default.png'; this.onerror=null;" />
                                <?php else: ?>
                                    <span>Chưa có icon</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin-specialties.php?edit=<?php echo $specialty['specialty_id']; ?>" class="btn-edit btn-sm">Sửa</a>
                                <a href="admin-specialties.php?delete=<?php echo $specialty['specialty_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa chuyên khoa này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'admin-footer.php'; ?>

