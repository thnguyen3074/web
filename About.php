<?php
/**
 * Giới thiệu - Medicare
 * Trang giới thiệu về Medicare
 */

$pageTitle = 'Giới thiệu';
require_once 'config.php';
include 'header.php';
?>

<main class="page about-page">
    <section class="about-hero">
        <div class="about-hero-text">
            <h1>Giới thiệu về Medicare</h1>
            <p>
                Medicare là nền tảng đặt lịch khám trực tuyến giúp kết nối người
                bệnh với hệ thống bệnh viện, phòng khám và chuyên gia y tế trên cả
                nước. Chúng tôi mang đến trải nghiệm đặt lịch minh bạch, tiện lợi
                và đáng tin cậy cho mọi gia đình.
            </p>
            <div class="about-stats">
                <div>
                    <strong>500+</strong>
                    <span>Cơ sở y tế liên kết</span>
                </div>
                <div>
                    <strong>1.200+</strong>
                    <span>Bác sĩ đối tác</span>
                </div>
                <div>
                    <strong>50.000+</strong>
                    <span>Lịch khám đã đặt</span>
                </div>
            </div>
        </div>
        <div class="about-hero-media">
            <img
                src="images/facilities/default.jpg"
                alt="Medicare platform"
            />
        </div>
    </section>

    <section class="about-grid">
        <article class="about-card">
            <h3>Sứ mệnh</h3>
            <p>
                Xây dựng hệ sinh thái chăm sóc sức khỏe số, giúp người dân tiếp cận
                dịch vụ y tế nhanh chóng và chính xác.
            </p>
        </article>
        <article class="about-card">
            <h3>Tầm nhìn</h3>
            <p>
                Trở thành nền tảng đặt lịch khám trực tuyến hàng đầu Việt Nam, đồng
                hành cùng quá trình chuyển đổi số trong ngành y tế.
            </p>
        </article>
        <article class="about-card">
            <h3>Giá trị cốt lõi</h3>
            <ul>
                <li>Chính xác và minh bạch</li>
                <li>Lấy bệnh nhân làm trung tâm</li>
                <li>Bảo mật dữ liệu tuyệt đối</li>
            </ul>
        </article>
        <article class="about-card">
            <h3>Tại sao chọn Medicare?</h3>
            <ul>
                <li>Đặt lịch nhanh chóng trong vài phút</li>
                <li>Thông tin bác sĩ, chuyên khoa rõ ràng</li>
                <li>Hỗ trợ 24/7 với đội ngũ tận tâm</li>
            </ul>
        </article>
    </section>
</main>

<?php include 'footer.php'; ?>

