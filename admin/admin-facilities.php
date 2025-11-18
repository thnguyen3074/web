<?php
/**
 * Admin Facilities Management - Medicare
 * CRUD quản lý cơ sở y tế
 */

$pageTitle = 'Quản lý cơ sở y tế';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa facility
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $facility_id = intval($_GET['delete']);
    // Xóa các liên kết chuyên khoa trước
    $sql_delete_links = "DELETE FROM facility_specialty WHERE facility_id = $facility_id";
    mysqli_query($conn, $sql_delete_links);
    // Xóa facility
    $sql_delete = "DELETE FROM facilities WHERE facility_id = $facility_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-facilities.php');
    exit();
}

// Xử lý tạo tài khoản Facility Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_facility_admin') {
    $facility_id = intval($_POST['facility_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Kiểm tra email trùng
    $check_email = "SELECT admin_id FROM facility_admins WHERE email = '$email'";
    $result_check = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result_check) > 0) {
        header('Location: admin-facilities.php?error=email_exists');
        exit();
    }
    
    // Kiểm tra facility đã có admin chưa
    $check_facility_admin = "SELECT admin_id FROM facility_admins WHERE facility_id = $facility_id";
    $result_check_facility = mysqli_query($conn, $check_facility_admin);
    if (mysqli_num_rows($result_check_facility) > 0) {
        header('Location: admin-facilities.php?error=facility_has_admin');
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
    header('Location: admin-facilities.php?success=facility_admin_created');
    exit();
}

// Xử lý thêm/sửa facility
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset($_POST['action']) || $_POST['action'] != 'create_facility_admin')) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $working_hours = mysqli_real_escape_string($conn, $_POST['working_hours']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Xử lý upload ảnh
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Kiểm tra loại file
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('facility_', true) . '.' . $file_ext;
            $upload_dir = '../images/facilities/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'images/facilities/' . $new_filename;
            }
        }
    }
    
    if (isset($_POST['facility_id']) && is_numeric($_POST['facility_id'])) {
        // Update
        $facility_id = intval($_POST['facility_id']);
        
        // Nếu có upload ảnh mới, cập nhật image
        if (!empty($image_path)) {
            // Lấy ảnh cũ để xóa
            $sql_old = "SELECT image FROM facilities WHERE facility_id = $facility_id";
            $result_old = mysqli_query($conn, $sql_old);
            if ($result_old) {
                $old_facility = mysqli_fetch_assoc($result_old);
                if (!empty($old_facility['image']) && file_exists('../' . $old_facility['image'])) {
                    unlink('../' . $old_facility['image']);
                }
            }
            $sql_update = "UPDATE facilities SET name = '$name', type = '$type', address = '$address', phone = '$phone', working_hours = '$working_hours', description = '$description', image = '$image_path' WHERE facility_id = $facility_id";
        } else {
            // Giữ nguyên ảnh cũ
            $sql_update = "UPDATE facilities SET name = '$name', type = '$type', address = '$address', phone = '$phone', working_hours = '$working_hours', description = '$description' WHERE facility_id = $facility_id";
        }
        mysqli_query($conn, $sql_update);
    } else {
        // Insert
        if (empty($image_path)) {
            $sql_insert = "INSERT INTO facilities (name, type, address, phone, working_hours, description, image) VALUES ('$name', '$type', '$address', '$phone', '$working_hours', '$description', NULL)";
        } else {
            $sql_insert = "INSERT INTO facilities (name, type, address, phone, working_hours, description, image) VALUES ('$name', '$type', '$address', '$phone', '$working_hours', '$description', '$image_path')";
        }
        mysqli_query($conn, $sql_insert);
    }
    header('Location: admin-facilities.php');
    exit();
}

// Lấy danh sách bệnh viện và kiểm tra đã có facility admin chưa
$hospitals = [];
$sql_hospitals = "SELECT f.*, 
                         (SELECT COUNT(*) FROM facility_admins WHERE facility_id = f.facility_id) AS has_admin
                  FROM facilities f 
                  WHERE f.type = 'hospital' 
                  ORDER BY f.facility_id DESC";
$result_hospitals = mysqli_query($conn, $sql_hospitals);
if ($result_hospitals) {
    while ($row = mysqli_fetch_assoc($result_hospitals)) {
        $hospitals[] = $row;
    }
}

