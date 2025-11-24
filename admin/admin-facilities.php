<?php
// Admin Facilities Management - CRUD quản lý cơ sở y tế

$pageTitle = 'Quản lý cơ sở y tế';
require_once '../config.php';
include 'admin-header.php';

// Xóa facility
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $facility_id = intval($_GET['delete']);
    $sql_delete_links = "DELETE FROM facility_specialty WHERE facility_id = $facility_id";
    mysqli_query($conn, $sql_delete_links);
    $sql_delete = "DELETE FROM facilities WHERE facility_id = $facility_id";
    mysqli_query($conn, $sql_delete);
    $redirect_tab = isset($_GET['tab']) ? '?tab=' . $_GET['tab'] : '';
    header('Location: admin-facilities.php' . $redirect_tab);
    exit();
}

// Tạo tài khoản quản trị viên cơ sở y tế
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_facility_admin') {
    $facility_id = intval($_POST['facility_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    $check_email = "SELECT admin_id FROM facility_admins WHERE email = '$email'";
    $result_check = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result_check) > 0) {
        header('Location: admin-facilities.php?error=email_exists');
        exit();
    }
    $check_email_admin = "SELECT admin_id FROM admins WHERE email = '$email'";
    $result_check_admin = mysqli_query($conn, $check_email_admin);
    if (mysqli_num_rows($result_check_admin) > 0) {
        header('Location: admin-facilities.php?error=email_exists');
        exit();
    }
    
    if (empty($password)) {
        header('Location: admin-facilities.php?error=password_required');
        exit();
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $password_hash = mysqli_real_escape_string($conn, $password_hash);
    $sql_insert = "INSERT INTO facility_admins (facility_id, fullname, email, password) VALUES ($facility_id, '$fullname', '$email', '$password_hash')";
    mysqli_query($conn, $sql_insert);
    $redirect_tab = isset($_GET['tab']) ? '&tab=' . $_GET['tab'] : '';
    header('Location: admin-facilities.php?success=facility_admin_created' . $redirect_tab);
    exit();
}

// Thêm/sửa facility
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset($_POST['action']) || $_POST['action'] != 'create_facility_admin')) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $working_hours = mysqli_real_escape_string($conn, $_POST['working_hours']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Upload ảnh - validate và lưu file
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png']; // Chỉ cho phép JPG, PNG
        $max_size = 5 * 1024 * 1024; // Tối đa 5MB
        
        // Kiểm tra loại file và kích thước
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('facility_', true) . '.' . $file_ext; // Tên file unique để tránh trùng
            $upload_dir = '../images/facilities/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            // Di chuyển file từ temp sang thư mục đích
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'images/facilities/' . $new_filename;
            }
        }
    }
    
    // Update hoặc Insert facility
    if (isset($_POST['facility_id']) && is_numeric($_POST['facility_id'])) {
        // Update facility hiện có
        $facility_id = intval($_POST['facility_id']);
        
        if (!empty($image_path)) {
            // Xóa ảnh cũ nếu có upload ảnh mới
            $sql_old = "SELECT image FROM facilities WHERE facility_id = $facility_id";
            $result_old = mysqli_query($conn, $sql_old);
            if ($result_old) {
                $old_facility = mysqli_fetch_assoc($result_old);
                if (!empty($old_facility['image']) && file_exists('../' . $old_facility['image'])) {
                    unlink('../' . $old_facility['image']); // Xóa file ảnh cũ
                }
            }
            $sql_update = "UPDATE facilities SET name = '$name', type = '$type', address = '$address', phone = '$phone', working_hours = '$working_hours', description = '$description', image = '$image_path' WHERE facility_id = $facility_id";
        } else {
            // Giữ nguyên ảnh cũ
            $sql_update = "UPDATE facilities SET name = '$name', type = '$type', address = '$address', phone = '$phone', working_hours = '$working_hours', description = '$description' WHERE facility_id = $facility_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert facility mới
        if (empty($image_path)) {
            $sql_insert = "INSERT INTO facilities (name, type, address, phone, working_hours, description, image) VALUES ('$name', '$type', '$address', '$phone', '$working_hours', '$description', NULL)";
        } else {
            $sql_insert = "INSERT INTO facilities (name, type, address, phone, working_hours, description, image) VALUES ('$name', '$type', '$address', '$phone', '$working_hours', '$description', '$image_path')";
        }
        mysqli_query($conn, $sql_insert);
    }
    $redirect_tab = isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : (isset($_GET['tab']) ? '?tab=' . $_GET['tab'] : '');
    header('Location: admin-facilities.php' . $redirect_tab);
    exit();
}

