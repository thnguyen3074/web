<?php
// Admin Contact Message Detail

$pageTitle = 'Chi tiết yêu cầu hỗ trợ';
require_once '../config.php';
include 'admin-header.php';

$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($message_id <= 0) {
    header('Location: admin-contact-messages.php');
    exit();
}

$sql = "SELECT * FROM contact_messages WHERE id = $message_id";
$result = mysqli_query($conn, $sql);
$message = mysqli_fetch_assoc($result);

if (!$message) {
    header('Location: admin-contact-messages.php');
    exit();
}

function formatDateTime($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y H:i');
}
?>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">Yêu cầu hỗ trợ #<?php echo $message['id']; ?></h1>
        <a href="admin-contact-messages.php" class="btn-admin-secondary">← Quay lại</a>
    </div>

    <div class="card">
        <h2>Thông tin yêu cầu</h2>
        <table class="info-table">
            <tr>
                <td>Họ và tên</td>
                <td><?php echo htmlspecialchars($message['fullname']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>
                    <?php echo htmlspecialchars($message['email']); ?>
                </td>
            </tr>
            <tr>
                <td>Chủ đề</td>
                <td><?php echo htmlspecialchars($message['subject']); ?></td>
            </tr>
            <tr>
                <td>Ngày gửi</td>
                <td><?php echo formatDateTime($message['created_at']); ?></td>
            </tr>
            <?php if (!empty($message['phone'])): ?>
            <tr>
                <td>Số điện thoại</td>
                <td><?php echo htmlspecialchars($message['phone']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="card">
        <h2>Nội dung chi tiết</h2>
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
        </div>
    </div>

    <div class="card">
        <h2>Hành động</h2>
        <div class="flex-wrap">
            <a href="admin-contact-messages.php?delete=<?php echo $message['id']; ?>" class="btn-delete btn-sm" onclick="return confirm('Bạn có chắc muốn xóa yêu cầu hỗ trợ này?')">Xóa yêu cầu</a>
            <a href="admin-contact-messages.php" class="btn-admin-secondary btn-sm">Danh sách yêu cầu</a>
        </div>
    </div>
</div>

<?php include 'admin-footer.php'; ?>

