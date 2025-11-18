-- ====================================================================
-- MEDICARE DATABASE - MySQL Database Schema
-- ====================================================================
-- Database: medicare_db
-- Character Set: UTF8MB4
-- Collation: utf8mb4_unicode_ci
-- Storage Engine: InnoDB
-- ====================================================================

-- Drop database if exists (use with caution in production)
-- DROP DATABASE IF EXISTS medicare_db;

-- Create database
CREATE DATABASE IF NOT EXISTS medicare_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE medicare_db;

-- ====================================================================
-- 1. USERS TABLE - Người dùng đăng ký tài khoản
-- ====================================================================
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 2. FACILITIES TABLE - Cơ sở y tế (Bệnh viện/Phòng khám)
-- ====================================================================
DROP TABLE IF EXISTS facilities;

CREATE TABLE facilities (
    facility_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    type ENUM('hospital', 'clinic') NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    working_hours VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 3. SPECIALTIES TABLE - Danh sách chuyên khoa
-- ====================================================================
DROP TABLE IF EXISTS specialties;

CREATE TABLE specialties (
    specialty_id INT AUTO_INCREMENT PRIMARY KEY,
    specialty_name VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_specialty_name (specialty_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 4. FACILITY_SPECIALTY TABLE - Liên kết nhiều-nhiều
-- ====================================================================
DROP TABLE IF EXISTS facility_specialty;

CREATE TABLE facility_specialty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    specialty_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(facility_id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(specialty_id) ON DELETE CASCADE,
    UNIQUE KEY unique_facility_specialty (facility_id, specialty_id),
    INDEX idx_facility_id (facility_id),
    INDEX idx_specialty_id (specialty_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 5. APPOINTMENTS TABLE - Lịch hẹn
-- ====================================================================
DROP TABLE IF EXISTS appointments;

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    facility_id INT NOT NULL,
    specialty_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    symptoms TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'canceled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (facility_id) REFERENCES facilities(facility_id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(specialty_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_facility_id (facility_id),
    INDEX idx_specialty_id (specialty_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_date_time (appointment_date, appointment_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 6. ADMINS TABLE - Quản trị viên tổng
-- ====================================================================
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 7. FACILITY_ADMINS TABLE - Quản trị viên của từng cơ sở y tế
-- ====================================================================
DROP TABLE IF EXISTS facility_admins;

CREATE TABLE facility_admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(facility_id) ON DELETE CASCADE,
    INDEX idx_facility_id (facility_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 8. CONTACT_MESSAGES TABLE - Tin nhắn liên hệ
-- ====================================================================
DROP TABLE IF EXISTS contact_messages;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 9. INSERT DEMO DATA - Dữ liệu mẫu
-- ====================================================================

-- Insert Specialties (Chuyên khoa)
INSERT INTO specialties (specialty_name, icon) VALUES
('Tim mạch', NULL),
('Da liễu', NULL),
('Nhi khoa', NULL),
('Nội tổng quát', NULL),
('Tai - Mũi - Họng', NULL),
('Thần kinh', NULL),
('Mắt', NULL),
('Nha khoa', NULL),
('Sản phụ khoa', NULL),
('Cơ xương khớp', NULL),
('Ung bướu', NULL),
('Tâm thần', NULL),
('Y học cổ truyền', NULL),
('Phục hồi chức năng', NULL),
('Dinh dưỡng', NULL);

-- Insert Facilities (Cơ sở y tế)
INSERT INTO facilities (name, type, address, phone, working_hours, description) VALUES
-- Bệnh viện
('Bệnh viện Quốc tế Medicare', 'hospital', '123 Đường Sức Khỏe, Quận 1, TP.HCM', '(028) 1234 5678', '7:00 - 21:00', 'Bệnh viện quốc tế với đội ngũ bác sĩ giàu kinh nghiệm, trang thiết bị hiện đại.'),
('Bệnh viện Đa khoa Tâm Đức', 'hospital', '68 Lý Thường Kiệt, Hà Nội', '(024) 9876 5432', '7:30 - 19:30', 'Bệnh viện đa khoa uy tín tại Hà Nội.'),
('Bệnh viện Chợ Rẫy', 'hospital', '201B Nguyễn Chí Thanh, Quận 5, TP.HCM', '(028) 3855 4137', '24/7', 'Bệnh viện đa khoa trung ương lớn nhất miền Nam.'),
('Bệnh viện Bạch Mai', 'hospital', '78 Giải Phóng, Hai Bà Trưng, Hà Nội', '(024) 3869 3731', '24/7', 'Bệnh viện đa khoa trung ương hàng đầu Việt Nam.'),
('Bệnh viện Nhi Đồng 1', 'hospital', '341 Sư Vạn Hạnh, Quận 10, TP.HCM', '(028) 3927 1119', '7:00 - 20:00', 'Bệnh viện chuyên khoa nhi hàng đầu phía Nam.'),
('Bệnh viện Mắt TP.HCM', 'hospital', '280 Điện Biên Phủ, Quận 3, TP.HCM', '(028) 3930 6666', '7:00 - 20:00', 'Bệnh viện chuyên khoa mắt uy tín.'),
('Bệnh viện Răng Hàm Mặt TP.HCM', 'hospital', '201A Nguyễn Chí Thanh, Quận 5, TP.HCM', '(028) 3855 6731', '7:00 - 17:00', 'Bệnh viện chuyên khoa răng hàm mặt.'),
('Bệnh viện Phụ sản Hà Nội', 'hospital', '929 La Thành, Đống Đa, Hà Nội', '(024) 3834 3181', '24/7', 'Bệnh viện chuyên khoa sản phụ khoa hàng đầu.'),
-- Phòng khám
('Phòng khám Tim mạch An Tâm', 'clinic', '99 Phan Đình Phùng, Huế', '(0234) 123 456', '8:00 - 20:00', 'Phòng khám chuyên khoa tim mạch.'),
('Phòng khám Tim Sofia', 'clinic', '89 Pasteur, Quận 1, TP.HCM', '(028) 7777 8888', '8:00 - 17:30', 'Phòng khám chuyên khoa tim mạch và nội tổng quát.'),
('Phòng khám Đa khoa Quốc tế', 'clinic', '123 Nguyễn Văn Cừ, Quận 5, TP.HCM', '(028) 3838 1234', '8:00 - 20:00', 'Phòng khám đa khoa với nhiều chuyên khoa.'),
('Phòng khám Nhi đồng Sunshine', 'clinic', '456 Lê Lợi, Quận 1, TP.HCM', '(028) 3829 5678', '7:30 - 18:00', 'Phòng khám chuyên khoa nhi.'),
('Phòng khám Da liễu Thẩm mỹ', 'clinic', '789 Đường 3/2, Quận 10, TP.HCM', '(028) 3862 9999', '8:00 - 20:00', 'Phòng khám chuyên khoa da liễu và thẩm mỹ.'),
('Phòng khám Tai Mũi Họng Sài Gòn', 'clinic', '321 Trần Hưng Đạo, Quận 5, TP.HCM', '(028) 3927 8888', '7:00 - 19:00', 'Phòng khám chuyên khoa tai mũi họng.'),
('Phòng khám Mắt Quốc tế', 'clinic', '654 Nguyễn Trãi, Thanh Xuân, Hà Nội', '(024) 3556 1234', '8:00 - 18:00', 'Phòng khám chuyên khoa mắt với công nghệ hiện đại.'),
('Phòng khám Nha khoa Smile', 'clinic', '147 Lý Tự Trọng, Quận 1, TP.HCM', '(028) 3825 6789', '8:00 - 20:00', 'Phòng khám nha khoa với đội ngũ bác sĩ chuyên nghiệp.'),
('Phòng khám Cơ xương khớp', 'clinic', '258 Hoàng Diệu, Quận 4, TP.HCM', '(028) 3940 1234', '7:30 - 17:30', 'Phòng khám chuyên khoa cơ xương khớp.'),
('Phòng khám Thần kinh', 'clinic', '369 Võ Văn Tần, Quận 3, TP.HCM', '(028) 3930 5678', '8:00 - 17:00', 'Phòng khám chuyên khoa thần kinh.'),
('Phòng khám Y học cổ truyền', 'clinic', '159 Nguyễn Thái Học, Ba Đình, Hà Nội', '(024) 3823 4567', '7:00 - 19:00', 'Phòng khám y học cổ truyền và châm cứu.');

-- Insert Facility_Specialty (Liên kết cơ sở y tế với chuyên khoa)
INSERT INTO facility_specialty (facility_id, specialty_id) VALUES
-- Bệnh viện Quốc tế Medicare (1): Tim mạch, Da liễu, Nội tổng quát, Thần kinh, Mắt
(1, 1), (1, 2), (1, 4), (1, 6), (1, 7),
-- Bệnh viện Đa khoa Tâm Đức (2): Tim mạch, Nhi khoa, Tai - Mũi - Họng, Sản phụ khoa
(2, 1), (2, 3), (2, 5), (2, 9),
-- Bệnh viện Chợ Rẫy (3): Tất cả chuyên khoa chính
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9), (3, 10), (3, 11),
-- Bệnh viện Bạch Mai (4): Tất cả chuyên khoa chính
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6), (4, 7), (4, 8), (4, 9), (4, 10), (4, 11), (4, 12),
-- Bệnh viện Nhi Đồng 1 (5): Nhi khoa, Nội tổng quát, Tai - Mũi - Họng, Mắt, Dinh dưỡng
(5, 3), (5, 4), (5, 5), (5, 7), (5, 15),
-- Bệnh viện Mắt TP.HCM (6): Mắt, Nội tổng quát
(6, 7), (6, 4),
-- Bệnh viện Răng Hàm Mặt TP.HCM (7): Nha khoa, Tai - Mũi - Họng
(7, 8), (7, 5),
-- Bệnh viện Phụ sản Hà Nội (8): Sản phụ khoa, Nhi khoa, Nội tổng quát
(8, 9), (8, 3), (8, 4),
-- Phòng khám Tim mạch An Tâm (9): Tim mạch
(9, 1),
-- Phòng khám Tim Sofia (10): Tim mạch, Nội tổng quát
(10, 1), (10, 4),
-- Phòng khám Đa khoa Quốc tế (11): Nội tổng quát, Tim mạch, Da liễu, Tai - Mũi - Họng
(11, 4), (11, 1), (11, 2), (11, 5),
-- Phòng khám Nhi đồng Sunshine (12): Nhi khoa, Dinh dưỡng
(12, 3), (12, 15),
-- Phòng khám Da liễu Thẩm mỹ (13): Da liễu, Y học cổ truyền
(13, 2), (13, 13),
-- Phòng khám Tai Mũi Họng Sài Gòn (14): Tai - Mũi - Họng, Nội tổng quát
(14, 5), (14, 4),
-- Phòng khám Mắt Quốc tế (15): Mắt, Nội tổng quát
(15, 7), (15, 4),
-- Phòng khám Nha khoa Smile (16): Nha khoa
(16, 8),
-- Phòng khám Cơ xương khớp (17): Cơ xương khớp, Phục hồi chức năng
(17, 10), (17, 14),
-- Phòng khám Thần kinh (18): Thần kinh, Nội tổng quát
(18, 6), (18, 4),
-- Phòng khám Y học cổ truyền (19): Y học cổ truyền, Phục hồi chức năng, Dinh dưỡng
(19, 13), (19, 14), (19, 15);

-- Insert Users (Người dùng demo)
-- Password: "password123" (hashed with bcrypt - demo only, use proper hashing in production)
INSERT INTO users (fullname, email, phone, password) VALUES
('Nguyễn Văn A', 'nguyenvana@email.com', '0901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Trần Thị B', 'tranthib@email.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Lê Văn C', 'levanc@email.com', '0912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Phạm Thị D', 'phamthid@email.com', '0923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Hoàng Văn E', 'hoangvane@email.com', '0934567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Võ Thị F', 'vothif@email.com', '0945678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Đặng Văn G', 'dangvang@email.com', '0956789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Bùi Thị H', 'buithih@email.com', '0967890123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ngô Văn I', 'ngovani@email.com', '0978901234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Lý Thị K', 'lythik@email.com', '0989012345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert Admins (Quản trị viên demo)
-- Password cho tất cả admin: "admin123"
-- Hash được tạo bằng password_hash() với PASSWORD_DEFAULT
INSERT INTO admins (email, password, name) VALUES
('admin@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Administrator'),
('superadmin@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Super Administrator'),
('manager@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Manager'),
('support@medicare.vn', '$2y$10$crrPnip3HMNWAl4kFgwaH.5ajeh8aPLrwn2MicSAjnEvivOqZ4rPa', 'Support Staff');

-- Insert Facility Admins (Quản trị viên cơ sở y tế demo)
-- Password cho tất cả facility admin: "facility123"
-- Hash được tạo bằng password_hash() với PASSWORD_DEFAULT
INSERT INTO facility_admins (facility_id, fullname, email, password) VALUES
(1, 'Nguyễn Văn A', 'admin.bvbachmai@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW'),
(2, 'Trần Thị B', 'admin.bv108@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW'),
(3, 'Lê Văn C', 'admin.pkminhkhai@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW'),
(4, 'Phạm Thị D', 'admin.pkvinhphuc@medicare.vn', '$2y$10$xKpfEtcbWaNgWA2.D8F/.e0MbGBXtDzlwirlbW9AbB8Mkhx/KrnaW');

-- Insert Appointments (Lịch hẹn demo)
INSERT INTO appointments (user_id, facility_id, specialty_id, appointment_date, appointment_time, symptoms, status) VALUES
-- Lịch hẹn sắp tới (pending/confirmed)
(1, 1, 1, '2024-12-20', '08:00:00', 'Đau ngực, khó thở', 'pending'),
(2, 2, 3, '2024-12-25', '09:30:00', 'Nổi mẩn đỏ trên da', 'confirmed'),
(1, 3, 1, '2024-12-22', '14:00:00', 'Khám tim định kỳ', 'confirmed'),
(4, 5, 3, '2024-12-21', '10:30:00', 'Trẻ sốt cao, ho nhiều', 'pending'),
(5, 6, 7, '2024-12-23', '15:00:00', 'Mắt mờ, nhức mắt', 'confirmed'),
(6, 11, 4, '2024-12-24', '09:00:00', 'Khám tổng quát', 'pending'),
(7, 14, 5, '2024-12-26', '11:00:00', 'Đau họng, nghẹt mũi', 'confirmed'),
(8, 16, 8, '2024-12-27', '14:30:00', 'Đau răng, sưng nướu', 'pending'),
-- Lịch hẹn đã hoàn thành
(3, 9, 1, '2024-12-10', '10:00:00', 'Khám định kỳ', 'completed'),
(2, 13, 2, '2024-12-05', '16:00:00', 'Nổi mụn, ngứa da', 'completed'),
(9, 15, 7, '2024-12-08', '13:00:00', 'Khám mắt định kỳ', 'completed'),
(10, 17, 10, '2024-12-12', '09:00:00', 'Đau khớp gối', 'completed'),
-- Lịch hẹn đã hủy
(4, 2, 1, '2024-11-28', '08:30:00', 'Đau tim', 'canceled'),
(5, 12, 3, '2024-12-01', '10:00:00', 'Trẻ ho, sốt', 'canceled');

-- ====================================================================
-- 7. TRIGGERS - Tự động cập nhật timestamp
-- ====================================================================

-- Trigger: Cập nhật updated_at khi status thay đổi
DROP TRIGGER IF EXISTS update_appointment_status;

DELIMITER $$

CREATE TRIGGER update_appointment_status
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END$$

DELIMITER ;

-- ====================================================================
-- 8. VIEWS - View danh sách lịch hẹn chi tiết
-- ====================================================================

DROP VIEW IF EXISTS appointment_details;

CREATE VIEW appointment_details AS
SELECT 
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.symptoms,
    a.status,
    a.created_at,
    u.user_id,
    u.fullname AS patient_name,
    u.email AS patient_email,
    u.phone AS patient_phone,
    f.facility_id,
    f.name AS facility_name,
    f.type AS facility_type,
    f.address AS facility_address,
    f.phone AS facility_phone,
    s.specialty_id,
    s.specialty_name
FROM appointments a
INNER JOIN users u ON a.user_id = u.user_id
INNER JOIN facilities f ON a.facility_id = f.facility_id
INNER JOIN specialties s ON a.specialty_id = s.specialty_id
ORDER BY a.appointment_date DESC, a.appointment_time DESC;

-- ====================================================================
-- 9. STORED PROCEDURES - Tạo lịch hẹn với kiểm tra trùng giờ
-- ====================================================================

DROP PROCEDURE IF EXISTS create_appointment;

DELIMITER $$

CREATE PROCEDURE create_appointment(
    IN p_user_id INT,
    IN p_facility_id INT,
    IN p_specialty_id INT,
    IN p_appointment_date DATE,
    IN p_appointment_time TIME,
    IN p_symptoms TEXT,
    OUT p_appointment_id INT,
    OUT p_result_message VARCHAR(255)
)
BEGIN
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

DELIMITER ;

-- ====================================================================
-- 10. ADDITIONAL USEFUL PROCEDURES
-- ====================================================================

-- Procedure: Cập nhật trạng thái lịch hẹn
DROP PROCEDURE IF EXISTS update_appointment_status_proc;

DELIMITER $$

CREATE PROCEDURE update_appointment_status_proc(
    IN p_appointment_id INT,
    IN p_status VARCHAR(20),
    OUT p_result_message VARCHAR(255)
)
BEGIN
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

-- ====================================================================
-- END OF DATABASE SETUP
-- ====================================================================

-- Verify tables
SELECT 'Database setup completed successfully!' AS message;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_facilities FROM facilities;
SELECT COUNT(*) AS total_specialties FROM specialties;
SELECT COUNT(*) AS total_appointments FROM appointments;

