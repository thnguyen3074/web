<?php
/**
 * Admin Appointments Management - Medicare
 * Quản lý lịch hẹn
 */

$pageTitle = 'Quản lý lịch hẹn';
require_once '../config.php';
include 'admin-header.php';

// Xử lý xóa appointment
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $appointment_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM appointments WHERE appointment_id = $appointment_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-appointments.php');
    exit();
}

// Xử lý cập nhật trạng thái
if (isset($_GET['update_status']) && is_numeric($_GET['update_status']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['update_status']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $sql_update = "UPDATE appointments SET status = '$status' WHERE appointment_id = $appointment_id";
    mysqli_query($conn, $sql_update);
    header('Location: admin-appointments.php');
    exit();
}

// Lấy tham số tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$facility_filter = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Phân trang
$per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page);
$offset = ($page - 1) * $per_page;

// Xây dựng điều kiện WHERE
$where_conditions = [];

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(COALESCE(a.patient_name, u.fullname) LIKE '%$search_escaped%' OR COALESCE(a.patient_email, u.email) LIKE '%$search_escaped%' OR COALESCE(a.patient_phone, u.phone) LIKE '%$search_escaped%' OR f.name LIKE '%$search_escaped%' OR s.specialty_name LIKE '%$search_escaped%' OR a.symptoms LIKE '%$search_escaped%')";
}

// Thêm điều kiện lọc theo trạng thái
if (!empty($status_filter) && in_array($status_filter, ['pending', 'confirmed', 'completed', 'canceled'])) {
    $status_escaped = mysqli_real_escape_string($conn, $status_filter);
    $where_conditions[] = "a.status = '$status_escaped'";
}

// Thêm điều kiện lọc theo cơ sở y tế
if ($facility_filter > 0) {
    $where_conditions[] = "a.facility_id = $facility_filter";
}

// Thêm điều kiện lọc theo ngày
if (!empty($date_from)) {
    $date_from_escaped = mysqli_real_escape_string($conn, $date_from);
    $where_conditions[] = "a.appointment_date >= '$date_from_escaped'";
}
if (!empty($date_to)) {
    $date_to_escaped = mysqli_real_escape_string($conn, $date_to);
    $where_conditions[] = "a.appointment_date <= '$date_to_escaped'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Đếm tổng số bản ghi
$sql_count = "SELECT COUNT(*) AS total 
              FROM appointments a
              LEFT JOIN users u ON a.user_id = u.user_id
              JOIN facilities f ON a.facility_id = f.facility_id
              JOIN specialties s ON a.specialty_id = s.specialty_id
              $where_clause";
$result_count = mysqli_query($conn, $sql_count);
$total_records = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_records / $per_page);

// Lấy danh sách appointments với JOIN và filter
// Ưu tiên hiển thị thông tin từ appointments (patient_name, patient_email, patient_phone)
// Nếu không có thì mới lấy từ users (cho các lịch hẹn cũ)
$appointments = [];
$sql = "SELECT a.*, 
               COALESCE(a.patient_name, u.fullname) AS display_name,
               COALESCE(a.patient_email, u.email) AS display_email,
               COALESCE(a.patient_phone, u.phone) AS display_phone,
               f.name AS facility_name, 
               s.specialty_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        JOIN facilities f ON a.facility_id = f.facility_id
        JOIN specialties s ON a.specialty_id = s.specialty_id
        $where_clause
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Lấy danh sách facilities cho filter
$facilities = [];
$sql_facilities = "SELECT facility_id, name FROM facilities ORDER BY name";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    while ($row = mysqli_fetch_assoc($result_facilities)) {
        $facilities[] = $row;
    }
}

// Hàm format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y');
}

