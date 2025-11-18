<?php
/**
 * Liên hệ - Medicare
 * Trang liên hệ với Medicare
 */

$pageTitle = 'Liên hệ';
require_once 'config.php';
include 'header.php';

// Lấy thông báo từ URL
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="page contact-page">
    <section class="page-hero">
        <h1>Liên hệ với chúng tôi</h1>
        <p>
            Đội ngũ Medicare luôn sẵn sàng hỗ trợ bạn 24/7. Hãy liên hệ khi cần
            tư vấn hoặc hỗ trợ đặt lịch khám.
        </p>
    </section>

    <?php if ($success == '1'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #c3e6cb;">
            Gửi thành công! Chúng tôi sẽ liên hệ lại với bạn sớm nhất có thể.
        </div>
    <?php endif; ?>

    <?php if ($error == 'empty'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #fcc;">
            Vui lòng điền đầy đủ thông tin.
        </div>
    <?php elseif ($error == 'send_failed'): ?>
        <div style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin: 20px auto; max-width: 800px; border: 1px solid #fcc;">
            Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.
        </div>
    <?php endif; ?>

    <section class="contact-wrapper">
        <div class="contact-info">
            <div class="contact-info-grid">
                <article class="contact-info-card">
                    <h3>Địa chỉ</h3>
                    <p>123 Đường Sức Khỏe, Quận 1, TP.HCM</p>
                </article>
                <article class="contact-info-card">
                    <h3>Hotline</h3>
                    <p>1900 6767 (24/7)</p>
                </article>
                <article class="contact-info-card">
                    <h3>Email hỗ trợ</h3>
                    <p>support@medicare.vn</p>
                </article>
                <article class="contact-info-card">
                    <h3>Thời gian làm việc</h3>
                    <p>Thứ 2 - Chủ nhật: 7:00 - 21:00</p>
                </article>
            </div>
        </div>

        <div class="contact-form-card">
            <h2>Gửi phản hồi</h2>
            <p>Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại nhanh nhất.</p>
            <form class="contact-form" action="contact_process.php" method="POST">
                <div class="form-group">
                    <label for="contact-name">Họ và tên</label>
                    <input
                        type="text"
                        id="contact-name"
                        name="fullname"
                        placeholder="Nhập họ và tên"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="contact-email">Email</label>
                    <input
                        type="email"
                        id="contact-email"
                        name="email"
                        placeholder="Nhập email"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="contact-subject">Chủ đề</label>
                    <input
                        type="text"
                        id="contact-subject"
                        name="subject"
                        placeholder="Nhập chủ đề"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="contact-message">Nội dung liên hệ</label>
                    <textarea
                        id="contact-message"
                        name="message"
                        rows="4"
                        placeholder="Nhập nội dung cần hỗ trợ"
                        required
                    ></textarea>
                </div>
                <button type="submit" class="btn-primary">Gửi phản hồi</button>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

