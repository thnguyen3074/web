-- Thêm các cột để lưu thông tin bệnh nhân vào bảng appointments
ALTER TABLE `appointments` 
ADD COLUMN `patient_name` VARCHAR(100) NULL AFTER `user_id`,
ADD COLUMN `patient_email` VARCHAR(100) NULL AFTER `patient_name`,
ADD COLUMN `patient_phone` VARCHAR(20) NULL AFTER `patient_email`;