// Hàm format trạng thái
function formatStatus($status) {
    $status_text = [
        'pending' => 'Chờ',
        'confirmed' => 'Xác nhận',
        'completed' => 'Đã hoàn thành',
        'canceled' => 'Hủy'
    ];
    return isset($status_text[$status]) ? $status_text[$status] : $status;
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Quản lý lịch hẹn</h1>
    </div>

    <!-- Form tìm kiếm và lọc -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="GET" action="admin-appointments.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label for="search" style="display: block; margin-bottom: 5px; font-weight: 500;">Tìm kiếm</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên, email, SĐT, cơ sở..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label for="status" style="display: block; margin-bottom: 5px; font-weight: 500;">Trạng thái</label>
                <select id="status" name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">Tất cả</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                    <option value="canceled" <?php echo $status_filter == 'canceled' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <div>
                <label for="facility_id" style="display: block; margin-bottom: 5px; font-weight: 500;">Cơ sở y tế</label>
                <select id="facility_id" name="facility_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">Tất cả</option>
                    <?php foreach ($facilities as $facility): ?>
                        <option value="<?php echo $facility['facility_id']; ?>" <?php echo $facility_filter == $facility['facility_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($facility['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="date_from" style="display: block; margin-bottom: 5px; font-weight: 500;">Từ ngày</label>
                <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label for="date_to" style="display: block; margin-bottom: 5px; font-weight: 500;">Đến ngày</label>
                <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-admin-primary" style="flex: 1;">Tìm kiếm</button>
                <a href="admin-appointments.php" class="btn-admin-secondary" style="flex: 1; text-align: center; padding: 8px; text-decoration: none; display: inline-block;">Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên bệnh nhân</th>
                    <th>Cơ sở y tế</th>
                    <th>Chuyên khoa</th>
                    <th>Ngày khám</th>
                    <th>Giờ khám</th>
                    <th>Triệu chứng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr>
                        <td colspan="9">Chưa có lịch hẹn nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo $appointment['appointment_id']; ?></td>
                            <td>
                                <?php if (!empty($appointment['display_name'])): ?>
                                    <?php echo htmlspecialchars($appointment['display_name']); ?>
                                    <?php if (!empty($appointment['display_email'])): ?>
                                        <br><small style="color: #666;"><?php echo htmlspecialchars($appointment['display_email']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #999;">Khách (chưa đăng ký)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($appointment['facility_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialty_name']); ?></td>
                            <td><?php echo formatDate($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td style="max-width: 200px; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;"><?php echo htmlspecialchars(substr($appointment['symptoms'], 0, 50)) . (strlen($appointment['symptoms']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <span class="status-badge <?php echo $appointment['status']; ?>">
                                    <?php echo formatStatus($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <a href="admin-appointment-detail.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn-admin-secondary" style="padding: 4px 8px; font-size: 12px; text-decoration: none;">Chi tiết</a>
                                    <?php if ($appointment['status'] == 'pending'): ?>
                                        <a href="admin-appointments.php?<?php echo http_build_query(array_merge($_GET, ['update_status' => $appointment['appointment_id'], 'status' => 'confirmed'])); ?>" class="btn-confirm" style="padding: 4px 8px; font-size: 12px;">Xác nhận</a>
                                    <?php endif; ?>
                                    <?php if ($appointment['status'] != 'canceled' && $appointment['status'] != 'completed'): ?>
                                        <a href="admin-appointments.php?<?php echo http_build_query(array_merge($_GET, ['update_status' => $appointment['appointment_id'], 'status' => 'canceled'])); ?>" class="btn-cancel-action" style="padding: 4px 8px; font-size: 12px;" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">Hủy</a>
                                    <?php endif; ?>
                                    <a href="admin-appointments.php?<?php echo http_build_query(array_merge($_GET, ['delete' => $appointment['appointment_id']])); ?>" class="btn-delete" style="padding: 4px 8px; font-size: 12px;" onclick="return confirm('Bạn có chắc muốn xóa lịch hẹn này?')">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
        <div style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <?php
            // Tạo query string cho filter
            $query_params = [];
            if (!empty($search)) $query_params['search'] = $search;
            if (!empty($status_filter)) $query_params['status'] = $status_filter;
            if ($facility_filter > 0) $query_params['facility_id'] = $facility_filter;
            if (!empty($date_from)) $query_params['date_from'] = $date_from;
            if (!empty($date_to)) $query_params['date_to'] = $date_to;
            $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
            ?>
            
            <?php if ($page > 1): ?>
                <a href="admin-appointments.php?page=<?php echo ($page - 1); ?><?php echo $query_string; ?>" class="btn-admin-secondary" style="text-decoration: none; padding: 8px 16px;">← Trước</a>
            <?php endif; ?>
            
            <span style="padding: 8px 16px;">
                Trang <?php echo $page; ?> / <?php echo $total_pages; ?> 
                (Tổng: <?php echo $total_records; ?> lịch hẹn)
            </span>
            
            <?php if ($page < $total_pages): ?>
                <a href="admin-appointments.php?page=<?php echo ($page + 1); ?><?php echo $query_string; ?>" class="btn-admin-secondary" style="text-decoration: none; padding: 8px 16px;">Sau →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'admin-footer.php'; ?>

