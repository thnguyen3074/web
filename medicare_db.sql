-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 05:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medicare_db`
--

DELIMITER $$
--
-- Procedures
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `email`, `password`, `name`, `created_at`) VALUES
(1, 'admin@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Administrator', '2025-11-18 13:14:44'),
(2, 'superadmin@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Super Administrator', '2025-11-18 13:14:44'),
(3, 'manager@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Manager', '2025-11-18 13:14:44'),
(4, 'support@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Support Staff', '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
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
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `facility_id`, `specialty_id`, `appointment_date`, `appointment_time`, `symptoms`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2024-12-20', '08:00:00', 'Đau ngực, khó thở', 'pending', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(2, 2, 2, 3, '2024-12-25', '09:30:00', 'Nổi mẩn đỏ trên da', 'confirmed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(3, 1, 3, 1, '2024-12-22', '14:00:00', 'Khám tim định kỳ', 'confirmed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(4, 4, 5, 3, '2024-12-21', '10:30:00', 'Trẻ sốt cao, ho nhiều', 'pending', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(5, 5, 6, 7, '2024-12-23', '15:00:00', 'Mắt mờ, nhức mắt', 'confirmed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(6, 6, 11, 4, '2024-12-24', '09:00:00', 'Khám tổng quát', 'pending', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(7, 7, 14, 5, '2024-12-26', '11:00:00', 'Đau họng, nghẹt mũi', 'confirmed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(8, 8, 16, 8, '2024-12-27', '14:30:00', 'Đau răng, sưng nướu', 'pending', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(9, 3, 9, 1, '2024-12-10', '10:00:00', 'Khám định kỳ', 'completed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(10, 2, 13, 2, '2024-12-05', '16:00:00', 'Nổi mụn, ngứa da', 'completed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(11, 9, 15, 7, '2024-12-08', '13:00:00', 'Khám mắt định kỳ', 'completed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(12, 10, 17, 10, '2024-12-12', '09:00:00', 'Đau khớp gối', 'completed', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(13, 4, 2, 1, '2024-11-28', '08:30:00', 'Đau tim', 'canceled', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(14, 5, 12, 3, '2024-12-01', '10:00:00', 'Trẻ ho, sốt', 'canceled', '2025-11-18 13:14:44', '2025-11-18 13:14:44'),
(15, NULL, 3, 6, '2025-11-07', '08:00:00', 'dsadas', 'pending', '2025-11-19 15:21:34', '2025-11-19 15:21:34'),
(16, NULL, 2, 3, '2025-11-07', '08:00:00', 'sadasdasd', 'pending', '2025-11-19 15:22:08', '2025-11-19 15:22:08');

--
-- Triggers `appointments`
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
-- Stand-in structure for view `appointment_details`
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
-- Table structure for table `contact_messages`
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
-- Table structure for table `facilities`
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
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`facility_id`, `name`, `type`, `address`, `phone`, `working_hours`, `description`, `image`, `created_at`) VALUES
(1, 'Bệnh viện Việt-Đức', 'hospital', '40 Tràng Thi, Hoàn Kiếm, Hà Nội', '(024) 3825 3531', '07:00 - 16:00', 'Trong suốt quá trình hình thành và phát triển, Bệnh viện luôn là một trung tâm y tế hàng đầu gắn liền công tác khám chữa bệnh với nghiên cứu khoa học y học và đào tạo, nơi sản sinh ra những thầy thuốc hàng đầu của Việt Nam trong đó có nhiều danh nhân y học: Hồ Đắc Di, Tôn Thất Tùng, Nguyễn Trinh Cơ, Nguyễn Dương Quang….', 'images/facilities/Viet_duc.jpg', '2025-11-18 13:14:44'),
(2, 'Bệnh viện 108', 'hospital', '1 Trần Hưng Đạo, Hai Bà Trưng, Hà Nội ', '069.572400', '06:30-17:00', '  Bệnh viện TWQĐ 108 là Bệnh viện hạng đặc biệt, là trung tâm y tế chuyên sâu của nước Cộng hoà Xã hội Chủ nghĩa Việt Nam, là bệnh viện đa khoa, chiến lược tuyến cuối của Quân đội Nhân dân Việt Nam. Với mô hình Viện - Trường từ năm 1995, Bệnh viện còn là trung tâm nghiên cứu khoa học và đào tạo sau đại học, với tên gọi Viện nghiên cứu Khoa học Y dược Lâm sàng 108. Vẻ vang và tự hào, Bệnh viện là trung tâm bảo vệ, chăm sóc sức khoẻ cho các đồng chí lãnh đạo cao nhất của Đảng, Nhà nước, Quân đội, khám chữa bệnh cho Bộ đội, Nhân dân và người bệnh quốc tế Lào, Cam Pu Chia.    ', 'images/facilities/108.png', '2025-11-18 13:14:44'),
(3, 'Bệnh viện Đa Khoa Xanh Pôn', 'hospital', '12 Chu Văn An, Quận Ba Đình, Hà Nội', '0989819115', 'Sáng: Từ 7h00 đến 12h00.\r\nChiều: Từ 13h30 đến 17h00.', 'Bệnh viện có bề dày lịch sử hình thành và phát triển lâu dài, thành lập từ những năm 1970 của thế kỷ 19 và là Cơ sở y tế đầu tiên của khu vực Đông Dương. Đến nay, bệnh viện Đa khoa Xanh Pôn đã trở thành bệnh viện hạng 1 của thành phố Hà Nội, Với hơn 600 giường bệnh cùng hơn 1000 cán bộ nhân viên thuộc nhiều chuyên khoa đầu ngành như Nhi khoa, Ngoại khoa, Phẫu thuật tạo hình, Gây mê hồi sức,…', 'images/facilities/Xanh_pon.png', '2025-11-18 13:14:44'),
(4, 'Vinmec Times City Hospital', 'hospital', '12 phố Chu Văn An, phường Ba Đình, Hà Nội', '(024) 3974 3556', '08:00–17:00', 'Vinmec Times City là bệnh viện đa khoa đầu tiên tại Việt Nam đạt chứng chỉ JCI - Chứng chỉ uy tín hàng đầu thế giới về thẩm định chất lượng dịch vụ y tế, được công nhận trên 90 quốc gia, “Tiêu chuẩn vàng” tại các bệnh viện danh tiếng toàn cầu.', 'images/facilities/vinmec.jpg', '2025-11-18 13:14:44'),
(5, 'Bệnh viện Nhi Trung Ương', 'hospital', 'Số 18, ngõ 879, đường La Thành, phường Láng, Thành phố Hà Nội.', ' 0862 33 55 66 ', 'Thứ 2-6: 7h00-16h30', 'Bệnh viện Nhi Trung ương là bệnh viện Nhi khoa số 1 Việt Nam, và là 1 trong 3 bệnh viện Nhi hàng đầu khu vực.', 'images/facilities/nhitrunguong.png', '2025-11-18 13:14:44'),
(6, 'Bệnh viện Hồng Ngọc', 'hospital', '55 Yên Ninh, Ba Đình', '(024) 3927 5568', 'Thứ 2-Chủ Nhật: 7h30-17h', 'Bệnh viện Đa khoa Hồng Ngọc được thành lập, là bệnh viện tư nhân chất lượng cao tại miền Bắc tiên phong trong mô hình \"bệnh viện - khách sạn\" với cơ sở vật chất đạt tiêu chuẩn quốc tế.', 'images/facilities/hongngoc.jpg', '2025-11-18 13:14:44'),
(7, 'Bệnh viện Đại học Y Dược TP.HCM', 'hospital', '215 Hồng Bàng, Q.5', '(028) 3855 4269', '06:30–16:30', 'Bệnh viện Đại học Y Dược TPHCM là địa chỉ chăm sóc sức khỏe uy tín của hàng triệu người bệnh. Bệnh viện luôn nỗ lực phát huy những giá trị cốt lõi bền vững, đó là:\r\nTIÊN PHONG trong điều trị người bệnh, nghiên cứu khoa học, đào tạo và quản trị;\r\nTHẤU HIỂU nỗi đau về thể xác lẫn tinh thần của người bệnh để đưa ra những giải pháp điều trị tối ưu; Giữ vững sự CHUẨN MỰC của người Thầy giáo – Thầy thuốc, luôn là tấm gương sáng để thế hệ tiếp nối noi theo; Quản lý chất lượng, đảm bảo AN TOÀN cho người bệnh và nhân viên y tế.', 'images/facilities/147.png\n', '2025-11-18 13:14:44'),
(8, 'Bệnh viện Nhân dân Gia Định', 'hospital', '1 Nơ Trang Long, Bình Thạnh', '(028) 3841 2692', '24/7', ' Bệnh viện Nhân dân Gia Định là một trong những Bệnh viện Đa khoa loại I trực thuộc Sở Y tế TP.HCM. Với đội ngũ Y, Bác sĩ chuyên môn cao, dày dạn kinh nghiệm, Bệnh viện có đủ các chuyên khoa lớn, nhiều phân khoa sâu, trang bị đầy đủ trang thiết bị y tế nhằm nâng cao chất lượng chẩn đoán, điều trị và chăm sóc bệnh nhân, đáp ứng nhu cầu khám chữa bệnh ngày càng cao của nhân dân. Với quy mô lớn 1.500 giường, hàng ngày Bệnh viện phục vụ khoảng 1.500 bệnh nhân nội trú, hơn 4.000 lượt bệnh nhân đến khám bệnh và hơn 300 lượt bệnh nhân cấp cứu.', 'images/facilities/giadinh.png', '2025-11-18 13:14:44'),
(9, 'Bệnh viện Thống Nhất', 'hospital', '1 Lý Thường Kiệt, Tân Bình', '(028) 3869 0277', '8:00 - 20:00', 'Bệnh viện Thống Nhất là một bệnh viện đa khoa hạng I trực thuộc Bộ Y Tế nằm ở số 1 đường Lý Thường Kiệt, phường 7, quận Tân Bình, Thành phố Hồ Chí Minh, với tên gọi ban đầu là Bệnh viện Vì Dân.Bệnh viện Thống Nhất là một trong những bệnh viện lớn và uy tín ở thành phố Hồ Chí Minh, với lịch sử hơn 50 năm hoạt động và phát triển. Bệnh viện Thống Nhất có quy mô hơn 1.200 giường, trở thành một trung tâm lão khoa lớn nhất Việt Nam. Bệnh viện Thống Nhất là một bệnh viện uy tín và chất lượng, được nhiều người dân tin tưởng lựa chọn. Bệnh viện có trang thiết bị hiện đại, đội ngũ bác sĩ giỏi và nhiều kinh nghiệm, chi phí khám chữa bệnh hợp lý.', 'images/facilities/thongnhat.png', '2025-11-18 13:14:44'),
(10, 'Bệnh viện Nguyễn Tri Phương', 'hospital', '468 Nguyễn Trãi, Phường An Đông, TP.Hồ Chí Minh', '(028) 3923 4349', '8:00 - 17:30', 'Bệnh viện Nguyễn Tri Phương - Bệnh viện đa khoa hạng 1 Tp.HCM trong mô hình hợp tác viện trường đầy đủ chuyên khoa Nội - Ngoại - Sản - Nhi.', 'images/facilities/triphuong.png', '2025-11-18 13:14:44'),
(11, 'Phòng khám An Khang', 'clinic', '184-188 Nguyễn Đình Chiểu, Q.3', '(028) 3930 2785', '8:00 - 20:00', 'Phòng khám An Khang là phòng khám đa khoa cung cấp các dịch vụ khám chữa bệnh nhanh chóng, hiện đại, phù hợp cho nhu cầu chăm sóc sức khỏe đô thị.', 'images/facilities/ankhang.png', '2025-11-18 13:14:44'),
(12, 'Phòng khám Sài Gòn', 'clinic', '132-134 Lý Thái Tổ, Q.3', '(028) 3833 5177', '7:30 - 18:00', 'Phòng khám Sài Gòn là một trong các phòng khám chuyên khoa uy tín, cung cấp dịch vụ khám bệnh đa dạng, tập trung vào sự thuận tiện và chất lượng phục vụ.', 'images/facilities/saigon.png', '2025-11-18 13:14:44'),
(13, 'Victoria Healthcare', 'clinic', '135A Đ. Nguyễn Văn Trỗi, Phường 11, Phú Nhuận, Thành phố Hồ Chí Minh', '028 3910 4545', '\"Thứ 2 – Thứ 6: 8:00 – 20:00\r\nThứ 7: 8:00 – 17:00\r\nCN: 8:00 – 17:00 PM\"\r\n', 'Victoria Healthcare theo mô hình phòng khám tiêu chuẩn Mỹ, nổi tiếng với dịch vụ thân thiện, đội ngũ bác sĩ nhiều kinh nghiệm và chăm sóc sức khỏe toàn diện.\r\n', 'images/facilities/victoria.png', '2025-11-18 13:14:44'),
(14, 'Phòng khám Đa khoa Pasteur\r\n', 'clinic', '4 Nguyễn Thông, Quận 3\r\n', '(028) 3820 6999', '07:00–20:00\r\n', 'Phòng khám Đa khoa Pasteur cung cấp dịch vụ theo tiêu chuẩn cao, đặc biệt mạnh về xét nghiệm, chẩn đoán hình ảnh và các gói khám tổng quát.\r\n', 'images/facilities/pasteur.png', '2025-11-18 13:14:44'),
(15, 'Phòng khám Quốc tế Sài Gòn', 'clinic', '99 Sương Nguyệt Ánh, Quận 1', '(028) 3925 3118', '07:00–19:30\r\n', 'Phòng khám Quốc tế Sài Gòn chú trọng vào chất lượng dịch vụ, phục vụ theo mô hình quốc tế với các chuyên khoa đa dạng, phù hợp cho người nước ngoài và người Việt.', 'images/facilities/quocte.jpg', '2025-11-18 13:14:44'),
(16, 'Phòng khám CarePlus Tân Bình', 'clinic', '2-4 Tân Hải, Tân Bình\r\n', '(028) 3622 1212', 'cả ngày ', 'CarePlus thuộc hệ thống phòng khám quốc tế theo tiêu chuẩn Singapore, mạnh về khám sức khỏe định kỳ, nhi khoa và các dịch vụ tầm soát.\r\n', 'images/facilities/careplus.png', '2025-11-18 13:14:44'),
(17, 'Phòng khám An Phước\r\n', 'clinic', '73 Giải Phóng, Hai Bà Trưng\r\n', '(024) 3558 8811', '08:00–20:00\r\n', 'Phòng khám An Phước là phòng khám đa khoa cung cấp dịch vụ khám chữa bệnh cơ bản đến nâng cao với đội ngũ y bác sĩ giàu kinh nghiệm.\r\n', 'images/facilities/anphuoc.png', '2025-11-18 13:14:44'),
(18, 'Phòng khám Medlatec\r\n', 'clinic', '42 Nghĩa Dũng, Ba Đình\r\n', '1900 565656', '24/7', 'Phòng kham Medlatec là hệ thống phòng khám và xét nghiệm nổi tiếng toàn quốc, mạnh về xét nghiệm, chẩn đoán hình ảnh và dịch vụ lấy mẫu tận nơi.\r\n', 'images/facilities/medlatec.png', '2025-11-18 13:14:44'),
(19, 'Raffles Medical Clinic\r\n', 'clinic', '285B Điện Biên Phủ, Phường Võ Thị Sáu, Quận 3, Thành phố Hồ Chí Minh\r\n', '028 3824 0777', '\"Thứ 2 – Thứ 6: 8:00 – 18:00\r\nThứ 7: 8:00 – 17:00\"\r\n', 'Raffles Medical thuộc tập đoàn Raffles Medical Group (Singapore), cung cấp dịch vụ chăm sóc sức khỏe cao cấp, đặc biệt cho cộng đồng người nước ngoài.\r\n', 'images/facilities/123456.png', '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `facility_admins`
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
-- Dumping data for table `facility_admins`
--

INSERT INTO `facility_admins` (`admin_id`, `facility_id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 1, 'Nguyễn Văn A', 'admin.bvbachmai@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW', '2025-11-18 13:14:44'),
(2, 2, 'Trần Thị B', 'admin.bv108@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW', '2025-11-18 13:14:44'),
(3, 3, 'Lê Văn C', 'admin.pkminhkhai@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW', '2025-11-18 13:14:44'),
(4, 4, 'Phạm Thị D', 'admin.pkvinhphuc@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW', '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `facility_specialty`
--

CREATE TABLE `facility_specialty` (
  `id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facility_specialty`
--

INSERT INTO `facility_specialty` (`id`, `facility_id`, `specialty_id`, `created_at`) VALUES
(1, 1, 1, '2025-11-18 13:14:44'),
(2, 1, 2, '2025-11-18 13:14:44'),
(3, 1, 4, '2025-11-18 13:14:44'),
(4, 1, 6, '2025-11-18 13:14:44'),
(5, 1, 7, '2025-11-18 13:14:44'),
(6, 2, 1, '2025-11-18 13:14:44'),
(7, 2, 3, '2025-11-18 13:14:44'),
(8, 2, 5, '2025-11-18 13:14:44'),
(9, 2, 9, '2025-11-18 13:14:44'),
(10, 3, 1, '2025-11-18 13:14:44'),
(11, 3, 2, '2025-11-18 13:14:44'),
(12, 3, 3, '2025-11-18 13:14:44'),
(13, 3, 4, '2025-11-18 13:14:44'),
(14, 3, 5, '2025-11-18 13:14:44'),
(15, 3, 6, '2025-11-18 13:14:44'),
(16, 3, 7, '2025-11-18 13:14:44'),
(17, 3, 8, '2025-11-18 13:14:44'),
(18, 3, 9, '2025-11-18 13:14:44'),
(19, 3, 10, '2025-11-18 13:14:44'),
(20, 3, 11, '2025-11-18 13:14:44'),
(21, 4, 1, '2025-11-18 13:14:44'),
(22, 4, 2, '2025-11-18 13:14:44'),
(23, 4, 3, '2025-11-18 13:14:44'),
(24, 4, 4, '2025-11-18 13:14:44'),
(25, 4, 5, '2025-11-18 13:14:44'),
(26, 4, 6, '2025-11-18 13:14:44'),
(27, 4, 7, '2025-11-18 13:14:44'),
(28, 4, 8, '2025-11-18 13:14:44'),
(29, 4, 9, '2025-11-18 13:14:44'),
(30, 4, 10, '2025-11-18 13:14:44'),
(31, 4, 11, '2025-11-18 13:14:44'),
(32, 4, 12, '2025-11-18 13:14:44'),
(33, 5, 3, '2025-11-18 13:14:44'),
(34, 5, 4, '2025-11-18 13:14:44'),
(35, 5, 5, '2025-11-18 13:14:44'),
(36, 5, 7, '2025-11-18 13:14:44'),
(37, 5, 15, '2025-11-18 13:14:44'),
(38, 6, 7, '2025-11-18 13:14:44'),
(39, 6, 4, '2025-11-18 13:14:44'),
(40, 7, 8, '2025-11-18 13:14:44'),
(41, 7, 5, '2025-11-18 13:14:44'),
(42, 8, 9, '2025-11-18 13:14:44'),
(43, 8, 3, '2025-11-18 13:14:44'),
(44, 8, 4, '2025-11-18 13:14:44'),
(45, 9, 1, '2025-11-18 13:14:44'),
(46, 10, 1, '2025-11-18 13:14:44'),
(47, 10, 4, '2025-11-18 13:14:44'),
(48, 11, 4, '2025-11-18 13:14:44'),
(49, 11, 1, '2025-11-18 13:14:44'),
(50, 11, 2, '2025-11-18 13:14:44'),
(51, 11, 5, '2025-11-18 13:14:44'),
(52, 12, 3, '2025-11-18 13:14:44'),
(53, 12, 15, '2025-11-18 13:14:44'),
(54, 13, 2, '2025-11-18 13:14:44'),
(55, 13, 13, '2025-11-18 13:14:44'),
(56, 14, 5, '2025-11-18 13:14:44'),
(57, 14, 4, '2025-11-18 13:14:44'),
(58, 15, 7, '2025-11-18 13:14:44'),
(59, 15, 4, '2025-11-18 13:14:44'),
(60, 16, 8, '2025-11-18 13:14:44'),
(61, 17, 10, '2025-11-18 13:14:44'),
(62, 17, 14, '2025-11-18 13:14:44'),
(63, 18, 6, '2025-11-18 13:14:44'),
(64, 18, 4, '2025-11-18 13:14:44'),
(65, 19, 13, '2025-11-18 13:14:44'),
(66, 19, 14, '2025-11-18 13:14:44'),
(67, 19, 15, '2025-11-18 13:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `specialty_id` int(11) NOT NULL,
  `specialty_name` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specialties`
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
-- Table structure for table `users`
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
-- Dumping data for table `users`
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
-- Structure for view `appointment_details`
--
DROP TABLE IF EXISTS `appointment_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointment_details`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`symptoms` AS `symptoms`, `a`.`status` AS `status`, `a`.`created_at` AS `created_at`, `u`.`user_id` AS `user_id`, `u`.`fullname` AS `patient_name`, `u`.`email` AS `patient_email`, `u`.`phone` AS `patient_phone`, `f`.`facility_id` AS `facility_id`, `f`.`name` AS `facility_name`, `f`.`type` AS `facility_type`, `f`.`address` AS `facility_address`, `f`.`phone` AS `facility_phone`, `s`.`specialty_id` AS `specialty_id`, `s`.`specialty_name` AS `specialty_name` FROM (((`appointments` `a` join `users` `u` on(`a`.`user_id` = `u`.`user_id`)) join `facilities` `f` on(`a`.`facility_id` = `f`.`facility_id`)) join `specialties` `s` on(`a`.`specialty_id` = `s`.`specialty_id`)) ORDER BY `a`.`appointment_date` DESC, `a`.`appointment_time` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `appointments`
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
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `facility_admins`
--
ALTER TABLE `facility_admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_facility_id` (`facility_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `facility_specialty`
--
ALTER TABLE `facility_specialty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_facility_specialty` (`facility_id`,`specialty_id`),
  ADD KEY `idx_facility_id` (`facility_id`),
  ADD KEY `idx_specialty_id` (`specialty_id`);

--
-- Indexes for table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`specialty_id`),
  ADD UNIQUE KEY `specialty_name` (`specialty_name`),
  ADD KEY `idx_specialty_name` (`specialty_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `facility_admins`
--
ALTER TABLE `facility_admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `facility_specialty`
--
ALTER TABLE `facility_specialty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `specialties`
--
ALTER TABLE `specialties`
  MODIFY `specialty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`specialty_id`) ON DELETE CASCADE;

--
-- Constraints for table `facility_admins`
--
ALTER TABLE `facility_admins`
  ADD CONSTRAINT `facility_admins_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE;

--
-- Constraints for table `facility_specialty`
--
ALTER TABLE `facility_specialty`
  ADD CONSTRAINT `facility_specialty_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facility_specialty_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`specialty_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
