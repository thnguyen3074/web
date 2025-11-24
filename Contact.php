<?php
// Liên hệ - Trang liên hệ với Medicare

$pageTitle = 'Liên hệ';
require_once 'config.php';
include 'header.php';

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
        <div class="alert alert-success">
            Gửi thành công! Chúng tôi sẽ liên hệ lại với bạn sớm nhất có thể.
        </div>
    <?php endif; ?>

    <?php if ($error == 'empty'): ?>
        <div class="alert alert-error">
            Vui lòng điền đầy đủ thông tin.
        </div>
    <?php elseif ($error == 'send_failed'): ?>
        <div class="alert alert-error">
            Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.
        </div>
    <?php endif; ?>

    <section class="contact-wrapper">
        <div class="contact-info">
            <div class="contact-info-grid">
                <article class="contact-info-card">
                    <h3>Địa chỉ văn phòng</h3>
                    <p>70 Đường Tô Ký, Phường Tân Chánh Hiệp, Quận 12, TP.HCM</p>
                    <p>Văn phòng làm việc: Thứ 2 - Thứ 6, 8:00 - 17:00</p>
                </article>
                <article class="contact-info-card">
                    <h3>Hotline hỗ trợ</h3>
                    <p><b>1900 6767</b></p>
                    <p>Hỗ trợ 24/7, miễn phí cuộc gọi</p>
                </article>
                <article class="contact-info-card">
                    <h3>Email liên hệ</h3>
                    <p><b>support@medicare.vn</b></p>
                    <p>Phản hồi trong vòng 24 giờ</p>
                </article>
                <article class="contact-info-card">
                    <h3>Thời gian hỗ trợ</h3>
                    <p>Thứ 2 - Chủ nhật: 7:00 - 21:00</p>
                    <p>Hotline hoạt động 24/7</p>
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

