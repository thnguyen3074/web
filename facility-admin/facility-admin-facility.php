<?php
// Facility Admin Facility Management - Quản lý thông tin cơ sở y tế

$pageTitle = 'Quản lý cơ sở y tế';
require_once '../config.php';
include 'facility-admin-header.php';

$facility_id = intval($_SESSION['facility_id']);
$success_message = '';
$error_message = '';
$sql = "SELECT * FROM facilities WHERE facility_id = $facility_id";
$result = mysqli_query($conn, $sql);
$facility = mysqli_fetch_assoc($result);

if (!$facility) {
    header('Location: facility-admin-dashboard.php');
    exit();
}

// Cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_facility') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $working_hours = isset($_POST['working_hours']) ? trim($_POST['working_hours']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    if (empty($name) || empty($address) || empty($phone) || empty($working_hours)) {
        $error_message = 'Vui lòng điền đầy đủ thông tin bắt buộc (Tên, Địa chỉ, Số điện thoại, Giờ làm việc).';
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $address = mysqli_real_escape_string($conn, $address);
        $phone = mysqli_real_escape_string($conn, $phone);
        $working_hours = mysqli_real_escape_string($conn, $working_hours);
        $description = mysqli_real_escape_string($conn, $description);
        
        // Upload hình ảnh - validate và lưu file
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']; // Chỉ cho phép JPG, PNG, GIF
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            // Kiểm tra loại file và kích thước (tối đa 5MB)
            if (in_array($file_type, $allowed_types) && $file_size <= 5 * 1024 * 1024) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'facility_' . $facility_id . '_' . time() . '.' . $file_extension; // Tên file unique
                $upload_dir = '../images/facilities/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'images/facilities/' . $new_filename;
                    
                    // Xóa ảnh cũ nếu có
                    if (!empty($facility['image']) && file_exists('../' . $facility['image'])) {
                        @unlink('../' . $facility['image']);
                    }
                } else {
                    $error_message = 'Không thể upload hình ảnh.';
                }
            } else {
                $error_message = 'Hình ảnh không hợp lệ. Chỉ chấp nhận file JPG, PNG, GIF và kích thước tối đa 5MB.';
            }
        }
        
        // Cập nhật thông tin facility
        if (empty($error_message)) {
            if ($image_path) {
                // Cập nhật kèm ảnh mới
                $image_path_escaped = mysqli_real_escape_string($conn, $image_path);
                $sql_update = "UPDATE facilities SET 
                               name = '$name',
                               address = '$address',
                               phone = '$phone',
                               working_hours = '$working_hours',
                               description = '$description',
                               image = '$image_path_escaped'
                               WHERE facility_id = $facility_id";
            } else {
                // Giữ nguyên ảnh cũ
                $sql_update = "UPDATE facilities SET 
                               name = '$name',
                               address = '$address',
                               phone = '$phone',
                               working_hours = '$working_hours',
                               description = '$description'
                               WHERE facility_id = $facility_id";
            }
            
            if (mysqli_query($conn, $sql_update)) {
                $success_message = 'Cập nhật thông tin cơ sở y tế thành công!';
                $_SESSION['facility_name'] = $name;
            } else {
                $error_message = 'Có lỗi xảy ra khi cập nhật thông tin.';
            }
        }
    }
    $sql = "SELECT * FROM facilities WHERE facility_id = $facility_id";
    $result = mysqli_query($conn, $sql);
    $facility = mysqli_fetch_assoc($result);
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý thông tin cơ sở y tế</h1>
    </div>

    <?php if (!empty($success_message)): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; gap: 20px;">
        <!-- Form cập nhật thông tin -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px; color: var(--admin-primary);">Thông tin cơ sở y tế</h2>
            <form method="POST" action="facility-admin-facility.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_facility">
                
                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500;">Tên cơ sở y tế <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($facility['name']); ?>" required style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="type" style="display: block; margin-bottom: 5px; font-weight: 500;">Loại cơ sở</label>
                    <input type="text" id="type" value="<?php echo ($facility['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám'; ?>" disabled style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f5f5f5;">
                    <small style="color: #666; display: block; margin-top: 5px;">Loại cơ sở không thể thay đổi</small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="address" style="display: block; margin-bottom: 5px; font-weight: 500;">Địa chỉ <span style="color: red;">*</span></label>
                    <textarea id="address" name="address" rows="3" required style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($facility['address']); ?></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="phone" style="display: block; margin-bottom: 5px; font-weight: 500;">Số điện thoại <span style="color: red;">*</span></label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($facility['phone']); ?>" required style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="working_hours" style="display: block; margin-bottom: 5px; font-weight: 500;">Giờ làm việc <span style="color: red;">*</span></label>
                    <textarea id="working_hours" name="working_hours" rows="2" required style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($facility['working_hours']); ?></textarea>
                    <small style="color: #666; display: block; margin-top: 5px;">Ví dụ: Thứ 2-6: 7h00-17h00, Thứ 7: 7h00-12h00</small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="description" style="display: block; margin-bottom: 5px; font-weight: 500;">Mô tả</label>
                    <textarea id="description" name="description" rows="6" style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($facility['description']); ?></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="image" style="display: block; margin-bottom: 5px; font-weight: 500;">Hình ảnh</label>
                    <?php if (!empty($facility['image'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../<?php echo htmlspecialchars($facility['image']); ?>" alt="Hình ảnh hiện tại" style="max-width: 300px; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                            <p style="margin-top: 5px; color: #666; font-size: 14px;">Hình ảnh hiện tại</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif" style="width: 100%; max-width: 600px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #666; display: block; margin-top: 5px;">Chỉ chấp nhận file JPG, PNG, GIF. Kích thước tối đa 5MB.</small>
                </div>

                <button type="submit" class="btn-admin-primary">Cập nhật thông tin</button>
            </form>
        </div>

        <!-- Thông tin bổ sung -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 15px; color: var(--admin-primary);">Thông tin bổ sung</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; font-weight: bold; width: 200px; border-bottom: 1px solid #eee;">Mã cơ sở:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">#<?php echo $facility['facility_id']; ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold; border-bottom: 1px solid #eee;">Ngày tạo:</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo date('d/m/Y H:i', strtotime($facility['created_at'])); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php include 'facility-admin-footer.php'; ?>

