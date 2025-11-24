-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 24, 2025 lúc 10:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `medicare_db`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `create_appointment` (IN `p_user_id` INT, IN `p_facility_id` INT, IN `p_specialty_id` INT, IN `p_appointment_date` DATE, IN `p_appointment_time` TIME, IN `p_symptoms` TEXT, OUT `p_appointment_id` INT, OUT `p_result_message` VARCHAR(255))   BEGIN
    DECLARE v_conflict_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Lỗi hệ thống khi tạo lịch hẹn';
        SET p_appointment_id = NULL;
    END;

    START TRANSACTION;

    -- Kiểm tra trùng giờ hẹn tại cùng cơ sở y tế
    SELECT COUNT(*) INTO v_conflict_count
    FROM appointments
    WHERE facility_id = p_facility_id
      AND appointment_date = p_appointment_date
      AND appointment_time = p_appointment_time
      AND status IN ('pending', 'confirmed');

    IF v_conflict_count > 0 THEN
        SET p_result_message = 'Giờ hẹn đã được đặt. Vui lòng chọn giờ khác.';
        SET p_appointment_id = NULL;
        ROLLBACK;
    ELSE
        -- Tạo lịch hẹn mới
        INSERT INTO appointments (
            user_id,
            facility_id,
            specialty_id,
            appointment_date,
            appointment_time,
            symptoms,
            status
        ) VALUES (
            p_user_id,
            p_facility_id,
            p_specialty_id,
            p_appointment_date,
            p_appointment_time,
            p_symptoms,
            'pending'
        );

        SET p_appointment_id = LAST_INSERT_ID();
        SET p_result_message = 'Đặt lịch thành công!';
        COMMIT;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_appointment_status_proc` (IN `p_appointment_id` INT, IN `p_status` VARCHAR(20), OUT `p_result_message` VARCHAR(255))   BEGIN
    DECLARE v_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO v_exists
    FROM appointments
    WHERE appointment_id = p_appointment_id;

    IF v_exists = 0 THEN
        SET p_result_message = 'Không tìm thấy lịch hẹn';
    ELSE
        UPDATE appointments
        SET status = p_status
        WHERE appointment_id = p_appointment_id;
        
        SET p_result_message = 'Cập nhật trạng thái thành công';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`admin_id`, `email`, `password`, `name`, `created_at`) VALUES
(1, 'admin@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Administrator', '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `patient_email` varchar(100) DEFAULT NULL,
  `patient_phone` varchar(20) DEFAULT NULL,
  `facility_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `symptoms` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `patient_name`, `patient_email`, `patient_phone`, `facility_id`, `specialty_id`, `appointment_date`, `appointment_time`, `symptoms`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nguyễn Văn A', 'nguyenvana@email.com', '0901234567', 1, 1, '2025-10-05', '08:00:00', 'Đau ngực, khó thở', 'confirmed', '2025-10-01 07:00:00', '2025-10-01 07:00:00'),
(2, 2, 'Trần Thị B', 'tranthib@email.com', '0987654321', 2, 11, '2025-10-10', '10:30:00', 'Tầm soát ung thư định kỳ', 'completed', '2025-10-02 08:00:00', '2025-10-10 11:30:00'),
(3, 3, 'Lê Văn C', 'levanc@email.com', '0912345678', 3, 3, '2025-10-15', '09:00:00', 'Sốt, ho kéo dài', 'confirmed', '2025-10-03 09:00:00', '2025-10-03 09:00:00'),
(4, 4, 'Phạm Thị D', 'phamthid@email.com', '0923456789', 4, 9, '2025-10-20', '14:00:00', 'Khám thai định kỳ', 'pending', '2025-10-04 10:00:00', '2025-10-04 10:00:00'),
(5, 5, 'Hoàng Văn E', 'hoangvane@email.com', '0934567890', 5, 13, '2025-10-25', '15:30:00', 'Đau lưng, mỏi gối', 'confirmed', '2025-10-05 11:00:00', '2025-10-05 11:00:00'),
(6, 6, 'Võ Thị F', 'vothif@email.com', '0945678901', 6, 4, '2025-11-01', '07:30:00', 'Kiểm tra sức khỏe tổng quát', 'confirmed', '2025-10-15 12:00:00', '2025-10-15 12:00:00'),
(7, 7, 'Đặng Văn G', 'dangvang@email.com', '0956789012', 7, 5, '2025-11-10', '11:00:00', 'Viêm xoang mãn tính', 'pending', '2025-10-20 13:00:00', '2025-10-20 13:00:00'),
(8, 8, 'Bùi Thị H', 'buithih@email.com', '0967890123', 8, 2, '2025-11-18', '16:00:00', 'Nổi mẩn đỏ, ngứa da', 'completed', '2025-10-25 14:00:00', '2025-11-18 17:00:00'),
(9, 9, 'Ngô Văn I', 'ngovani@email.com', '0978901234', 9, 10, '2025-11-25', '08:45:00', 'Chấn thương đầu gối khi chơi thể thao', 'confirmed', '2025-11-01 15:00:00', '2025-11-01 15:00:00'),
(10, 10, 'Lý Thị K', 'lythik@email.com', '0989012345', 10, 1, '2025-11-30', '14:30:00', 'Tái khám tim mạch', 'pending', '2025-11-10 16:00:00', '2025-11-10 16:00:00');

--
-- Bẫy `appointments`
--
DELIMITER $$
CREATE TRIGGER `update_appointment_status` BEFORE UPDATE ON `appointments` FOR EACH ROW BEGIN
    IF OLD.status != NEW.status THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `appointment_details`
