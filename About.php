<?php
// Giới thiệu - Trang giới thiệu về Medicare

$pageTitle = 'Giới thiệu';
require_once 'config.php';
include 'header.php';

$total_facilities = 0;
$sql_facilities = "SELECT COUNT(*) as total FROM facilities";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    $row = mysqli_fetch_assoc($result_facilities);
    $total_facilities = $row['total'];
}

$total_appointments = 0;
$sql_appointments = "SELECT COUNT(*) as total FROM appointments";
$result_appointments = mysqli_query($conn, $sql_appointments);
if ($result_appointments) {
    $row = mysqli_fetch_assoc($result_appointments);
    $total_appointments = $row['total'];
}

function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}
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
            <p>
                Với hệ thống tìm kiếm thông minh, bạn có thể dễ dàng tìm kiếm bệnh viện, 
                phòng khám theo tên, địa chỉ hoặc chuyên khoa. Quy trình đặt lịch đơn giản 
                chỉ trong vài bước, giúp tiết kiệm thời gian và công sức.
            </p>
            <div class="about-stats">
                <div>
                    <strong><?php echo formatNumber($total_facilities); ?></strong>
                    <span>Cơ sở y tế liên kết</span>
                </div>
                <div>
                    <strong><?php echo formatNumber($total_appointments); ?></strong>
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
                dịch vụ y tế nhanh chóng và chính xác. Chúng tôi cam kết mang đến 
                giải pháp đặt lịch khám hiện đại, tiện lợi cho mọi người dân Việt Nam.
            </p>
        </article>
        <article class="about-card">
            <h3>Tầm nhìn</h3>
            <p>
                Trở thành nền tảng đặt lịch khám trực tuyến hàng đầu Việt Nam, đồng
                hành cùng quá trình chuyển đổi số trong ngành y tế. Mở rộng kết nối 
                với hàng nghìn cơ sở y tế trên toàn quốc.
            </p>
        </article>
        <article class="about-card">
            <h3>Giá trị cốt lõi</h3>
            <ul>
                <li>Chính xác và minh bạch trong mọi thông tin</li>
                <li>Lấy bệnh nhân làm trung tâm của mọi dịch vụ</li>
                <li>Bảo mật dữ liệu tuyệt đối và an toàn</li>
                <li>Tiện lợi và nhanh chóng trong mọi thao tác</li>
            </ul>
        </article>
        <article class="about-card">
            <h3>Tại sao chọn Medicare?</h3>
            <ul>
                <li>Đặt lịch nhanh chóng trong vài phút, không cần chờ đợi</li>
                <li>Thông tin bệnh viện, phòng khám, chuyên khoa rõ ràng và đầy đủ</li>
                <li>Hỗ trợ 24/7 với đội ngũ tận tâm và chuyên nghiệp</li>
                <li>Miễn phí đặt lịch, không phát sinh chi phí ẩn</li>
            </ul>
        </article>
        <article class="about-card">
            <h3>Dịch vụ của chúng tôi</h3>
            <ul>
                <li>Tìm kiếm bệnh viện, phòng khám theo nhiều tiêu chí</li>
                <li>Xem thông tin chi tiết về cơ sở y tế và chuyên khoa</li>
                <li>Đặt lịch khám trực tuyến nhanh chóng và dễ dàng</li>
                <li>Quản lý lịch hẹn của bạn một cách tiện lợi</li>
            </ul>
        </article>
        <article class="about-card">
            <h3>Quy trình đặt lịch</h3>
            <ol>
                <li>Tìm kiếm bệnh viện hoặc phòng khám phù hợp</li>
                <li>Xem thông tin chi tiết và chọn thời gian khám</li>
                <li>Điền thông tin cá nhân và triệu chứng</li>
                <li>Xác nhận và hoàn tất đặt lịch</li>
            </ol>
        </article>
    </section>
</main>

<?php include 'footer.php'; ?>

