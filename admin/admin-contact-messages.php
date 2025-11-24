<?php
// Admin Contact Messages Management - Quản lý yêu cầu hỗ trợ

$pageTitle = 'Yêu cầu hỗ trợ';
require_once '../config.php';
include 'admin-header.php';

// Xóa yêu cầu hỗ trợ
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM contact_messages WHERE id = $message_id";
    mysqli_query($conn, $sql_delete);
    header('Location: admin-contact-messages.php');
    exit();
}

// Lấy tham số tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page);
$offset = ($page - 1) * $per_page;

// Xây dựng điều kiện WHERE động cho filter
$where_conditions = [];

// Tìm kiếm
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(fullname LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR subject LIKE '%$search_escaped%' OR message LIKE '%$search_escaped%')";
}

// Filter theo khoảng thời gian
if (!empty($date_from)) {
    $date_from_escaped = mysqli_real_escape_string($conn, $date_from);
    $where_conditions[] = "DATE(created_at) >= '$date_from_escaped'";
}
if (!empty($date_to)) {
    $date_to_escaped = mysqli_real_escape_string($conn, $date_to);
    $where_conditions[] = "DATE(created_at) <= '$date_to_escaped'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Đếm tổng số bản ghi
$sql_count = "SELECT COUNT(*) AS total FROM contact_messages $where_clause";
$result_count = mysqli_query($conn, $sql_count);
$total_records = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_records / $per_page);

// Lấy danh sách yêu cầu hỗ trợ với phân trang
$messages = [];
$sql = "SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

// Format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y H:i');
}

// Format ngày ngắn
function formatDateShort($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y');
}
?>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="page-title">Yêu cầu hỗ trợ</h1>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="color: #666; font-size: 14px;">Tổng: <strong><?php echo $total_records; ?></strong> yêu cầu</span>
        </div>
    </div>

    <!-- Form tìm kiếm và lọc -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="GET" action="admin-contact-messages.php" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Tìm kiếm</label>
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="Tên, email, chủ đề, nội dung..." 
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                />
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Từ ngày</label>
                <input 
                    type="date" 
                    name="date_from" 
                    value="<?php echo htmlspecialchars($date_from); ?>" 
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                />
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Đến ngày</label>
                <input 
                    type="date" 
                    name="date_to" 
                    value="<?php echo htmlspecialchars($date_to); ?>" 
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                />
            </div>
            <div>
                <button type="submit" class="btn-admin-primary" style="padding: 8px 20px; height: fit-content;">Tìm kiếm</button>
                <?php if (!empty($search) || !empty($date_from) || !empty($date_to)): ?>
                    <a href="admin-contact-messages.php" class="btn-admin-secondary" style="display: inline-block; margin-top: 5px; padding: 8px 16px; text-decoration: none;">Xóa bộ lọc</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Danh sách yêu cầu hỗ trợ -->
    <?php if (empty($messages)): ?>
        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <p style="color: #999; font-size: 16px;">Không có yêu cầu hỗ trợ nào.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Chủ đề</th>
                        <th>Nội dung</th>
                        <th>Ngày gửi</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?php echo $msg['id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['fullname']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: #007bff; text-decoration: none;">
                                    <?php echo htmlspecialchars($msg['email']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars(mb_substr($msg['message'], 0, 100)) . (mb_strlen($msg['message']) > 100 ? '...' : ''); ?>
                            </td>
                            <td><?php echo formatDate($msg['created_at']); ?></td>
                            <td>
                                <a href="admin-contact-detail.php?id=<?php echo $msg['id']; ?>" class="btn-admin-secondary" style="padding: 4px 8px; font-size: 12px; text-decoration: none; margin-right: 5px;">Chi tiết</a>
                                <a href="admin-contact-messages.php?delete=<?php echo $msg['id']; ?>" class="btn-delete" style="padding: 4px 8px; font-size: 12px; text-decoration: none;" onclick="return confirm('Bạn có chắc muốn xóa yêu cầu hỗ trợ này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>" class="btn-admin-secondary" style="padding: 8px 16px; text-decoration: none;">‹ Trước</a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>" 
                       class="btn-admin-<?php echo $i == $page ? 'primary' : 'secondary'; ?>" 
                       style="padding: 8px 16px; text-decoration: none;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>" class="btn-admin-secondary" style="padding: 8px 16px; text-decoration: none;">Sau ›</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'admin-footer.php'; ?>