// Lấy tham số tìm kiếm, edit, tab
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id = isset($_GET['edit']) && is_numeric($_GET['edit']) ? intval($_GET['edit']) : 0;
$create_admin_id = isset($_GET['create_admin']) && is_numeric($_GET['create_admin']) ? intval($_GET['create_admin']) : 0;
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['hospital', 'clinic']) ? $_GET['tab'] : 'hospital';
$show_form = isset($_GET['add']) || $edit_id > 0;
$show_admin_form = $create_admin_id > 0;

$edit_facility = null;
if ($edit_id > 0) {
    $sql_edit = "SELECT * FROM facilities WHERE facility_id = $edit_id";
    $result_edit = mysqli_query($conn, $sql_edit);
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_facility = mysqli_fetch_assoc($result_edit);
    } else {
        $edit_id = 0;
        $show_form = false;
    }
}

$create_admin_facility = null;
if ($create_admin_id > 0) {
    $sql_fac = "SELECT * FROM facilities WHERE facility_id = $create_admin_id";
    $result_fac = mysqli_query($conn, $sql_fac);
    if ($result_fac && mysqli_num_rows($result_fac) > 0) {
        $create_admin_facility = mysqli_fetch_assoc($result_fac);
    } else {
        $create_admin_id = 0;
        $show_admin_form = false;
    }
}

// Xây dựng điều kiện WHERE
$where_conditions_hospital = ["f.type = 'hospital'"];
$where_conditions_clinic = ["f.type = 'clinic'"];

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $search_condition = "(f.name LIKE '%$search_escaped%' OR f.address LIKE '%$search_escaped%' OR f.phone LIKE '%$search_escaped%')";
    $where_conditions_hospital[] = $search_condition;
    $where_conditions_clinic[] = $search_condition;
}

$where_hospital = implode(' AND ', $where_conditions_hospital);
$where_clinic = implode(' AND ', $where_conditions_clinic);

$hospitals = [];
$sql_hospitals = "SELECT f.*, 
                         (SELECT COUNT(*) FROM facility_admins WHERE facility_id = f.facility_id) AS admin_count
                  FROM facilities f 
                  WHERE $where_hospital
                  ORDER BY f.facility_id DESC";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

$clinics = [];
$sql_clinics = "SELECT f.*, 
                       (SELECT COUNT(*) FROM facility_admins WHERE facility_id = f.facility_id) AS admin_count
                FROM facilities f 
                WHERE $where_clinic
                ORDER BY f.facility_id DESC";
