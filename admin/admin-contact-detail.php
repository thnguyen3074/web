<?php
// Admin Contact Detail - Chi tiết yêu cầu hỗ trợ

$pageTitle = 'Chi tiết yêu cầu hỗ trợ';
require_once '../config.php';
include 'admin-header.php';

// Lấy ID từ URL
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($message_id <= 0) {
    header('Location: admin-contact-messages.php');
    exit();
}

// Lấy thông tin yêu cầu hỗ trợ
$sql = "SELECT * FROM contact_messages WHERE id = $message_id";
$result = mysqli_query($conn, $sql);
$message = mysqli_fetch_assoc($result);

if (!$message) {
    header('Location: admin-contact-messages.php');
    exit();
}

// Format ngày
function formatDate($date) {
    $date_obj = new DateTime($date);
    return $date_obj->format('d/m/Y H:i:s');
}
?>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="page-title">Chi tiết yêu cầu hỗ trợ</h1>
        <div>
            <a href="admin-contact-messages.php" class="btn-admin-secondary" style="text-decoration: none; padding: 10px 20px; margin-right: 10px;">← Quay lại</a>
            <a href="admin-contact-messages.php?delete=<?php echo $message['id']; ?>" class="btn-delete" style="text-decoration: none; padding: 10px 20px;" onclick="return confirm('Bạn có chắc muốn xóa yêu cầu hỗ trợ này?')">Xóa yêu cầu</a>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">ID</label>
                <p style="margin: 0; font-size: 16px; color: #333;">#<?php echo $message['id']; ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">Ngày gửi</label>
                <p style="margin: 0; font-size: 16px; color: #333;"><?php echo formatDate($message['created_at']); ?></p>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">Họ và tên</label>
            <p style="margin: 0; font-size: 16px; color: #333; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                <?php echo htmlspecialchars($message['fullname']); ?>
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">Email</label>
            <p style="margin: 0; font-size: 16px; color: #333; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" style="color: #007bff; text-decoration: none;">
                    <?php echo htmlspecialchars($message['email']); ?>
                </a>
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">Chủ đề</label>
            <p style="margin: 0; font-size: 16px; color: #333; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                <?php echo htmlspecialchars($message['subject']); ?>
            </p>
        </div>

         <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #666; margin-bottom: 5px; font-size: 14px;">Nội dung</label>
            <div style="margin: 0; font-size: 16px; color: #333; padding: 15px; background: #f8f9fa; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
            </div>
        </div>
    </div>
</div>

<?php include 'admin-footer.php'; ?>