-- (See below for the actual view)
--
CREATE TABLE `appointment_details` (
`appointment_id` int(11)
,`appointment_date` date
,`appointment_time` time
,`symptoms` text
,`status` enum('pending','confirmed','completed','canceled')
,`created_at` timestamp
,`user_id` int(11)
,`patient_name` varchar(100)
,`patient_email` varchar(100)
,`patient_phone` varchar(20)
,`facility_id` int(11)
,`facility_name` varchar(200)
,`facility_type` enum('hospital','clinic')
,`facility_address` text
,`facility_phone` varchar(20)
,`specialty_id` int(11)
,`specialty_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `facilities`
--

CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('hospital','clinic') NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `working_hours` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `facilities`
--

INSERT INTO `facilities` (`facility_id`, `name`, `type`, `address`, `phone`, `working_hours`, `description`, `image`, `created_at`) VALUES
(1, 'Bệnh viện Việt-Đức', 'hospital', '40 Tràng Thi, Hoàn Kiếm, Hà Nội', '(024) 3825 3531', '07:00-16:00', '\"Bệnh viện Việt Đức là một trong 5 bệnh viện tuyến Trung ương, hạng đặc biệt của Việt Nam. Bệnh viện có lịch sử trên 100 năm, bề dày truyền thống danh tiếng, là cái nôi của ngành ngoại khoa Việt Nam gắn liền với những thành tựu Y học quan trọng của đất nước. \r\n\r\nViệt Đức là địa chỉ uy tín hàng đầu về ngoại khoa, tiến hành khám bệnh, chữa bệnh và thực hiện các kỹ thuật chụp chiếu, xét nghiệm, thăm dò chức năng cơ bản và chuyên sâu hàng ngày cho người dân. \r\n\r\nBệnh viện có đội ngũ y bác sĩ hùng hậu, nhiều người kiêm là cán bộ giảng dạy tại Đại học Y khoa Hà Nội hoặc Khoa Y Dược - Đại học Quốc gia Hà Nội. Trong số họ nhiều người là chuyên gia đầu ngành và bác sĩ giàu kinh nghiệm ở các chuyên khoa khác nhau. \"\r\n', 'images/facilities/facility_692411f08e31f1.00124338.jpg', '2025-11-24 08:06:08'),
(2, 'Bệnh Viện K', 'hospital', ' 43 P. Quán Sứ, Hàng Bông, Hoàn Kiếm, Hà Nội ', ' 0904 690 818', '\"thứ 2- thứ 6: 06:00 - 12:00 / 13:00 - 15:00\"', '\"Ngày 17 tháng 7 năm 1969, được sự đồng ý của Chính phủ, Bộ trưởng Bộ Y tế ra quyết định số 711/QĐ-BYT thành lập Bệnh viện K được thành lập từ tiền thân là Viện Curie Đông Dương (Insitut Curie de L’Indochine) ra đời tại Hà Nội vào ngày 19/10/1923 do Luật sư Mourlan phụ trách.. Bệnh viện với 3 cơ sở khang trang, sạch sẽ và nhiều trang thiết bị hiện đại sánh ngang tầm với các quốc gia trong khu vực. Hiện nay, 3 cơ sở khám chữa bệnh trên địa bàn Hà Nội đó là:\r\n\r\nCơ sở 1: Số 43 Quán Sứ, Hoàn Kiếm, Hà Nội. \r\nCơ sở 2: Tựu Liệt, Tam Hiệp, Thanh Trì, Hà Nội.\r\nCơ sở 3: cơ sở Tân Triều, số 30 Cầu Bươu, Thanh Trì, Hà Nội.\r\nBệnh viện hiện có 85 viện, trung tâm, khoa, phòng, bộ phận trực thuộc với hơn 1.700 cán bộ, người lao động. \"\r\n', 'images/facilities/facility_69241348209982.63007218.png', '2025-11-24 08:11:52'),
(3, 'Bệnh viện Nhi Đồng 2', 'hospital', '14 Lý Tự Trọng, Bến Nghé, Quận 1, Thành phố Hồ Chí Minh', '02838295723', 'Thứ 2-6: 7h00-16h30', '\"Bệnh viện Nhi Đồng 2 tọa lạc trên một khu đất cao với diện tích 8,6 hecta, giáp 4 mặt tiền đường: Lý Tự Trọng, Chu Mạnh Trinh, Nguyễn Du và Hai Bà Trưng (Q.1). Bệnh viện có vị trí đẹp và gắn liền với quá trình phát triển của thành phố.\r\n\r\nĐược thành lập từ ngày 01/06/1978; Bệnh viện Nhi đồng 2 là bệnh viện chuyên khoa Nhi – hạng 1, trực thuộc Sở Y tế TP.HCM. Đồng thời, cũng là một trong 4 bệnh viện Nhi hàng đầu tại Việt Nam phụ trách công tác khám, chữa bệnh cho các bé từ 0 đến dưới 16 tuổi. Bệnh viện có 1.400 giường bệnh, được xây dựng trong khuôn viên 8,6 hecta, nhiều cây xanh, thoáng mát, sân chơi rộng rãi, thân thiện với trẻ em…\r\n\r\nHệ thống trang thiết bị y tế hiện đại cùng với đội ngũ các bác sĩ giỏi chuyên môn, giàu y đức đã đáp ứng tốt nhu cầu chăm sóc sức khỏe cho các bệnh nhi. Bên cạnh đó, bệnh viện còn là trung tâm đào tạo thực hành cho các trường: Đại học Y Dược TP.HCM, Đại học Y khoa Phạm Ngọc Thạch, Đại học Nguyễn Tất Thành… ; Là cơ sở đào tạo y khoa liên tục do Bộ Y tế cấp mã đào tạo. Ngoài ra, bệnh viện cũng là nơi tiếp nhận sinh viên quốc tế đến tham quan và học tập về chuyên ngành nhi khoa.\r\n\r\nBệnh viện cũng là một trung tâm hợp tác nghiên cứu lâm sàng với các viện, bệnh viện đầu ngành trong cả nước, Tổ chức Y tế thế giới, các trường đại học và viện nghiên cứu của các nước phát triển…\r\n\r\nBệnh viện có 10 phòng chức năng, 38 khoa lâm sàng và cận lâm sàng với đầy đủ các chuyên khoa (đặc biệt là khoa Ngoại Thần kinh, vật lý trị liệu, khoa Tâm lý, khoa Sức khỏe trẻ em…).\r\n\r\nTừ năm 2004, Bệnh viện đã triển khai thực hiện phẫu thuật ghép thận và ghép gan. Năm 2010, triển khai đơn vị phẫu thuật tim hở và thông tim can thiệp. Bệnh viện Nhi đồng 2 là cơ sở nhi khoa duy nhất trong cả nước có phẫu thuật chấn thương sọ não và bệnh lý ngoại thần kinh nhi khoa…\r\n\r\nBan Giám đốc gồm 01 Bác sĩ Giám đốc và 03 Bác sĩ Phó Giám đốc.\r\n\r\nKhu điều trị ngoại trú bao gồm: khoa Khám bệnh, Cấp cứu, Sức khỏe trẻ em, khoa Tâm lý - phòng khám theo yêu cầu Chất lượng cao có thể đáp ứng 5.000 đến 6.000 lượt khám mỗi ngày. Đội ngũ nhân viên y tế với phong cách, thái độ phục vụ luôn hướng tới sự hài lòng người bệnh.\r\n\r\nVới tiêu chí lấy người bệnh làm trung tâm và chất lượng khám, chữa bệnh là ưu tiên hàng đầu cùng với sự quyết tâm của toàn thể cán bộ nhân viên y tế. Bệnh viện Nhi Đồng 2 sẽ phấn đấu hoàn thành xuất sắc nhiệm vụ chăm sóc sức khỏe nhân dân đồng thời sẽ phát triển bệnh viện trở thành bệnh viện chuyên khoa nhi hiện đại ngang tầm các nước trong khu vực.\"\r\n', 'images/facilities/facility_6924158aa39054.96306085.png', '2025-11-24 08:21:30'),
(4, 'Bệnh viện Hồng Ngọc', 'hospital', 'Bệnh viện Đa khoa Hồng Ngọc Yên Ninh: Số 55 Yên Ninh, Phường Ba Đình, Hà Nội', '(024) 3927 5568', 'Thứ 2-Chủ Nhật: 7h30-17h', '\"Hệ thống Bệnh viện Đa khoa Hồng Ngọc được xây dựng với mô hình bệnh viện – khách sạn tiên phong ở Hà Nội cũng như toàn khu vực miền Bắc. Đến nay, trải qua gần 20 năm xây dựng và phát triển, Hồng Ngọc đã trở thành thương hiệu quen thuộc và là địa chỉ y tế đáng tin cậy của hàng triệu bệnh nhân.\r\n\r\nBệnh viện Đa khoa Hồng Ngọc hiện nay có gần 500 giường bệnh; 25 khoa phòng cùng đội ngũ 200 bác sĩ; 430 y tá, điều dưỡng và kỹ thuật viên. Tất cả đều có chuyên môn cao, giàu kinh nghiệm, đáp ứng nhu cầu khám chữa bệnh khắt khe của toàn thể khách hàng. Đặc biệt, để đáp ứng nhu cầu thăm khám ngày càng tăng cao của người dân thủ đô cũng như của các tỉnh thành lân cận, Bệnh viện Đa khoa Hồng Ngọc đã xây dựng thêm 5 phòng khám vệ tinh cùng 1 bệnh viên mới 16 tầng hiện đại, cung cấp các dịch vụ y tế chất lượng cao, gồm:\r\n\r\nBệnh viện Đa khoa Hồng Ngọc Yên Ninh\r\nPhòng khám Đa khoa Hồng Ngọc Keangnam\r\nPhòng khám Đa khoa Hồng Ngọc Savico\r\nPhòng khám Đa khoa Hồng Ngọc – Nguyễn Tuân\r\nPhòng khám Đa khoa Hồng Ngọc – Tố Hữu\r\nPhòng khám Đa khoa Hồng Ngọc Tây Hồ\r\nBệnh viện Đa khoa Hồng Ngọc Phúc Trường Minh\r\nCác bác sĩ làm việc tại Bệnh viện Hồng Ngọc đều có thâm niên công tác tại Bệnh viện Bạch Mai, Phụ sản Trung ương, Phụ sản Hà Nội, Nhi… đảm bảo mang tới sự an tâm cho mỗi bệnh nhân khi tới thăm khám và điều trị:\r\n\r\nThầy thuốc ưu tú, Tiến sĩ, Bác sĩ Lê Thiện Thái - 40 năm kinh nghiệm khám, điều trị và phẫu thuật các bệnh lý Sản Phụ Khoa; từng giữ chức vụ Phó Giám đốc Bệnh viện Phụ sản Trung ương, Trưởng khoa Đẻ – Bệnh viện Phụ sản Trung Ương\r\nThạc sĩ, Bác sĩ nội trú Lê Thị Vân Anh - Gần 30 năm kinh nghiệm trong lĩnh vực Tiêu hóa - Gan - Mật; hiện là Trưởng khoa tại khoa Tiêu hóa Bệnh viện Hồng Ngọc \r\nThầy thuốc Nhân dân, Phó Giáo sư, Tiến sĩ Nguyễn Xuân Hùng - Giám đốc Trung tâm Phẫu thuật đại trực tràng - Tầng sinh môn, Bệnh viện Hữu nghị Việt Đức; giảng viên kiêm nhiệm Bộ môn Ngoại, Trường Đại học Y Hà Nội\r\nTiến sĩ, Bác sĩ chuyên khoa II Đinh Đăng Hòe - Hơn 40 năm kinh nghiệm trong lĩnh vực Tâm lý và Sức khỏe tâm thần; nguyên Giảng viên Đại hoc Y Hà Nội\r\nThạc sĩ, Bác sĩ nội trú Hà Lương Yên - Hơn 25 năm kinh nghiệm trong lĩnh vực Nội tiết và Đái tháo đường; từng công tác nhiều năm tại Khoa Nội tiết – Đái tháo đường thuộc Bệnh viện Bạch Mai \"\r\n', 'images/facilities/facility_692415c0eb3785.97297578.png', '2025-11-24 08:22:24'),
(5, 'Bệnh viện Y Học Cổ Truyền TP.HCM', 'hospital', '179-187 Nam Kỳ Khởi Nghĩa, Phường Võ Thị Sáu, Quận 3, Thành phố Hồ Chí Minh', '19002805', '\"Thứ 2 - Thứ 7: 7h - 19h Chủ nhật: 7h - 11h30\"', '\"Bệnh viện Y Học Cổ Truyền TPHCM là bệnh viện đầu ngành khu vực phía Nam về Y học cổ truyền, do sở Y tế quản lý. Qua quá trình xây dựng và phát triển gần 40 năm, bệnh viện đã có nhiều thành tựu về công tác phát triển nền Y học cổ truyền nước nhà. \r\n\r\nTrước năm 1975, Bệnh viện Y Học Cổ Truyền TPHCM là nhà bảo sanh của Bộ trưởng Bộ Y tế chế độ cũ với 30 giường nội trú. Sau ngày miền Nam giải phóng 30/4/1975 được Nhà nước Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam tiếp quản đặt tên là “Bệnh viện Đông Y miền Nam” do Bộ Y tế quản lý. - Năm 1979, Bộ Y tế chuyển giao cho Sở Y tế Tp.HCM quản lý và xây dựng phát triển thành bệnh viện chuyên khoa hạng II đầu ngành chuyên sâu về Y học cổ truyền, kết hợp Y học cổ truyền với Y học hiện đại trong điều trị - nghiên cứu khoa học và đào tạo với tên gọi “BỆNH VIỆN Y HỌC DÂN TỘC”. \r\n\r\nNăm 1999, bệnh viện được thành lập theo Quyết định số 4019/QĐ-UB-VX ngày 14 tháng 7 năm 1999 của Ủy ban nhân dân TPHCM về việc cho phép đổi tên Bệnh viện Y học dân tộc thành Bệnh viện Y học cổ truyền trực thuộc Sở Y tế TPHCM và quy định chức năng, nhiệm vụ, bộ máy tổ chức của Bệnh viện Y Học Cổ Truyền TPHCM; Quyết định 1549/QĐ-SYT ngày 27 tháng 8 năm 2013 của Sở Y tế TPHCM về việc ban hành quy chế tổ chức và hoạt động của bệnh viện Y Học Cổ Truyền thuộc Sở Y tế. \r\n\r\nBệnh viện Y Học Cổ Truyền TPHCM là bệnh viện chuyên khoa đầu ngành về YHCT của thành phố và là bệnh viện tuyến cuối về YHCT ở các tỉnh phía Nam. Bệnh viện nhận khám và điều trị cho cán bộ, nhân dân của thành phố và tỉnh khu vực phía Nam. Bệnh viện Y học cổ truyền là bệnh viện chuyên khoa hạng 2 với 250 giường nội trú, là đơn vị sự nghiệp y tế trực thuộc Sở Y tế TPHCM. Bệnh viện có nhiệm vụ chỉ đạo tuyến về chuyên môn YHCT cho các bệnh viện đa khoa trong thành phố. Bệnh viện có đội ngũ thầy thuốc có trình độ chuyên môn và kinh nghiệm cao về YHCT, được trang bị trang thiết bị y tế hiện đại, chuyên khoa sâu.\r\n\r\nBên cạnh công tác khám, chữa bệnh, bệnh viện Y học cổ truyền được phép tổ chức đào tạo, bồi dưỡng chuyên môn, nghiệp vụ thuộc các lĩnh vực chuyên ngành Y học cổ truyền. Bệnh viện còn là cơ sở thực hành của các trường Đại học Y Dược TPHCM, Đại học Y khoa Phạm Ngọc Thạch, khoa Y Đại học Quốc gia TpHCM, Trung học y Lê Hữu Trác… Bệnh viện Y Học Cổ Truyền thành phố Hồ Chí Minh đã và đang là một nơi đáng tin cậy, ngày càng được người bệnh tin yêu. Đáp lại tấm chân tình ấy, Cấp ủy Đảng, Ban Lãnh đạo bệnh viện cùng toàn thể công chức, viên chức, người lao động trên mọi lĩnh vực của bệnh viện sẽ không ngừng nổ lực, phấn đấu nâng cao chất lượng khám chữa bệnh, chất lượng toàn diện của bệnh viện vì mục tiêu góp phần xây dựng nền Y học Việt Nam hiện đại, khoa học, dân tộc và đại chúng. \r\n\r\nHướng tới, bệnh viện tiếp tục xây dựng và phát triển thành bệnh viện chuyên khoa hạng I đầu ngành chuyên sâu về Y học cổ truyền, không ngừng phấn đấu để được trao tặng Huân chương lao động Hạng Nhất.\"\r\n', 'images/facilities/facility_692415f67d7499.17772606.png', '2025-11-24 08:23:18'),
(6, 'Bệnh viện Bình Dân', 'hospital', '371 Điện Biên Phủ, Phường 4, Quận 3, Thành phố Hồ Chí Minh', '02838394747', 'Thứ Hai- Thứ 7: 06:00–11:30,13:00–16:00', '\"Thành lập từ năm 1954, Bệnh viện Bình Dân là chiếc nôi của ngành ngoại khoa của TP. Hồ Chí Minh và các tỉnh thành phía Nam.\r\n\r\nVới 1.200 giường bệnh, mỗi năm bệnh viện tiếp nhận hơn 610.000 lượt khám ngoại trú và hơn 71.000 lượt điều trị nội trú. Số trường hợp phẫu thuật hàng năm trên 30.000 người bệnh, trong đó có 670 trường hợp phẫu thuật robot, hơn 20.000 trường hợp phẫu thuật đặc biệt và loại I. Các chuyên ngành mũi nhọn của bệnh viện bao gồm Ngoại tổng quát, Ngoại lồng ngực và mạch máu, Ngoại Tiết niệu - Nam khoa và Gây mê hồi sức. Ngoài ra bệnh viện còn phát triển các chuyên ngành hỗ trợ toàn diện cho ngoại khoa như Ung bướu, Dinh dưỡng, Nội thận - Lọc máu, Sinh học phân tử,...\r\n\r\nVới bề dày truyền thống giảng dạy và hợp tác với các trường Y khoa hàng đầu, Bệnh viện Bình Dân là ngôi trường thực hành lâm sàng chất lượng của các thế hệ giảng viên và học viên y khoa trong hơn 70 năm qua. Với chiến lược phát triển nguồn nhân lực chất lượng cao, Bệnh viện Bình Dân luôn đẩy mạnh các hoạt động nghiên cứu khoa học, huấn luyện đào tạo, hợp tác quốc tế với các giáo sư và bệnh viện uy tín trên thế giới.\"\r\n', 'images/facilities/facility_6924163a35c5f1.77702616.png', '2025-11-24 08:24:26'),
(7, 'Bệnh viện Đa Khoa Gò Vấp', 'hospital', 'Đ. Quang Trung/641 Thông Tây Hội, Phường, Gò Vấp, Thành phố Hồ Chí Minh', '02835891799', 'Thứ Hai- Thứ 6: 07:00–11:00,13:30–16:00', 'Bệnh viện Đa Khoa Gò Vấp được thành lập từ ngày 23 tháng 02 năm 2007 theo Quyết định số 30/2007/QĐ-UBND ngày 23/02/2007 của Ủy ban Nhân dân thành phố Hồ Chí Minh, và chính thức hoạt động độc lập từ tháng 01 năm 2008.\r\n\r\nBệnh viện Đa Khoa Gò Vấp:\r\n\r\nCơ sở 1: Số 212 Lê Đức Thọ, Phường An Hội Đông, TP. Hồ Chí Minh \r\n\r\nCơ sở 2 (Trụ sở chính): 641 Quang Trung, Phường Thông Tây Hội, TP. Hồ Chí Minh.\r\n\r\nTháng 02 năm 2017, Bệnh viện Đa Khoa Gò Vấp khánh thành cơ sở 2 tại số 641 Quang Trung, Phường Thông Tây Hội, diện tích khuôn viên 13.340m2\r\n\r\nBệnh viện hoạt động chủ yếu tại cơ sở mới nhưng vẫn duy trì hoạt động Phòng khám Y học cổ truyền tại cơ sở 1. Với cơ sở vật chất mới là điều kiện thuận lợi cho Bệnh viện trong hoạt động khám chữa bệnh và chăm sóc sức khỏe nhân dân trong năm 2019, nhưng cũng đặt ra những thách thức mới, cơ hội phát triển mới mà Bệnh viện cần phải định hướng, khai thác, tập trung cho giai đoạn 2020 - 2025 và những năm tiếp theo.\r\n', 'images/facilities/facility_692416ad31d5f9.07687898.jpg', '2025-11-24 08:26:21'),
(8, 'Bệnh viện Đa Khoa Củ Chi', 'hospital', '1307 Tỉnh Lộ 7, ấp Chợ Cũ, Củ Chi, Thành phố Hồ Chí Minh 700000, Việt Nam', ' 19002805', 'Thứ 2 - Thứ 6: 07:00 - 16:00 Thứ 7 - Chủ Nhật: 07:00 - 11:00', 'Là bệnh viện đa khoa hạng II, Bệnh viện Đa Khoa Củ Chi chuyên thăm khám, điều trị đa chuyên khoa với hơn 1000 lượt khám ngoại trú mỗi ngày. Vì thế, bệnh viện khuyến khích người dân đặt khám online trước khi đến để hạn chế thời gian chờ đợi, đồng thời giảm tình trạng quá tải cho bệnh viện. Bệnh viện Đa Khoa Củ Chi được đầu tư nhiều máy móc, trang thiết bị hiện đại như: máy CT, X- Quang kỹ thuật số, siêu âm tim, máy nội soi, máy xét nghiệm miễn dịch, sinh hóa… cùng với việc nâng cấp, mở rộng phòng mổ và trang bị máy móc hiện đại cho phòng mổ như hệ thống mổ nội soi ổ bụng, máy C-arm.\r\n\r\nVới bước chuyển mình đáng kể đó, hiện nay Bệnh viện Đa Khoa Củ Chi đang từng bước khẳng định được năng lực của một bệnh viện đa khoa tuyến huyện cửa ngõ Tây Bắc của thành phố Hồ Chí Minh. Bằng chứng là không chỉ thực hiện khám và điều trị các bệnh lý đơn giản mà bệnh viện còn có thể thực hiện phẫu thuật can thiệp cho những trường hợp bệnh nặng, tổn thương phức tạp mà không cần phải chuyển lên tuyến trên, tạo được niềm tin trong người dân, là điểm đến tin cậy cho họ khi có vấn đề về sức khỏe nhất là ở một huyện ngoại thành là vùng sâu, vùng xa, vùng kháng chiến cũ của cách mạng.\r\n', 'images/facilities/facility_692416c6f0bd43.48186196.png', '2025-11-24 08:26:46'),
(9, 'Bệnh viện Đa khoa Quốc tế Nam Sài Gòn', 'hospital', '88 Đường Số 8, Khu dân cư Trung Sơn, Bình Chánh, Thành phố Hồ Chí Minh, Việt Nam', '(+84) 18006767', 'Thứ 2- Thứ 7: 07:00 - 12:00 /13:30 - 17:00', 'Bệnh viện Đa khoa Quốc tế Nam Sài Gòn là bệnh viện tư nhân và bắt đầu đi vào hoạt động từ cuối 2018.\r\n\r\nVới quy mô hơn 300 giường bệnh, được thiết kế, xây dựng hiện đại, hợp lý và thoáng mát với khuôn viên rộng rãi nhiều cây xanh, bệnh viện được trang bị những máy móc, trang thiết bị y tế đồng bộ, hiện đại và mới nhất hiện nay.\r\n\r\nĐội ngũ nhân viên y tế phục vụ tận tâm, yêu nghề cùng tập thể bác sĩ, chuyên gia giỏi chuyên môn, giàu y đức và đặc biệt với sự hỗ trợ hợp tác toàn diện của đội ngũ bác sĩ chuyên gia đầu ngành đến từ một số bệnh viện hàng đầu của khu vực TP.HCM như: Bệnh viện Chợ Rẫy, Bệnh viện Đại học Y Dược TP.HCM… Bệnh viện Đa khoa Quốc tế Nam Sài Gòn là địa chỉ đáng tin cậy để quý khách hàng, người bệnh lựa chọn khi có nhu cầu khám chữa bệnh.\r\n', 'images/facilities/facility_692416dc3eb629.13634053.png', '2025-11-24 08:27:08'),
(10, 'Bệnh viện An Bình', 'hospital', '146 Đ. An Bình, Phường An Đông, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', '(+84) 2839234359', 'Thứ Hai- Thứ Sáu: 07:00–11:30,13:00–16:00/Thứ Bảy,07:00–11:00', 'Bệnh viện An Bình được thành lập vào tháng 01/2001 với tên chính thức là Bệnh viện Đa Khoa An Bình. Bệnh viện toạ lạc trên một khu đất rộng 17.361m2.\r\n\r\nKhởi thủy của bệnh viện An Bình ngày nay là một ngôi chùa của đồng bào người Hoa, được xây cất từ năm 1892. Tại đây, vào năm 1885 đã có những hoạt động y tế nhân đạo: khám bệnh và điều trị miễn phí dựa trên nền tảng y học cổ truyền.\r\n\r\nDo nhu cầu về y tế của nhân dân ngày một tăng nên bệnh viện đã bắt đầu được xây dựng từ năm 1916. Đến năm 1945 bệnh viện đưa vào sử dụng Tây y và lấy tên là Bệnh viện Triều Châu. Sau nhiều lần kiến thiết hiện đại, bệnh viện đã có bộ mặt như ngày nay vào năm 1970.\r\n\r\nNăm 1978 bệnh viện được công lập hoá, trở thành bệnh viện An Bình, là bệnh viện đa khoa do Sở Y tế Thành phố Hồ Chí Minh quản lí trực tiếp.\r\n\r\nTrải qua quá trình phát triển không ngừng, đặc biệt là gần 20 năm qua, bệnh viện được Thành phố liên tục giao nhiệm vụ thí điểm từ 1972 – 1994 là bệnh viện thí điểm thu phí một phần.\r\n\r\nNgày 19/5/1994 bệnh viện An Bình được vinh dự nhận trách nhiệm do Đảng bộ và chính quyền TP giao: \" Chăm lo sức khoẻ cho nhân dân lao động nghèo của TP.HCM\". Từ đó bệnh viện mang tên mới là Bệnh viện miễn phí An Bình.\r\n\r\nVà đến tháng 01/2001, bệnh viện đã trở lại mang tên là Bệnh viện Đa Khoa An Bình.\r\n', 'images/facilities/facility_692416f1e07478.92233058.png', '2025-11-24 08:27:29'),
(11, 'PHÒNG KHÁM ĐA KHOA KHANG AN', 'clinic', '213 Đ. Đào Duy Từ, Phường 6, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', '(+84) 2839575188', 'Thứ 2- Thứ Bảy: 07:00 - 19:00', 'Phòng khám Khang An họat động với mục tiêu cung cấp dịch vụ y khoa “chuyên nghiệp và hiện đại – vì sức khỏe và an toàn bệnh nhân”. Phòng khám được sự hỗ trợ chuyên môn và liên kết trực tiếp với các Bệnh viện lớn tại TPHCM Đội ngũ thăm khám và cố vấn chuyên môn là những Giáo sư - Phó giáo sư - Tiến sĩ... chuyên gia hàng đầu trong lĩnh vưc Cơ xương khớp. \r\n\r\nCác dịch vụ của phòng khám Khang An:\r\n\r\nKhám bệnh chuyên khoa cơ xương khớp\r\nKhám bệnh lão khoa\r\nSiêu âm tổng quát – xét nghiệm máu – đo loãng xương\r\nNội khoa – trị khàn tiếng\r\nMột số bác sĩ tiêu biểu trong quá trình công tác tại đây có thể kể đến:\r\n\r\nTS BS Nguyễn Đình Khoa: Trưởng khoa cơ xương khớp Bệnh Viện Chợ Rẫy.\r\nThs. BS. Phạm Thị Vân Thanh: Bệnh Viện Chợ Rẫy – Tu Nghiệp tại Mỹ\r\nBS CKII. Nguyễn Thành Hưng: Thầy Thuốc Ưu Tú. Chuyên khám nội tổng quát cơ xương khớp.\r\nBên cạnh những thế mạnh về chuyên môn, phòng khám đa khoa Khang An còn đầu tư trang thiết bị hiện đại, đời mới và luôn cải tiến về chất lượng phục vụ.\r\n', 'images/facilities/facility_6924170cc03187.12471116.jpg', '2025-11-24 08:27:56'),
(12, 'Phòng khám Tai Mũi Họng Sài Gòn', 'clinic', '30, đường Phạm Hùng, KP2, P. Long Hoa , TX. Hòa Thành, T. Tây Ninh', ' 0904 690 818', 'Thứ 2- Thứ 6: 17:00 - 19:30 Thứ 7- Chủ Nhật: 08:00 - 12:00 /15:00 - 18:00', 'Phòng Khám Tai Mũi Họng Nhi Đồng Plus do các Bác sĩ có nhiều năm kinh nghiệm, hiện đang công tác tại các bệnh viện nhi đồng TP.HCM trực tiếp thăm khám và điều trị \r\n\r\nPhòng khám với phương châm hướng tới việc đảm bảo chất lượng điều trị, chi phí hợp lý và thuận tiện cho bệnh nhân khi đến khám \r\n\r\nChuyên khám:\r\n\r\n. Khám Tai - Mũi - Họng người lớn và trẻ em\r\n\r\n. Cắt Amidan, nạo VA, dó luân nhĩ, viêm tai giữa\r\n\r\n. Các khối U đầu cổ\r\n\r\n. Tầm soát ung thư đầu cổ qua nội soi \r\n\r\n. Tư vấn tầm soát , chẩn đoán , can thiệp khiếm thính\r\n', 'images/facilities/facility_692417323ba1a8.56362238.png', '2025-11-24 08:28:34'),
(13, 'Phòng Khám Đa Khoa Sài Gòn Waterpoint', 'clinic', 'Khu Đô thị WATERPOINT - xã An Thạnh, huyện Bến Lức, tỉnh Long An', '0272 3979138', 'Thứ 2- Chủ Nhật: 07:00 - 16:00', 'Phòng khám Đa khoa Sài Gòn Waterpoint là 1 chuỗi đơn vị y tế chất lượng cao có cơ sở đầu tiên được xây dựng trên một khu vực yên tĩnh ven sông Vàm Cỏ Đông, “Thành phố bên sông” Waterpoin Nam Long, khu đô thị tích hợp hàng đầu tại cửa ngõ Tây Nam TP.HCM  trực thuộc xã An Thạnh, huyện Bến Lức, tỉnh Long An.\r\n\r\nPKĐK Sài Gòn Waterpoint tọa lại ngay tại trung tâm khu đô thị Waterpoint Nam Long\r\n\r\nPhòng khám được thành lập với sứ mệnh mang đến dịch vụ chăm sóc sức khỏe chất lượng cao, uy tín, giá cả hợp lý cho cư dân Nam Long, người dân tỉnh Long An và các tỉnh lân cận\r\n\r\nChúng tôi luôn lấy Người bệnh làm trung tâm, lấy  chất lượng và sự thân thiện làm nền tảng xây dựng Tầm nhìn – Sứ mệnh – Giá trị cốt lõi làm tiêu chí hoạt động để đội ngũ Bác sĩ, Điều dưỡng và nhân viên mang lại sự hài lòng nhất cho người bệnh.\r\n\r\nTẦM NHÌN\r\n\r\nTrở thành cơ sở y tế hàng đầu về cung cấp dịch vụ chăm sóc sức khỏe toàn diện cho người dân trên khắp các tỉnh, thành trên toàn quốc.\r\n\r\nSỨ MỆNH\r\n\r\nGóp phần đảm bảo chất lượng cuộc sống cộng đồng bằng những dịch vụ chăm sóc sức khỏe hiện đại theo tiêu chuẩn quốc tế, dựa trên y học chứng cứ, theo đúng tiêu chí Lấy người bệnh làm trung tâm – Chất lượng – Thân thiện.\r\n\r\nGIÁ TRỊ CỐT LÕI\r\n\r\nChúng tôi đặt an toàn điều trị lên hàng đầu, đảm bảo đầy đủ các quyền lợi để khách hàng được hưởng dịch vụ y tế tốt nhất, tiện ích nhất dựa trên những giá trị cốt lõi: Lấy người bệnh làm trung tâm – Chất lượng – Thân thiện.\r\n\r\nĐiểm nổi bật của Phòng khám đa khoa Sài Gòn Waterpoint:\r\n\r\nĐội ngũ bác sĩ:\r\nGiàu kinh nghiệm, chuyên môn cao, từng tốt nghiệp từ các trường đại học y danh tiếng trong nước và nước ngoài.\r\nTham gia thường xuyên các hội thảo, khóa đào tạo nâng cao chuyên môn.\r\nLuôn cập nhật kiến thức y khoa mới nhất để mang đến dịch vụ tốt nhất cho bệnh nhân.\r\nTrang thiết bị:\r\nHiện đại, tiên tiến, nhập khẩu từ các nước phát triển như Mỹ, Châu Âu, Nhật Bản, Hàn Quốc…\r\nGiúp chẩn đoán chính xác bệnh lý, hỗ trợ điều trị hiệu quả.\r\nDịch vụ đa dạng:\r\nKhám chữa bệnh đa khoa: Nội khoa, ngoại khoa, sản phụ khoa…\r\nXét nghiệm, chẩn đoán hình ảnh.\r\nĐiều trị nội khoa, ngoại khoa.\r\nMôi trường:\r\nKhang trang, sạch sẽ, vô trùng.\r\nHệ thống điều hòa, thông gió hiện đại.\r\nPhòng khám thoáng mát, tạo cảm giác thoải mái cho bệnh nhân.\r\nGiá cả:\r\nHợp lý, phù hợp với mọi đối tượng.\r\nCó nhiều chương trình ưu đãi, hỗ trợ cho bệnh nhân.\r\nChăm sóc khách hàng:\r\nChu đáo, tận tâm.\r\nLuôn lắng nghe, giải đáp mọi thắc mắc của bệnh nhân.\r\nCó hệ thống đặt lịch khám online, giúp tiết kiệm thời gian cho bệnh nhân.\r\n\r\n\r\nCÁC DỊCH VỤ CHUYÊN KHOA\r\n\r\nNỘI KHOA\r\n\r\nKhám và điều trị các bệnh lý: Tim mạch, Hô hấp, Tiểu đường, Nội tiết, các bệnh lý cô xương khớp....\r\n\r\nNGOẠI KHOA\r\n\r\n Khám và điều trị các bệnh lý: Tiêu hóa, Gan, Mật, Tiết niệu Phẫu thuật các khối u phần mềm như u mỡ, u bã đậu Chích nhọt, mũ, apxe... UNG BƯỚU - MẠCH MÁU Khám và điều trị các bệnh lý tuyến giáp Khám và điều trị các bệnh lý mạch máu ngoại biên\r\n\r\nCHẤN THƯƠNG CHỈNH HÌNH\r\n\r\nNắn chỉnh xương khớp, băng cố định, bó bột, tháo bột Khâu các vết thương phần mềm\r\n\r\nSẢN PHỤ KHOA\r\n\r\n Khám và điều trị các bệnh lý phụ khoa như: Các bệnh lý tử cung, buồng trứng, các bệnh viêm nhiễm phần phụ... Khám và chăm sóc thai định kỳ Dịch vụ thai sản\r\n\r\nCHẨN ĐOÁN HÌNH ẢNH\r\n\r\nSIÊU ÂM\r\n\r\nHệ thống siêu âm máy siêu âm hiện đại với 5 đầu dò giúp chẩn đoán chính xác các bệnh lý\r\n\r\nỔ bụng: gan, mật, tụy, thận niệu, tổ chức phần mềm\r\n\r\nTuyến giáp, tuyến vú\r\n\r\nTheo dõi thai kỳ\r\n\r\nCác bệnh lý  phụ khoa\r\n\r\nX – QUANG\r\n\r\nMáy X- quang kỹ thuật số cho hình ảnh với độ phân giải cao giúp bác sĩ khảo sát cấu trúc các bộ phận cơ thể một cách rõ nét như chụp xương khớp, chụp cột sống, chụp phổi, chụp hệ tiết niệu\r\n\r\nNỘI SOI\r\n\r\nHệ thống máy nội soi Colympus cho hình ảnh rõ nét giúp chẩn đoán và điều trị các bệnh lý dạ dày, đại trực tràng\r\n\r\nTRUNG TÂM XÉT NGHIỆM\r\n\r\nPhòng khám trang bị hệ thống máy xét nghiệm hiện đại thuộc các lĩnh vực, huyết học, sinh hóa, miễn dịch, nội tiết giúp thực hiện các xét nghiệm\r\n\r\nTổng phân tích tế bào máu\r\n\r\nXét nghiệm tầm soát tiểu đường\r\n\r\nXét nghiệm tầm soát ung thư giai đoạn sớm\r\n\r\nXét nghiệm đánh giá chức năng gan, chức năng thận, chức năng tuyến giáp\r\n\r\nVới những cam kết về chất lượng dịch vụ, Phòng khám đa khoa Sài Gòn Waterpoint mong muốn trở thành người bạn đồng hành tin cậy trong việc chăm sóc sức khỏe cho bạn và gia đình.\r\n', 'images/facilities/facility_6924176a89e9c8.92013910.png', '2025-11-24 08:29:30'),
(14, 'Phòng khám đa khoa Hoàn Mỹ Sài Gòn', 'clinic', '4A Hoàng Việt, Phường 4, Tân Bình, Thành phố Hồ Chí Minh 700000', '1900633449', 'Thứ 2- Thứ 3: 07:30 - 11:30', 'Phòng Khám Đa khoa Hoàn Mỹ Sài Gòn (được đổi tên từ “Phòng khám Đa Khoa Hoàn Mỹ Tân Bình” vào tháng 11/2015) trực thuộc Bệnh viện Đa Khoa Hoàn Mỹ Sài Gòn và là thành viên của Tập đoàn Y khoa Hoàn Mỹ. Với dịch vụ khám, chữa bệnh chất lượng cao và tinh thần phục vụ chu đáo, Phòng Khám Đa khoa Hoàn Mỹ Sài Gòn đã trở thành địa chỉ khám, chữa bệnh tin cậy cho hầu hết người dân TP.HCM và các khu vực lân cận. Đội ngũ thạc sĩ, bác sĩ, điều dưỡng giỏi chuyên môn và được hỗ trợ bởi nhiều trang thiết bị hiện đại, hệ thống xét nghiệm hiện đại… giúp Phòng khám đạt nhiều thành công trong ứng dụng kỹ thuật y khoa tiên tiến vào chẩn đoán và điều trị. “Chuyên môn” và “Y đức” là yếu tố được đặt lên hàng đầu tại Phòng Khám Đa Khoa Hoàn Mỹ Sài Gòn cũng như Hệ thống Y khoa Hoàn Mỹ nói chúng. Với phương châm “tất cả vì bệnh nhân”, đội ngũ bác sĩ của Phòng khám luôn nỗ lực với tinh thần trách nhiệm cao nhất để mang lại cho bệnh nhân dịch vụ khám chữa bệnh chất lượng cao với chi phí hợp lý. Bên cạnh đó, nhằm tăng cường chất lượng phục vụ khách hàng, phòng khám không ngừng hợp tác và chia sẻ kinh nghiệm với các bệnh viện/phòng khám trong hệ thống Hoàn Mỹ cũng như các tổ chức chăm sóc y tế trong và ngoài nước. Việc đầu tư các thiết bị y tế thế hệ mới cho phép Phòng khám phục vụ bệnh nhân ngày một tốt hơn với các kỹ thuật khám, chữa bệnh tiên tiến giúp nâng cao hiệu quả, rút ngắn thời gian điều trị và giảm thiểu các thương tổn cho bệnh nhân.', 'images/facilities/facility_692417a84ef840.07748246.png', '2025-11-24 08:30:32'),
(15, 'PHÒNG KHÁM ĐA KHOA QUỐC TẾ VIỆT HEALTHCARE', 'clinic', '16 & 18 Đ. Lý Thường Kiệt, Phường Diên Hồng, Thành phố Hồ Chí Minh', '0795787879', 'Thứ 2- Thứ 7: 06;00-21:00', 'Phòng Khám đa khoa quốc tế Việt Healthcare được thành lập theo Giấy phép Hoạt động Khám bệnh, Chữa bệnh số 08143/HCM-GPHĐ của Sở Y tế TP. Hồ Chí Minh.\r\n\r\nHệ thống máy xét nghiệm được nhập khẩu từ Nhật. Ngoài các xét nghiệm thường qui, còn có máy tầm soát nhiễm virus viêm gan siêu vi A, B, C, chức năng tuyến giáp và các marker ung thư của các cơ quan như: Phổi, Dạ dày – Đại tràng, Gan – Mật – Tụy, Vú, Tử cung – Buồng trứng và Tiền liệt tuyến.\r\n\r\nMáy Test hơi thở C13 của Đức là dòng máy hiện đại, chẩn đoán nhiễm vi khuẩn HP dạ dày qua hơi thở.\r\n\r\nMáy siêu âm của Nhật, hiệu Hitachi Aloka Arietta 850, là dòng máy siêu âm cao cấp của Hitachi, có 5 đầu dò, đặc biệt là chức năng siêu âm đàn hồi mô thời gian thực (RTE) để chẩn đoán bệnh lý mô tuyến vú, tuyến giáp, hệ niệu và độ xơ cứng gan từ giai đoạn rất sớm.\r\n\r\nHệ thống máy nội soi ống tiêu hóa của Nhật, hiệu Olympus CV170, là dòng máy hiện đại, có 2 tính năng độc đáo là tính năng NBI và nội soi độ phân giải cao, giúp Bác sĩ nội soi quan sát chi tiết tổn thương niêm mạc ống tiêu hóa, tạo nên bước đột phá trong việc sàng lọc, chẩn đoán bệnh ung thư dạ dày – tá tràng và đại trực tràng ở giai đoạn sớm và rất sớm.\r\n\r\n Điểm ưu việt của Phòng Nội soi là vệ sinh dụng cụ nội soi bằng máy rửa tự động của Hàn Quốc, đảm bảo an toàn cho quý khách, góp phần nâng cao chất lượng điều trị.\r\n\r\nNgoài ra tại phòng khám còn có nhà thuốc đạt chuẩn GGP\r\n', 'images/facilities/facility_692417d361bec5.59062577.jpg', '2025-11-24 08:31:15'),
(16, 'Phòng khám Phụ Sản 315 Quận Tân Bình', 'clinic', '490 - 492 Đ. Trường Chinh, Phường Tân Bình, Tân Bình, Thành phố Hồ Chí Minh 70000, Việt Nam', '(+84) 899949315', 'Thứ 2- Thứ 6:17:00 - 20:30 Thứ 7- Chủ Nhật: 08:00 - 11:30 /13:30 - 20:30', 'Hệ thống phòng khám Phụ sản 315 thuộc chuỗi hệ sinh thái 315 healthcare. Đây là một trong những hệ thống có quy mô lớn tại TP.HCM, sở hữu cơ sở vật chất hiện đại, môi trường y tế đạt chuẩn quốc tế. Đặc biệt, phòng khám luôn áp dụng các phương pháp điều trị bệnh tiên tiến, hiệu quả.\r\n\r\nSong song, phòng khám có đội ngũ các bác sĩ giàu kinh nghiệm và nhiều năm công tác tại các bệnh viện lớn như Từ Dũ, Hùng Vương,... Khi đến khám, bác sĩ trực tiếp điều trị và giải thích cặn kẽ kết quả xét nghiệm, chẩn đoán cũng như hỗ trợ chăm sóc y học cần thiết cho sự phát triển thai kỳ hay các vấn đề phụ khoa.\r\n\r\n\r\n\r\nThẻ thành viên phụ khoa 1.150.000 VNĐ giảm còn 980.000 VNĐ/năm\r\n\r\n·       Không giới hạn dịch vụ tư vấn sức khỏe phụ khoa\r\n\r\n·       Công thêm:\r\n\r\no  1 lần định kỳ khám nhũ, siêu âm nhũ\r\n\r\nThẻ vàng phụ khoa 1.890.000 VNĐ giảm còn 1.600.000 VNĐ/năm\r\n\r\n·       Không giới hạn dịch vụ tư vấn sức khỏe phụ khoa\r\n\r\n·       Công thêm:\r\n\r\no  1 lần định kỳ khám nhũ, siêu âm nhũ\r\n\r\no  2 lần định kỳ soi dịch âm đạo\r\n\r\no  1 lần xét nghiệm định kỳ công thức máu\r\n\r\no  1 lần xét nghiệm định kỳ nước tiểu\r\n\r\nThẻ thành viên sản khoa\r\n\r\n·       Tuổi thai < 12 tuần: 5.000.000VNĐ giảm còn 4.000.000VNĐ\r\n\r\n·       Tuổi thai 12 – 26 tuần: 4.000.000VNĐ giảm còn 3.200.000 VNĐ\r\n\r\n·       Tuổi thai > 26 tuần: 3.000.000VNĐ giảm còn 2.400.000 VNĐ\r\n\r\nThẻ vàng sản phụ khoa\r\n\r\n·       Tuổi thai < 12 tuần: 7.500.000VNĐ giảm còn 6.000.000 VNĐ\r\n\r\n·       Tuổi thai 12 – 26 tuần: 6.500.000VNĐ giảm còn 5.200.000 VNĐ\r\n\r\n·       Tuổi thai > 26 tuần: 5.500.000VNĐ giảm còn 4.400.000 VNĐ \r\n', 'images/facilities/facility_692418058bb538.02415094.png', '2025-11-24 08:32:05'),
(17, 'Phòng Khám Tim Mạch - Tiểu Đường 315 Quận Bình Tân', 'clinic', 'Đ. Lê Văn Quới/582 - 584 P, Khu phố 11, Bình Tân, Thành phố Hồ Chí Minh 70000, Việt Nam', '0933077315', 'Thứ Hai- Chủ Nhật: 08:00–11:30,13:30–20:30', 'Hệ thống Phòng Khám Tim Mạch - Tiểu Đường 315 được đầu tư và phát triển bởi Tập đoàn Y Tế 315 với tầm nhìn “Trở thành phòng khám chuyên khoa Lão chất lượng - uy tín, đáp ứng nhu cầu chăm sóc sức khỏe của người cao tuổi trong và ngoài nước”.\r\n\r\nSứ mệnh “Luôn lấy bệnh nhân làm trung tâm, cung cấp dịch vụ chăm sóc sức khỏe đạt chuẩn Quốc tế nhằm không ngừng nâng cao chất lượng sống cho cộng đồng.”\r\n\r\nPhòng khám Tim Mạch - Tiểu Đường 315 cung cấp các dịch vụ chăm sóc y tế toàn diện cho người cao tuổi, bao gồm các chuyên khoa về Tim Mạch, Nội Tổng Hợp, Hô Hấp, Tiêu Hoá, Cơ Xương Khớp, Thần Kinh.\r\n\r\nĐội ngũ Bác sĩ chuyên khoa giàu kinh nghiệm đến từ các bệnh viện hàng đầu Thành phố như Bệnh viện Nhân dân 115, BV Gia Định, Nguyễn Tri Phương,.. áp dụng phương pháp chẩn đoán và điều trị tiên tiến cho bệnh nhân; Trang thiết bị y tế hiện đại, đạt chuẩn Quốc tế mang đến sự chuyên nghiệp khi bệnh nhân thăm khám, điều trị tại đây.\r\n\r\nVới sự tận tâm và nỗ lực không ngừng, Phòng Khám Tim Mạch - Tiểu Đường 315 mong muốn trở thành điểm đến chất lượng cho sức khỏe người cao tuổi, góp phần nâng cao chất lượng cuộc sống và sự phục hồi sức khỏe cho người cao tuổi trong cộng đồng.\r\n', 'images/facilities/facility_692418248e72f1.05948999.png', '2025-11-24 08:32:36'),
(18, 'Phòng khám Sản phụ khoa Hiếm muộn Tân Bình', 'clinic', '302B Đ. Lý Thường Kiệt, Phường 06, Tân Bình, Thành phố Hồ Chí Minh 70000', '(024) 3843 0748', 'Thứ Hai- Thứ Bảy: 08:00–21:00/Chủ Nhật,08:00–12:00', 'Phòng khám sản phụ khoa Tân Bình cung cấp các dịch vụ chẩn đoán và điều trị các bệnh lý phụ khoa và sản khoa. Chúng tôi cung cấp dịch vụ trọn gói cho bệnh nhân với sự tận tâm của đội ngũ y – bác sĩ nhiều năm kinh nghiệm và trang thiết bị hiện đại như máy siêu âm 4D, siêu âm đầu dò âm đạo, máy nghe tim thai, phòng và máy xét nghiệm tại chỗ cho kết quả nhanh nhất. Dụng cụ sử dụng 1 lần và riêng biệt cho từng người bệnh, bệnh nhân được cam kết chăm sóc chu đáo và riêng biệt trong môi trường thân thiện và chuyên nghiệp', 'images/facilities/facility_692418820c92b1.78795588.jpg', '2025-11-24 08:34:10'),
(19, 'Phòng khám đa khoa Bình Điền', 'clinic', '87 Quản Trọng Linh, Phường 7, Quận 8, Thành phố Hồ Chí Minh', '0839236632', 'Thứ Hai-Chủ Nhật: 07:30–17:00', 'Phòng khám Bình Điền tự hào với đội ngũ Bác Sĩ tốt nghiệp Đại học Y Dược Tp.HCM, Đại học Y Hà Nội, Y Huế, tu nghiệp ở các nước tiên tiến, có nhiều năm kinh nghiệm công tác tại các bệnh viện hàng đầu như: Chợ rẫy, Đại Học Y Dược, Từ Dũ, Hùng Vương ... Trong lĩnh vực nội - ngoại - sản - nhi.', 'images/facilities/facility_692418be5793a1.35682088.jpg', '2025-11-24 08:35:10'),
(20, 'Phòng khám Hello Doctor', 'clinic', '152/6 Đ. Thành Thái, Phường 12, Quận 10, Thành phố Hồ Chí Minh 700000', '0886006167', 'Thứ Hai- Thứ Sáu: 08:00–20:00/Thứ Bảy-Chủ Nhật: 08:00–18:00', 'Đầu năm 2011 Hello Doctor đã ghi dấu sự hình thành của mình ở Việt Nam về y tế từ xa (Telemedicine)- một lĩnh vực khá mới mẻ và đang phát triển nhanh chóng trên thế giới với mục tiêu phục vụ cho những bệnh nhân quan tâm về sức khỏe muốn được bác sĩ tư vấn và đưa ra giải pháp hỗ trợ đã thu hút hơn 300 giáo sư bác sĩ tham gia hệ thống, hợp tác hướng điều trị chuyên sâu.\r\n\r\nƯớc tính Hello Doctor đã phục vụ cho hơn 4.000.000 bệnh nhân nắm rõ tình trạng sức khỏe của mình và cung cấp cho họ giải pháp sức khỏe đúng đắn nhất trên toàn quốc như Hồ Chí Minh, Hà Nội, Đà Nẵng, Khánh Hòa, Đồng Nai, Bình Dương, Cần Thơ, Cà Mau….\r\n\r\nChúng tôi tin rằng Sức khỏe là thứ không thể đánh mất trong cuộc đời. Do vậy, Hello Doctor có sứ mệnh với mỗi cá nhân được chữa lành bệnh khi mang bệnh và sống hạnh phúc khi khỏe mạnh.\r\n\r\nĐiều trị chuyên sâu\r\n\r\nBệnh nhân của Hello Doctor sẽ được hướng dẫn và cung cấp các thông tin hữu ích trong quá trình tư vấn và điều trị như nơi khám chữa bệnh hợp lý, các xét nghiệm cần nên làm, bác sĩ chuyên khoa giỏi(đủ uy tín và nhiều kinh nghiệm) theo nhiều chuyên khoa như cơ xương khớp, tâm thần kinh, nội ngoại thần kinh, nam khoa…và nhiều phương pháp điều trị khác nhau như phẫu thuật, can thiệp tim mạch, xạ trị, hóa trị…\r\n', 'images/facilities/facility_692418f8da4886.61054724.png', '2025-11-24 08:36:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `facility_admins`
--

CREATE TABLE `facility_admins` (
  `admin_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `facility_admins`
--

INSERT INTO `facility_admins` (`admin_id`, `facility_id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 1, 'Admin Bệnh viện Việt-Đức', 'facility1.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:00:00'),
(2, 2, 'Admin Bệnh Viện K', 'facility2.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:01:00'),
(3, 3, 'Admin Bệnh viện Nhi Đồng 2', 'facility3.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:02:00'),
(4, 4, 'Admin Bệnh viện Hồng Ngọc', 'facility4.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:03:00'),
(5, 5, 'Admin BV Y Học Cổ Truyền TP.HCM', 'facility5.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:04:00'),
(6, 6, 'Admin Bệnh viện Bình Dân', 'facility6.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:05:00'),
(7, 7, 'Admin Bệnh viện Đa Khoa Gò Vấp', 'facility7.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:06:00'),
(8, 8, 'Admin Bệnh viện Đa Khoa Củ Chi', 'facility8.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:07:00'),
(9, 9, 'Admin BV ĐKQT Nam Sài Gòn', 'facility9.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:08:00'),
(10, 10, 'Admin Bệnh viện An Bình', 'facility10.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:09:00'),
(11, 11, 'Admin PK Đa Khoa Khang An', 'facility11.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:10:00'),
(12, 12, 'Admin PK Tai Mũi Họng Sài Gòn', 'facility12.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:11:00'),
(13, 13, 'Admin PK Đa Khoa SG Waterpoint', 'facility13.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:12:00'),
(14, 14, 'Admin PK Đa khoa Hoàn Mỹ SG', 'facility14.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:13:00'),
(15, 15, 'Admin PK Quốc tế Việt Healthcare', 'facility15.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:14:00'),
(16, 16, 'Admin PK Phụ Sản 315 Tân Bình', 'facility16.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:15:00'),
(17, 17, 'Admin PK Tim Mạch 315 Bình Tân', 'facility17.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:16:00'),
(18, 18, 'Admin PK Sản phụ khoa Tân Bình', 'facility18.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:17:00'),
(19, 19, 'Admin PK Đa khoa Bình Điền', 'facility19.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:18:00'),
(20, 20, 'Admin PK Hello Doctor', 'facility20.admin@medicare.vn', '$2y$10$eOANt5TV/.UdyjUaTMHwruHiz6ZGMI5v1JSncbcSDetVk48xKS0xi', '2025-11-24 09:19:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `facility_specialty`
--

CREATE TABLE `facility_specialty` (
  `id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `facility_specialty`
--

INSERT INTO `facility_specialty` (`id`, `facility_id`, `specialty_id`, `created_at`) VALUES
(0, 1, 1, '2025-11-24 09:42:29'),
(0, 1, 4, '2025-11-24 09:42:29'),
(0, 1, 5, '2025-11-24 09:42:29'),
(0, 1, 6, '2025-11-24 09:42:29'),
(0, 1, 7, '2025-11-24 09:42:29'),
(0, 1, 10, '2025-11-24 09:42:29'),
(0, 1, 14, '2025-11-24 09:42:29'),
(0, 2, 11, '2025-11-24 09:42:29'),
(0, 2, 4, '2025-11-24 09:42:29'),
(0, 2, 13, '2025-11-24 09:42:29'),
(0, 2, 15, '2025-11-24 09:42:29'),
(0, 3, 3, '2025-11-24 09:42:29'),
(0, 3, 4, '2025-11-24 09:42:29'),
(0, 3, 5, '2025-11-24 09:42:29'),
(0, 3, 7, '2025-11-24 09:42:29'),
(0, 3, 12, '2025-11-24 09:42:29'),
(0, 3, 15, '2025-11-24 09:42:29'),
(0, 4, 1, '2025-11-24 09:42:29'),
(0, 4, 2, '2025-11-24 09:42:29'),
(0, 4, 4, '2025-11-24 09:42:29'),
(0, 4, 9, '2025-11-24 09:42:29'),
(0, 4, 10, '2025-11-24 09:42:29'),
(0, 4, 15, '2025-11-24 09:42:29'),
(0, 5, 13, '2025-11-24 09:42:29'),
(0, 5, 4, '2025-11-24 09:42:29'),
(0, 5, 10, '2025-11-24 09:42:29'),
(0, 5, 14, '2025-11-24 09:42:29'),
(0, 5, 15, '2025-11-24 09:42:29'),
(0, 6, 1, '2025-11-24 09:42:29'),
(0, 6, 4, '2025-11-24 09:42:29'),
(0, 6, 6, '2025-11-24 09:42:29'),
(0, 6, 10, '2025-11-24 09:42:29'),
(0, 7, 4, '2025-11-24 09:42:29'),
(0, 7, 9, '2025-11-24 09:42:29'),
(0, 7, 10, '2025-11-24 09:42:29'),
(0, 7, 13, '2025-11-24 09:42:29'),
(0, 8, 4, '2025-11-24 09:42:29'),
(0, 8, 9, '2025-11-24 09:42:29'),
(0, 8, 10, '2025-11-24 09:42:29'),
(0, 8, 13, '2025-11-24 09:42:29'),
(0, 9, 1, '2025-11-24 09:42:29'),
(0, 9, 4, '2025-11-24 09:42:29'),
(0, 9, 9, '2025-11-24 09:42:29'),
(0, 9, 10, '2025-11-24 09:42:29'),
(0, 10, 1, '2025-11-24 09:42:29'),
(0, 10, 4, '2025-11-24 09:42:29'),
(0, 10, 6, '2025-11-24 09:42:29'),
(0, 10, 10, '2025-11-24 09:42:29'),
(0, 11, 2, '2025-11-24 09:42:29'),
(0, 11, 4, '2025-11-24 09:42:29'),
(0, 11, 8, '2025-11-24 09:42:29'),
(0, 11, 9, '2025-11-24 09:42:29'),
(0, 11, 10, '2025-11-24 09:42:29'),
(0, 12, 5, '2025-11-24 09:42:29'),
(0, 12, 3, '2025-11-24 09:42:29'),
(0, 12, 7, '2025-11-24 09:42:29'),
(0, 13, 4, '2025-11-24 09:42:29'),
(0, 13, 9, '2025-11-24 09:42:29'),
(0, 13, 10, '2025-11-24 09:42:29'),
(0, 13, 13, '2025-11-24 09:42:29'),
(0, 14, 1, '2025-11-24 09:42:29'),
(0, 14, 2, '2025-11-24 09:42:29'),
(0, 14, 4, '2025-11-24 09:42:29'),
(0, 14, 10, '2025-11-24 09:42:29'),
(0, 15, 4, '2025-11-24 09:42:29'),
(0, 15, 5, '2025-11-24 09:42:29'),
(0, 15, 8, '2025-11-24 09:42:29'),
(0, 15, 9, '2025-11-24 09:42:29'),
(0, 16, 9, '2025-11-24 09:42:29'),
(0, 16, 3, '2025-11-24 09:42:29'),
(0, 16, 15, '2025-11-24 09:42:29'),
(0, 17, 1, '2025-11-24 09:42:29'),
(0, 17, 2, '2025-11-24 09:42:29'),
(0, 17, 4, '2025-11-24 09:42:29'),
(0, 17, 15, '2025-11-24 09:42:29'),
(0, 18, 9, '2025-11-24 09:42:29'),
(0, 18, 3, '2025-11-24 09:42:29'),
(0, 18, 10, '2025-11-24 09:42:29'),
(0, 19, 4, '2025-11-24 09:42:29'),
(0, 19, 8, '2025-11-24 09:42:29'),
(0, 19, 9, '2025-11-24 09:42:29'),
(0, 19, 10, '2025-11-24 09:42:29'),
(0, 20, 4, '2025-11-24 09:42:29'),
(0, 20, 6, '2025-11-24 09:42:29'),
(0, 20, 12, '2025-11-24 09:42:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `specialties`
--

CREATE TABLE `specialties` (
  `specialty_id` int(11) NOT NULL,
  `specialty_name` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `specialties`
--

INSERT INTO `specialties` (`specialty_id`, `specialty_name`, `icon`, `created_at`) VALUES
(1, 'Tim mạch', 'images\\specialties\\Tim_mach.jpg', '2025-11-18 13:14:44'),
(2, 'Da liễu', 'images\\specialties\\Da_lieu.png', '2025-11-18 13:14:44'),
(3, 'Nhi khoa', 'images\\specialties\\Nhi_khoa.png', '2025-11-18 13:14:44'),
(4, 'Nội tổng quát', 'images\\specialties\\Noi_tong_quat.png', '2025-11-18 13:14:44'),
(5, 'Tai - Mũi - Họng', 'images\\specialties\\Tai_mui_hong.jpg', '2025-11-18 13:14:44'),
(6, 'Thần kinh', 'images\\specialties\\Than_kinh.jpg', '2025-11-18 13:14:44'),
(7, 'Mắt', 'images\\specialties\\Mat.jpg', '2025-11-18 13:14:44'),
(8, 'Nha khoa', 'images\\specialties\\Nha_khoa.jpg', '2025-11-18 13:14:44'),
(9, 'Sản phụ khoa', 'images\\specialties\\San_phu_khoa.png', '2025-11-18 13:14:44'),
(10, 'Cơ xương khớp', 'images\\specialties\\Co_xuong_khop.jpg\r\n', '2025-11-18 13:14:44'),
(11, 'Ung bướu', 'images\\specialties\\Ung_buou.png', '2025-11-18 13:14:44'),
(12, 'Tâm thần', 'images\\specialties\\Tam_than.png', '2025-11-18 13:14:44'),
(13, 'Y học cổ truyền', 'images\\specialties\\Y_hoc_co_truyen.png', '2025-11-18 13:14:44'),
(14, 'Phục hồi chức năng', 'images\\specialties\\Phuc_hoi_chuc_nang.png', '2025-11-18 13:14:44'),
(15, 'Dinh dưỡng', 'images\\specialties\\Dinh_duong.jpg', '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'Nguyễn Văn A', 'nguyenvana@email.com', '0901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(2, 'Trần Thị B', 'tranthib@email.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(3, 'Lê Văn C', 'levanc@email.com', '0912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(4, 'Phạm Thị D', 'phamthid@email.com', '0923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(5, 'Hoàng Văn E', 'hoangvane@email.com', '0934567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(6, 'Võ Thị F', 'vothif@email.com', '0945678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(7, 'Đặng Văn G', 'dangvang@email.com', '0956789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(8, 'Bùi Thị H', 'buithih@email.com', '0967890123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(9, 'Ngô Văn I', 'ngovani@email.com', '0978901234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(10, 'Lý Thị K', 'lythik@email.com', '0989012345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-18 13:14:44'),
(11, 'votranhoang', 'votranhoang162004@gmail.com', '0949168999', '$2y$10$ZdXHApwfqgQX3Bg5nHJTxelhILPMx/39HWV3DD1dc7N7tJfvIbA4O', '2025-11-18 15:08:20');

-- --------------------------------------------------------

--
-- Cấu trúc cho view `appointment_details`
--
DROP TABLE IF EXISTS `appointment_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointment_details`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`symptoms` AS `symptoms`, `a`.`status` AS `status`, `a`.`created_at` AS `created_at`, `u`.`user_id` AS `user_id`, `u`.`fullname` AS `patient_name`, `u`.`email` AS `patient_email`, `u`.`phone` AS `patient_phone`, `f`.`facility_id` AS `facility_id`, `f`.`name` AS `facility_name`, `f`.`type` AS `facility_type`, `f`.`address` AS `facility_address`, `f`.`phone` AS `facility_phone`, `s`.`specialty_id` AS `specialty_id`, `s`.`specialty_name` AS `specialty_name` FROM (((`appointments` `a` join `users` `u` on(`a`.`user_id` = `u`.`user_id`)) join `facilities` `f` on(`a`.`facility_id` = `f`.`facility_id`)) join `specialties` `s` on(`a`.`specialty_id` = `s`.`specialty_id`)) ORDER BY `a`.`appointment_date` DESC, `a`.`appointment_time` DESC ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Chỉ mục cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_facility_id` (`facility_id`),
  ADD KEY `idx_specialty_id` (`specialty_id`),
  ADD KEY `idx_appointment_date` (`appointment_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date_time` (`appointment_date`,`appointment_time`);

--
-- Chỉ mục cho bảng `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_name` (`name`);

--
-- Chỉ mục cho bảng `facility_admins`
--
ALTER TABLE `facility_admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_facility_id` (`facility_id`),
  ADD KEY `idx_email` (`email`);

--
-- Chỉ mục cho bảng `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`specialty_id`),
  ADD UNIQUE KEY `specialty_name` (`specialty_name`),
  ADD KEY `idx_specialty_name` (`specialty_name`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `facility_admins`
--
ALTER TABLE `facility_admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `specialties`
--
ALTER TABLE `specialties`
  MODIFY `specialty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;