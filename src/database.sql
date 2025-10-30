-- ==========================================
-- Database: MediCare - Hệ thống đặt lịch khám bệnh
-- Version: 1.0
-- Date: 2024
-- ==========================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS medicare_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE medicare_db;

-- ==========================================
-- Bảng: specialties (Chuyên khoa)
-- ==========================================
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: users (Người dùng - Bệnh nhân, Bác sĩ, Admin)
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    avatar VARCHAR(255),
    role ENUM('patient', 'doctor', 'admin') DEFAULT 'patient',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: doctors (Thông tin bác sĩ)
-- ==========================================
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialty_id INT NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    degree VARCHAR(100),
    experience_years INT DEFAULT 0,
    bio TEXT,
    consultation_fee DECIMAL(10,2),
    rating DECIMAL(3,2) DEFAULT 5.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id),
    INDEX idx_user_id (user_id),
    INDEX idx_specialty_id (specialty_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: patients (Thông tin bệnh nhân chi tiết)
-- ==========================================
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    id_card VARCHAR(20),
    blood_type VARCHAR(10),
    allergies TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    insurance_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: schedules (Lịch làm việc của bác sĩ)
-- ==========================================
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=Chủ nhật, 1=Thứ 2, ..., 6=Thứ 7',
    time_slot VARCHAR(20) NOT NULL COMMENT 'morning, afternoon, evening',
    start_time TIME,
    end_time TIME,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_day_of_week (day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: appointments (Lịch hẹn khám)
-- ==========================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_code VARCHAR(20) UNIQUE NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    notes TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    cancelled_by ENUM('patient', 'doctor', 'admin'),
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_appointment_code (appointment_code),
    INDEX idx_patient_id (patient_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: medical_records (Hồ sơ bệnh án)
-- ==========================================
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT,
    symptoms TEXT,
    treatment TEXT,
    prescription TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_appointment_id (appointment_id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_doctor_id (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: reviews (Đánh giá bác sĩ)
-- ==========================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: notifications (Thông báo)
-- ==========================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Bảng: settings (Cài đặt hệ thống)
-- ==========================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- CHÈN DỮ LIỆU MẪU
-- ==========================================

-- Chuyên khoa
INSERT INTO specialties (name, description, icon) VALUES
('Tim mạch', 'Khám và điều trị các bệnh về tim mạch, huyết áp, suy tim', 'fa-heart'),
('Nội khoa', 'Điều trị bệnh nội khoa tổng quát', 'fa-user-md'),
('Nhi khoa', 'Chăm sóc sức khỏe trẻ em từ sơ sinh đến 18 tuổi', 'fa-baby'),
('Da liễu', 'Điều trị các bệnh về da', 'fa-hand-holding-medical'),
('Sản - Phụ khoa', 'Chăm sóc sức khỏe phụ nữ và thai sản', 'fa-baby-carriage'),
('Mắt', 'Khám và điều trị các bệnh về mắt', 'fa-eye'),
('Thần kinh', 'Điều trị bệnh thần kinh', 'fa-brain'),
('Hô hấp', 'Điều trị các bệnh về phổi và đường hô hấp', 'fa-lungs'),
('Tiêu hóa', 'Điều trị bệnh lý đường tiêu hóa, gan mật', 'fa-stethoscope');

-- Tài khoản Admin
INSERT INTO users (username, email, password, full_name, phone, gender, role) VALUES
('admin', 'admin@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', '0123456789', 'male', 'admin');

-- Tài khoản Bệnh nhân
INSERT INTO users (username, email, password, full_name, phone, birth_date, gender, role) VALUES
('patient1', 'nguyen.van.an@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn An', '0912345678', '1985-05-15', 'male', 'patient'),
('patient2', 'tran.thi.binh@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Bình', '0923456789', '1990-08-22', 'female', 'patient'),
('patient3', 'le.van.cuong@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn Cường', '0934567890', '1995-12-10', 'male', 'patient'),
('patient4', 'pham.thi.dung@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Dung', '0945678901', '1988-03-05', 'female', 'patient');

-- Tài khoản Bác sĩ
INSERT INTO users (username, email, password, full_name, phone, gender, role) VALUES
('doctor1', 'nguyen.van.a@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0987654321', 'male', 'doctor'),
('doctor2', 'tran.thi.b@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0987654322', 'female', 'doctor'),
('doctor3', 'le.van.c@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0987654323', 'male', 'doctor'),
('doctor4', 'pham.thi.d@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', '0987654324', 'female', 'doctor');

-- Thông tin bệnh nhân chi tiết
INSERT INTO patients (user_id, blood_type) VALUES
(2, 'O+'),
(3, 'A+'),
(4, 'B+'),
(5, 'AB+');

-- Thông tin bác sĩ
INSERT INTO doctors (user_id, specialty_id, license_number, degree, experience_years, bio, consultation_fee, rating, total_reviews) VALUES
(6, 1, 'CC001234', 'Thạc sĩ Y khoa', 15, 'Bác sĩ Nguyễn Văn A tốt nghiệp Đại học Y Dược TP.HCM với hơn 15 năm kinh nghiệm trong điều trị các bệnh về tim mạch.', 500000, 4.9, 120),
(7, 4, 'CC002234', 'Tiến sĩ Y khoa', 12, 'Bác sĩ Trần Thị B chuyên về điều trị các bệnh da liễu ở mọi lứa tuổi.', 450000, 4.8, 95),
(8, 3, 'CC003234', 'Bác sĩ chuyên khoa', 8, 'Bác sĩ Lê Văn C chuyên chăm sóc sức khỏe trẻ em và thanh thiếu niên.', 400000, 4.7, 80),
(9, 5, 'CC004234', 'Thạc sĩ Y khoa', 20, 'Bác sĩ Phạm Thị D với 20 năm kinh nghiệm trong lĩnh vực sản phụ khoa.', 600000, 4.9, 150);

-- Lịch làm việc của bác sĩ
INSERT INTO schedules (doctor_id, day_of_week, time_slot, start_time, end_time) VALUES
(1, 1, 'morning', '08:00:00', '12:00:00'),  -- Thứ 2 sáng
(1, 1, 'afternoon', '14:00:00', '17:00:00'), -- Thứ 2 chiều
(1, 3, 'morning', '08:00:00', '12:00:00'),  -- Thứ 4 sáng
(1, 3, 'afternoon', '14:00:00', '17:00:00'), -- Thứ 4 chiều
(1, 5, 'morning', '08:00:00', '12:00:00'),  -- Thứ 6 sáng
(1, 5, 'afternoon', '14:00:00', '17:00:00'), -- Thứ 6 chiều

(2, 1, 'morning', '08:00:00', '12:00:00'),
(2, 2, 'afternoon', '14:00:00', '17:00:00'),
(2, 3, 'morning', '08:00:00', '12:00:00'),
(2, 4, 'afternoon', '14:00:00', '17:00:00'),

(3, 2, 'morning', '08:00:00', '12:00:00'),
(3, 3, 'morning', '08:00:00', '12:00:00'),
(3, 4, 'afternoon', '14:00:00', '17:00:00'),
(3, 5, 'morning', '08:00:00', '12:00:00'),

(4, 1, 'morning', '08:00:00', '12:00:00'),
(4, 2, 'afternoon', '14:00:00', '17:00:00'),
(4, 4, 'morning', '08:00:00', '12:00:00'),
(4, 5, 'afternoon', '14:00:00', '17:00:00');

-- Lịch hẹn
INSERT INTO appointments (appointment_code, patient_id, doctor_id, appointment_date, appointment_time, reason, status) VALUES
('APT2024001', 1, 1, '2024-12-15', '09:00:00', 'Đau ngực, khó thở', 'confirmed'),
('APT2024002', 2, 2, '2024-12-15', '14:30:00', 'Da nổi mẩn đỏ', 'pending'),
('APT2024003', 3, 3, '2024-12-16', '10:00:00', 'Trẻ bị sốt, ho', 'confirmed'),
('APT2024004', 4, 4, '2024-12-20', '08:30:00', 'Khám thai định kỳ', 'pending'),
('APT2024005', 1, 1, '2024-12-10', '09:00:00', 'Đau tim', 'completed'),
('APT2024006', 3, 3, '2024-12-10', '10:00:00', 'Cảm cúm', 'completed');

-- Hồ sơ bệnh án
INSERT INTO medical_records (appointment_id, patient_id, doctor_id, diagnosis, symptoms, treatment, prescription) VALUES
(5, 1, 1, 'Cao huyết áp giai đoạn 1', 'Huyết áp cao, đau đầu', 'Kê đơn thuốc điều trị cao huyết áp', 'Amlodipine 5mg, uống 1 viên/ngày, Aspirin 81mg uống 1 viên/ngày'),
(6, 3, 3, 'Cảm cúm, sốt nhẹ', 'Sốt 38°C, ho, sổ mũi', 'Kê đơn thuốc hạ sốt', 'Paracetamol 500mg khi sốt, nghỉ ngơi, uống nhiều nước');

-- Đánh giá
INSERT INTO reviews (appointment_id, doctor_id, patient_id, rating, comment) VALUES
(5, 1, 1, 5, 'Bác sĩ rất tận tình, chẩn đoán chính xác'),
(6, 3, 3, 5, 'Bác sĩ nhiều kinh nghiệm, trẻ nhỏ rất thích');

-- Thông báo
INSERT INTO notifications (user_id, type, title, message, link) VALUES
(2, 'appointment_confirmed', 'Lịch hẹn đã được xác nhận', 'BS. Nguyễn Văn A đã xác nhận lịch hẹn vào 15/12/2024 lúc 09:00', '/patient/appointments.html'),
(2, 'medical_record', 'Kết quả khám đã có', 'Kết quả khám ngày 10/12/2024 đã có sẵn', '/patient/medical-record.html');

-- Cài đặt hệ thống
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'MediCare', 'Tên hệ thống'),
('site_email', 'info@medicare.com', 'Email hệ thống'),
('site_phone', '0123 456 789', 'Số điện thoại hệ thống'),
('site_address', '123 Nguyễn Văn Linh, TP.HCM', 'Địa chỉ hệ thống'),
('min_cancel_hours', '12', 'Số giờ tối thiểu để hủy lịch hẹn'),
('min_booking_hours', '2', 'Số giờ tối thiểu để đặt lịch'),
('sms_notification', '1', 'Bật/tắt thông báo SMS (1=bật, 0=tắt)'),
('email_notification', '1', 'Bật/tắt thông báo Email (1=bật, 0=tắt)');

-- ==========================================
-- VIEWS (Khung nhìn)
-- ==========================================

-- View: Danh sách bác sĩ với chuyên khoa
CREATE OR REPLACE VIEW v_doctors_detail AS
SELECT 
    d.id,
    u.full_name,
    d.license_number,
    s.name as specialty_name,
    s.icon as specialty_icon,
    d.experience_years,
    d.bio,
    d.consultation_fee,
    d.rating,
    d.total_reviews,
    d.status,
    u.email,
    u.phone,
    u.avatar
FROM doctors d
INNER JOIN users u ON d.user_id = u.id
INNER JOIN specialties s ON d.specialty_id = s.id;

-- View: Lịch hẹn chi tiết
CREATE OR REPLACE VIEW v_appointments_detail AS
SELECT 
    a.id,
    a.appointment_code,
    a.appointment_date,
    a.appointment_time,
    a.reason,
    a.notes,
    a.status,
    pa.id as patient_id,
    u1.full_name as patient_name,
    u1.phone as patient_phone,
    u1.email as patient_email,
    d.id as doctor_id,
    u2.full_name as doctor_name,
    s.name as specialty_name,
    a.created_at,
    a.updated_at
FROM appointments a
INNER JOIN patients pa ON a.patient_id = pa.id
INNER JOIN users u1 ON pa.user_id = u1.id
INNER JOIN doctors d ON a.doctor_id = d.id
INNER JOIN users u2 ON d.user_id = u2.id
INNER JOIN specialties s ON d.specialty_id = s.id;

-- ==========================================
-- STORED PROCEDURES (Thủ tục lưu trữ)
-- ==========================================

DELIMITER //

-- Thủ tục: Tạo mã lịch hẹn tự động
CREATE PROCEDURE sp_generate_appointment_code(
    OUT code VARCHAR(20)
)
BEGIN
    DECLARE last_id INT;
    DECLARE new_code VARCHAR(20);
    
    SELECT COALESCE(MAX(id), 0) INTO last_id FROM appointments;
    SET new_code = CONCAT('APT', YEAR(NOW()), LPAD(last_id + 1, 4, '0'));
    
    SET code = new_code;
END //

-- Thủ tục: Đếm số lịch hẹn theo trạng thái
CREATE PROCEDURE sp_count_appointments_by_status(
    IN p_status VARCHAR(20),
    OUT p_count INT
)
BEGIN
    SELECT COUNT(*) INTO p_count 
    FROM appointments 
    WHERE status = p_status;
END //

-- Thủ tục: Cập nhật đánh giá trung bình của bác sĩ
CREATE PROCEDURE sp_update_doctor_rating(IN p_doctor_id INT)
BEGIN
    UPDATE doctors d
    SET 
        d.rating = (
            SELECT ROUND(AVG(rating), 2)
            FROM reviews
            WHERE doctor_id = p_doctor_id
        ),
        d.total_reviews = (
            SELECT COUNT(*)
            FROM reviews
            WHERE doctor_id = p_doctor_id
        )
    WHERE d.id = p_doctor_id;
END //

DELIMITER ;

-- ==========================================
-- TRIGGERS (Trigger)
-- ==========================================

DELIMITER //

-- Trigger: Tự động tạo mã lịch hẹn khi insert
CREATE TRIGGER trg_appointments_before_insert
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
    DECLARE generated_code VARCHAR(20);
    CALL sp_generate_appointment_code(generated_code);
    SET NEW.appointment_code = generated_code;
END //

-- Trigger: Cập nhật đánh giá bác sĩ sau khi thêm đánh giá mới
CREATE TRIGGER trg_reviews_after_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    CALL sp_update_doctor_rating(NEW.doctor_id);
END //

DELIMITER ;

-- ==========================================
-- Bảng log (Nhật ký hoạt động)
-- ==========================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- GRANT PERMISSIONS (Cấp quyền - Optional)
-- ==========================================
-- CREATE USER 'medicare_user'@'localhost' IDENTIFIED BY 'medicare_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON medicare_db.* TO 'medicare_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ==========================================
-- Kết thúc
-- ==========================================
-- Mật khẩu mặc định cho tất cả user: "password" (đã được hash bằng bcrypt)
-- Để đổi mật khẩu, sử dụng hàm password_hash() của PHP hoặc bcrypt