// Lấy danh sách phòng khám và kiểm tra đã có facility admin chưa
$clinics = [];
$sql_clinics = "SELECT f.*, 
                       (SELECT COUNT(*) FROM facility_admins WHERE facility_id = f.facility_id) AS has_admin
                FROM facilities f 
                WHERE f.type = 'clinic' 
                ORDER BY f.facility_id DESC";
$result_clinics = mysqli_query($conn, $sql_clinics);
if ($result_clinics) {
    while ($row = mysqli_fetch_assoc($result_clinics)) {
        $clinics[] = $row;
    }
}

// Lấy thông báo lỗi/thành công từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý cơ sở y tế</h1>
        <button class="btn-admin-primary" onclick="openModal('facilityModal')">
            + Thêm cơ sở y tế
        </button>
    </div>

    <?php if ($error == 'email_exists'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Email này đã được sử dụng. Vui lòng chọn email khác.
        </div>
    <?php elseif ($error == 'facility_has_admin'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Cơ sở này đã có tài khoản quản trị viên. Mỗi cơ sở chỉ có thể có 1 tài khoản quản trị.
        </div>
    <?php elseif ($error == 'password_required'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #fcc;">
            Vui lòng nhập mật khẩu.
        </div>
    <?php endif; ?>

    <?php if ($success == 'facility_admin_created'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #c3e6cb;">
            Tạo tài khoản quản trị viên cơ sở thành công!
        </div>
    <?php endif; ?>

    <div class="admin-tabs">
        <button class="tab-btn active" data-tab="hospital" onclick="switchTab('hospital')">Bệnh viện</button>
        <button class="tab-btn" data-tab="clinic" onclick="switchTab('clinic')">Phòng khám</button>
    </div>

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
                                    <button class="btn-edit" onclick="editFacility(<?php echo $hospital['facility_id']; ?>, '<?php echo htmlspecialchars($hospital['name'], ENT_QUOTES); ?>', 'hospital', '<?php echo htmlspecialchars($hospital['address'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($hospital['phone'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($hospital['working_hours'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($hospital['description'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($hospital['image'] ?? '', ENT_QUOTES); ?>')">Edit</button>
                                    <a href="admin-facilities.php?delete=<?php echo $hospital['facility_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa cơ sở này?')">Delete</a>
                                    <?php if ($hospital['has_admin'] == 0): ?>
                                        <button class="btn-confirm" onclick="openFacilityAdminModal(<?php echo $hospital['facility_id']; ?>, '<?php echo htmlspecialchars($hospital['name'], ENT_QUOTES); ?>')" style="margin-left: 5px;">Tạo Facility Admin</button>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-size: 14px; margin-left: 5px;">✓ Đã có admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-content" id="clinic-tab">
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
                                    <button class="btn-edit" onclick="editFacility(<?php echo $clinic['facility_id']; ?>, '<?php echo htmlspecialchars($clinic['name'], ENT_QUOTES); ?>', 'clinic', '<?php echo htmlspecialchars($clinic['address'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($clinic['phone'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($clinic['working_hours'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($clinic['description'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($clinic['image'] ?? '', ENT_QUOTES); ?>')">Edit</button>
                                    <a href="admin-facilities.php?delete=<?php echo $clinic['facility_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa cơ sở này?')">Delete</a>
                                    <?php if ($clinic['has_admin'] == 0): ?>
                                        <button class="btn-confirm" onclick="openFacilityAdminModal(<?php echo $clinic['facility_id']; ?>, '<?php echo htmlspecialchars($clinic['name'], ENT_QUOTES); ?>')" style="margin-left: 5px;">Tạo Facility Admin</button>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-size: 14px; margin-left: 5px;">✓ Đã có admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa cơ sở y tế -->
<div id="facilityModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Thêm cơ sở y tế</h2>
            <span class="close" onclick="closeModal('facilityModal')">&times;</span>
        </div>
        <form class="modal-form" method="POST" action="admin-facilities.php" enctype="multipart/form-data">
            <input type="hidden" name="facility_id" id="facility-id" />
            <div class="form-group">
                <label for="facility-name">Tên cơ sở y tế</label>
                <input type="text" id="facility-name" name="name" required />
            </div>
            <div class="form-group">
                <label for="facility-type">Loại</label>
                <select id="facility-type" name="type" required>
                    <option value="hospital">Bệnh viện</option>
                    <option value="clinic">Phòng khám</option>
                </select>
            </div>
            <div class="form-group">
                <label for="facility-address">Địa chỉ</label>
                <input type="text" id="facility-address" name="address" required />
            </div>
            <div class="form-group">
                <label for="facility-phone">Số điện thoại</label>
                <input type="tel" id="facility-phone" name="phone" required />
            </div>
            <div class="form-group">
                <label for="facility-hours">Giờ làm việc</label>
                <input type="text" id="facility-hours" name="working_hours" placeholder="VD: 7:00 - 21:00" required />
            </div>
            <div class="form-group">
                <label for="facility-description">Mô tả</label>
                <textarea id="facility-description" name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="facility-image">Ảnh cơ sở y tế</label>
                <input type="file" id="facility-image" name="image" accept="image/jpeg,image/jpg,image/png" />
                <small>Chỉ chấp nhận file JPG, PNG (tối đa 5MB)</small>
                <div id="current-image-preview" style="margin-top: 10px;"></div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('facilityModal')">Hủy</button>
                <button type="submit" class="btn-admin-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal tạo Facility Admin -->
<div id="facilityAdminModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tạo tài khoản Facility Admin</h2>
            <span class="close" onclick="closeModal('facilityAdminModal')">&times;</span>
        </div>
        <form class="modal-form" method="POST" action="admin-facilities.php">
            <input type="hidden" name="action" value="create_facility_admin" />
            <input type="hidden" name="facility_id" id="facility-admin-facility-id" />
            <div style="background: #e7f3ff; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                <strong>Cơ sở y tế:</strong> <span id="facility-admin-facility-name"></span>
            </div>
            <div class="form-group">
                <label for="facility-admin-fullname">Họ và tên</label>
                <input type="text" id="facility-admin-fullname" name="fullname" required />
            </div>
            <div class="form-group">
                <label for="facility-admin-email">Email</label>
                <input type="email" id="facility-admin-email" name="email" required />
            </div>
            <div class="form-group">
                <label for="facility-admin-password">Mật khẩu</label>
                <input type="password" id="facility-admin-password" name="password" required />
                <small>Mật khẩu sẽ được mã hóa trước khi lưu vào database</small>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('facilityAdminModal')">Hủy</button>
                <button type="submit" class="btn-admin-primary">Tạo tài khoản</button>
            </div>
        </form>
    </div>
</div>

<script>
function openFacilityAdminModal(facilityId, facilityName) {
    document.getElementById('facility-admin-facility-id').value = facilityId;
    document.getElementById('facility-admin-facility-name').textContent = facilityName;
    document.getElementById('facility-admin-fullname').value = '';
    document.getElementById('facility-admin-email').value = '';
    document.getElementById('facility-admin-password').value = '';
    document.getElementById('facilityAdminModal').style.display = 'block';
}

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.getElementById('facility-id').value = '';
    document.getElementById('modal-title').textContent = 'Thêm cơ sở y tế';
    document.getElementById('facility-name').value = '';
    document.getElementById('facility-type').value = 'hospital';
    document.getElementById('facility-address').value = '';
    document.getElementById('facility-phone').value = '';
    document.getElementById('facility-hours').value = '';
    document.getElementById('facility-description').value = '';
    document.getElementById('facility-image').value = '';
    document.getElementById('current-image-preview').innerHTML = '';
}

function editFacility(id, name, type, address, phone, hours, description, image) {
    document.getElementById('facility-id').value = id;
    document.getElementById('modal-title').textContent = 'Chỉnh sửa cơ sở y tế';
    document.getElementById('facility-name').value = name;
    document.getElementById('facility-type').value = type;
    document.getElementById('facility-address').value = address;
    document.getElementById('facility-phone').value = phone;
    document.getElementById('facility-hours').value = hours;
    document.getElementById('facility-description').value = description;
    document.getElementById('facility-image').value = '';
    
    // Hiển thị ảnh hiện tại
    const preview = document.getElementById('current-image-preview');
    if (image && image.trim() !== '') {
        preview.innerHTML = '<p>Ảnh hiện tại:</p><img src="../' + image + '" alt="Current image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;" />';
    } else {
        preview.innerHTML = '<p>Chưa có ảnh</p>';
    }
    
    document.getElementById('facilityModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(tab + '-tab').classList.add('active');
    event.target.classList.add('active');
}
</script>

<?php include 'admin-footer.php'; ?>