$result_clinics = mysqli_query($conn, $sql_clinics);
if ($result_clinics) {
    while ($row = mysqli_fetch_assoc($result_clinics)) {
        $clinics[] = $row;
    }
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý cơ sở y tế</h1>
        <?php if (!$show_form && !$show_admin_form): ?>
            <a href="admin-facilities.php?add=1&tab=<?php echo $tab; ?>" class="btn-admin-primary">
                + Thêm cơ sở y tế
            </a>
        <?php else: ?>
            <a href="admin-facilities.php?tab=<?php echo $tab; ?>" class="btn-admin-secondary">
                ← Quay lại
            </a>
        <?php endif; ?>
    </div>

    <?php if ($error == 'email_exists'): ?>
        <div class="alert alert-error">
            Email này đã được sử dụng. Vui lòng chọn email khác.
        </div>
    <?php elseif ($error == 'password_required'): ?>
        <div class="alert alert-error">
            Vui lòng nhập mật khẩu.
        </div>
    <?php endif; ?>

    <?php if ($success == 'facility_admin_created'): ?>
        <div class="alert alert-success">
            Tạo tài khoản quản trị viên cơ sở thành công!
        </div>
    <?php endif; ?>

    <?php if ($show_admin_form): ?>
        <!-- Form tạo quản trị viên cơ sở y tế -->
        <div class="card">
            <h2>Tạo tài khoản quản trị viên cơ sở y tế</h2>
            <div class="alert-info">
                <strong>Cơ sở y tế:</strong> <?php echo htmlspecialchars($create_admin_facility['name']); ?>
            </div>
            <form method="POST" action="admin-facilities.php">
                <input type="hidden" name="action" value="create_facility_admin" />
                <input type="hidden" name="facility_id" value="<?php echo $create_admin_id; ?>" />
                <div class="form-group">
                    <label for="facility-admin-fullname">Họ và tên <span class="text-red">*</span></label>
                    <input type="text" id="facility-admin-fullname" name="fullname" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-admin-email">Email <span class="text-red">*</span></label>
                    <input type="email" id="facility-admin-email" name="email" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-admin-password">Mật khẩu <span class="text-red">*</span></label>
                    <input type="password" id="facility-admin-password" name="password" required class="form-input" />
                    <small>Mật khẩu sẽ được mã hóa trước khi lưu vào database</small>
                </div>
                <div class="flex-gap" style="margin-top: 20px;">
                    <a href="admin-facilities.php?tab=<?php echo $tab; ?>" class="btn-cancel">Hủy</a>
                    <button type="submit" class="btn-admin-primary">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    <?php elseif ($show_form): ?>
        <!-- Form thêm/sửa cơ sở y tế -->
        <div class="card">
            <h2><?php echo $edit_id > 0 ? 'Chỉnh sửa cơ sở y tế' : 'Thêm cơ sở y tế mới'; ?></h2>
            <form method="POST" action="admin-facilities.php" enctype="multipart/form-data">
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="facility_id" value="<?php echo $edit_id; ?>" />
                <?php endif; ?>
                <div class="form-group">
                    <label for="facility-name">Tên cơ sở y tế <span class="text-red">*</span></label>
                    <input type="text" id="facility-name" name="name" value="<?php echo $edit_facility ? htmlspecialchars($edit_facility['name']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-type">Loại <span class="text-red">*</span></label>
                    <select id="facility-type" name="type" required class="form-input">
                        <option value="hospital" <?php echo ($edit_facility && $edit_facility['type'] == 'hospital') ? 'selected' : ''; ?>>Bệnh viện</option>
                        <option value="clinic" <?php echo ($edit_facility && $edit_facility['type'] == 'clinic') ? 'selected' : ''; ?>>Phòng khám</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="facility-address">Địa chỉ <span class="text-red">*</span></label>
                    <input type="text" id="facility-address" name="address" value="<?php echo $edit_facility ? htmlspecialchars($edit_facility['address']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-phone">Số điện thoại <span class="text-red">*</span></label>
                    <input type="tel" id="facility-phone" name="phone" value="<?php echo $edit_facility ? htmlspecialchars($edit_facility['phone']) : ''; ?>" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-hours">Giờ làm việc <span class="text-red">*</span></label>
                    <input type="text" id="facility-hours" name="working_hours" value="<?php echo $edit_facility ? htmlspecialchars($edit_facility['working_hours']) : ''; ?>" placeholder="VD: 7:00 - 21:00" required class="form-input" />
                </div>
                <div class="form-group">
                    <label for="facility-description">Mô tả</label>
                    <textarea id="facility-description" name="description" rows="4" class="form-input"><?php echo $edit_facility ? htmlspecialchars($edit_facility['description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="facility-image">Ảnh cơ sở y tế</label>
                    <input type="file" id="facility-image" name="image" accept="image/jpeg,image/jpg,image/png" />
                    <small>Chỉ chấp nhận file JPG, PNG (tối đa 5MB)</small>
                    <?php if ($edit_facility && !empty($edit_facility['image'])): ?>
                        <div style="margin-top: 10px;">
                            <p>Ảnh hiện tại:</p>
                            <img src="../<?php echo htmlspecialchars($edit_facility['image']); ?>" alt="Current image" class="img-preview" />
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-gap" style="margin-top: 20px;">
                    <a href="admin-facilities.php?tab=<?php echo $tab; ?>" class="btn-cancel">Hủy</a>
                    <button type="submit" class="btn-admin-primary">Lưu</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Form tìm kiếm -->
        <div class="card">
            <form method="GET" action="admin-facilities.php" class="form-row">
                <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>" />
                <div class="form-field">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên, địa chỉ, số điện thoại..." class="form-input-sm">
                </div>
                <div class="flex-gap">
                    <button type="submit" class="btn-admin-primary">Tìm kiếm</button>
                    <a href="admin-facilities.php?tab=<?php echo htmlspecialchars($tab); ?>" class="btn-admin-secondary">Xóa bộ lọc</a>
                </div>
            </form>
        </div>

        <div class="admin-tabs">
            <a href="admin-facilities.php?tab=hospital<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="tab-btn <?php echo $tab == 'hospital' ? 'active' : ''; ?>">Bệnh viện</a>
            <a href="admin-facilities.php?tab=clinic<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="tab-btn <?php echo $tab == 'clinic' ? 'active' : ''; ?>">Phòng khám</a>
        </div>

        <?php if ($tab == 'hospital'): ?>
            <div class="tab-content active" id="hospital-tab">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Giờ làm việc</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hospitals)): ?>
                                <tr>
                                    <td colspan="6">Chưa có bệnh viện nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hospitals as $hospital): ?>
                                    <tr>
                                        <td><?php echo $hospital['facility_id']; ?></td>
                                        <td><?php echo htmlspecialchars($hospital['name']); ?></td>
                                        <td><?php echo htmlspecialchars($hospital['address']); ?></td>
                                        <td><?php echo htmlspecialchars($hospital['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($hospital['working_hours']); ?></td>
                                <td>
                                    <a href="admin-facilities.php?edit=<?php echo $hospital['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-edit btn-sm">Sửa</a>
                                    <a href="admin-facilities.php?delete=<?php echo $hospital['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa cơ sở này?')">Xóa</a>
                                    <a href="admin-facilities.php?create_admin=<?php echo $hospital['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-confirm btn-sm">Tạo quản trị viên</a>
                                    <?php if ($hospital['admin_count'] > 0): ?>
                                        <span class="badge badge-success">✓ <?php echo $hospital['admin_count']; ?> admin</span>
                                    <?php endif; ?>
                                </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="tab-content active" id="clinic-tab">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Giờ làm việc</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clinics)): ?>
                                <tr>
                                    <td colspan="6">Chưa có phòng khám nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clinics as $clinic): ?>
                                    <tr>
                                        <td><?php echo $clinic['facility_id']; ?></td>
                                        <td><?php echo htmlspecialchars($clinic['name']); ?></td>
                                        <td><?php echo htmlspecialchars($clinic['address']); ?></td>
                                        <td><?php echo htmlspecialchars($clinic['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($clinic['working_hours']); ?></td>
                                <td>
                                    <a href="admin-facilities.php?edit=<?php echo $clinic['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-edit btn-sm">Sửa</a>
                                    <a href="admin-facilities.php?delete=<?php echo $clinic['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-delete btn-sm" onclick="return confirm('Bạn có chắc muốn xóa cơ sở này?')">Xóa</a>
                                    <a href="admin-facilities.php?create_admin=<?php echo $clinic['facility_id']; ?>&tab=<?php echo $tab; ?>" class="btn-confirm btn-sm">Tạo quản trị viên</a>
                                    <?php if ($clinic['admin_count'] > 0): ?>
                                        <span class="badge badge-success">✓ <?php echo $clinic['admin_count']; ?> admin</span>
                                    <?php endif; ?>
                                </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'admin-footer.php'; ?>

