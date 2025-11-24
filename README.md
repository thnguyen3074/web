# Medicare - Hệ thống đặt lịch khám bệnh trực tuyến

Hệ thống quản lý và đặt lịch khám bệnh trực tuyến cho bệnh viện và phòng khám. Dự án được phát triển bằng PHP, MySQL, HTML, CSS và JavaScript.

## 📋 Mục lục

- [Tính năng](#tính-năng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Cấu hình](#cấu-hình)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Database Schema](#database-schema)
- [Bảo mật](#bảo-mật)

## ✨ Tính năng

### 👤 Người dùng (User)
- **Đăng ký/Đăng nhập**: Tạo tài khoản và đăng nhập vào hệ thống
- **Tìm kiếm**: Tìm kiếm bệnh viện, phòng khám, chuyên khoa theo tên hoặc địa chỉ
- **Xem danh sách**: 
  - Danh sách bệnh viện và phòng khám
  - Danh sách chuyên khoa
  - Chi tiết cơ sở y tế
- **Đặt lịch khám**: 
  - Đặt lịch khám tại bệnh viện/phòng khám
  - Chọn chuyên khoa và thời gian khám
  - Đặt lịch không cần đăng nhập (guest booking)
  - Xác nhận thông tin trước khi đặt lịch
- **Quản lý lịch hẹn**: 
  - Xem danh sách lịch hẹn của mình
  - Hủy lịch hẹn (nếu chưa được xử lý)
  - Xem chi tiết lịch hẹn
- **Quản lý tài khoản**: 
  - Xem và cập nhật thông tin cá nhân
  - Đổi mật khẩu

### 🏥 Quản trị viên cơ sở y tế (Facility Admin)
- **Dashboard**: Xem thống kê lịch hẹn của cơ sở
- **Quản lý lịch hẹn**: 
  - Xem danh sách lịch hẹn
  - Tìm kiếm và lọc lịch hẹn
  - Cập nhật trạng thái lịch hẹn (pending, confirmed, completed, canceled)
  - Xem chi tiết lịch hẹn
- **Quản lý chuyên khoa**: 
  - Thêm/xóa chuyên khoa cho cơ sở
- **Quản lý thông tin cơ sở**: 
  - Cập nhật thông tin cơ sở y tế
  - Upload hình ảnh
- **Quản lý tài khoản**: 
  - Cập nhật thông tin cá nhân
  - Đổi mật khẩu

### 🔐 Quản trị viên hệ thống (Admin)
- **Dashboard**: Xem thống kê tổng quan hệ thống
- **Quản lý cơ sở y tế**: 
  - Thêm/sửa/xóa bệnh viện và phòng khám
  - Upload hình ảnh
  - Tạo tài khoản quản trị viên cho cơ sở
- **Quản lý chuyên khoa**: 
  - Thêm/sửa/xóa chuyên khoa
  - Upload icon chuyên khoa
- **Quản lý lịch hẹn**: 
  - Xem tất cả lịch hẹn trong hệ thống
  - Tìm kiếm và lọc lịch hẹn
  - Cập nhật trạng thái lịch hẹn
  - Xem chi tiết lịch hẹn
- **Quản lý người dùng**: 
  - Xem danh sách người dùng
  - Tìm kiếm người dùng
  - Cập nhật thông tin người dùng
- **Quản lý quản trị viên cơ sở**: 
  - Thêm/sửa/xóa quản trị viên cơ sở y tế
  - Gán quản trị viên cho cơ sở

## 💻 Yêu cầu hệ thống

- **PHP**: >= 7.4
- **MySQL/MariaDB**: >= 10.4
- **Web Server**: Apache (XAMPP, WAMP, LAMP) hoặc Nginx
- **PHP Extensions**: 
  - mysqli
  - mbstring
  - gd (cho xử lý hình ảnh)

## 🚀 Cài đặt

### Bước 1: Clone hoặc tải dự án

```bash
git clone <repository-url>
cd web
```

### Bước 2: Cấu hình database

1. Tạo database mới trong phpMyAdmin hoặc MySQL:
```sql
CREATE DATABASE medicare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import file SQL:
```bash
# Sử dụng phpMyAdmin hoặc command line
mysql -u root -p medicare_db < medicare_db.sql
```

Hoặc import file `medicare_db.sql` trực tiếp trong phpMyAdmin.

### Bước 3: Cấu hình kết nối database

Mở file `config.php` và cập nhật thông tin kết nối:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'medicare_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### Bước 4: Cấu hình web server

#### XAMPP/WAMP:
- Đặt thư mục dự án vào `htdocs` (XAMPP) hoặc `www` (WAMP)
- Truy cập: `http://localhost/web`

### Bước 5: Phân quyền thư mục

Đảm bảo thư mục `images/` có quyền ghi:

```bash
chmod -R 755 images/
chmod -R 755 images/facilities/
chmod -R 755 images/specialties/
```

## 📁 Cấu trúc thư mục

```
web/
├── admin/                      # Trang quản trị hệ thống
│   ├── admin-dashboard.php
│   ├── admin-facilities.php
│   ├── admin-appointments.php
│   ├── admin-users.php
│   ├── admin-admins.php
│   ├── admin-specialties.php
│   ├── admin-login.php
│   ├── admin-header.php
│   ├── admin-footer.php
│   ├── admin.css
│   └── admin.js
├── facility-admin/            # Trang quản trị cơ sở y tế
│   ├── facility-admin-dashboard.php
│   ├── facility-admin-appointments.php
│   ├── facility-admin-specialties.php
│   ├── facility-admin-facility.php
│   ├── facility-admin-profile.php
│   ├── facility-admin-login.php
│   ├── facility-admin-header.php
│   └── facility-admin-footer.php
├── images/                    # Thư mục chứa hình ảnh
│   ├── facilities/           # Hình ảnh cơ sở y tế
│   ├── specialties/           # Icon chuyên khoa
│   └── users/                 # Avatar người dùng
├── ui-html/                   # File HTML mẫu (không sử dụng)
├── About.php                  # Trang giới thiệu
├── Booking.php                # Trang đặt lịch
├── BookingConfirm.php         # Trang xác nhận đặt lịch
├── BookingSuccess.php         # Trang đặt lịch thành công
├── booking_process.php        # Xử lý đặt lịch
├── Contact.php                # Trang liên hệ
├── contact_process.php        # Xử lý form liên hệ
├── Facility.php               # Danh sách cơ sở y tế
├── FacilityDetail.php         # Chi tiết cơ sở y tế
├── Specialty.php              # Danh sách chuyên khoa
├── SpecialtyResult.php        # Kết quả tìm kiếm theo chuyên khoa
├── search.php                 # Trang tìm kiếm
├── index.php                  # Trang chủ
├── login.php                  # Trang đăng nhập
├── login_process.php          # Xử lý đăng nhập
├── register.php               # Trang đăng ký
├── register_process.php       # Xử lý đăng ký
├── logout.php                 # Xử lý đăng xuất
├── UserProfile.php            # Trang hồ sơ người dùng
├── update_profile.php         # Xử lý cập nhật profile
├── change_password.php        # Trang đổi mật khẩu
├── change_password_process.php # Xử lý đổi mật khẩu
├── MyAppointments.php         # Lịch hẹn của tôi
├── header.php                 # Header component
├── footer.php                 # Footer component
├── config.php                 # Cấu hình database
├── style.css                  # CSS cho trang user
├── main.js                    # JavaScript cho trang user
├── medicare_db.sql            # File SQL database
└── README.md                  # File này
```

## ⚙️ Cấu hình

### Database Configuration

File `config.php` chứa cấu hình kết nối database:

```php
define('DB_HOST', 'localhost');      // Host database
define('DB_NAME', 'medicare_db');    // Tên database
define('DB_USER', 'root');           // Username
define('DB_PASS', '');               // Password
define('DB_CHARSET', 'utf8mb4');     // Charset
```

### Session Configuration

Hệ thống sử dụng PHP sessions để quản lý đăng nhập. Đảm bảo `session_start()` được gọi trước khi sử dụng session.

## 📖 Hướng dẫn sử dụng

### Tài khoản mặc định

Sau khi import database, hệ thống sẽ tự động tạo tài khoản admin mặc định:

- **Email**: `admin@medicare.vn`
- **Mật khẩu**: `admin123`

### Quy trình đặt lịch

1. **Người dùng chưa đăng nhập**:
   - Tìm kiếm hoặc duyệt danh sách cơ sở y tế
   - Chọn cơ sở y tế và chuyên khoa
   - Điền thông tin cá nhân và chọn thời gian
   - Xác nhận và đặt lịch

2. **Người dùng đã đăng nhập**:
   - Đăng nhập vào hệ thống
   - Tìm kiếm hoặc duyệt danh sách cơ sở y tế
   - Chọn cơ sở y tế và chuyên khoa
   - Điền thông tin (có thể tự động điền từ tài khoản)
   - Xác nhận và đặt lịch
   - Xem và quản lý lịch hẹn trong "Lịch hẹn của tôi"

### Quản lý lịch hẹn

- **Trạng thái lịch hẹn**:
  - `pending`: Chờ xác nhận
  - `confirmed`: Đã xác nhận
  - `completed`: Đã hoàn thành
  - `canceled`: Đã hủy

- **Quyền hủy lịch hẹn**:
  - Người dùng chỉ có thể hủy lịch hẹn ở trạng thái `pending` hoặc `confirmed`
  - Quản trị viên có thể cập nhật trạng thái bất kỳ

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP**: >= 7.4 (Procedural style)
- **MySQLi**: Kết nối database (procedural style, không dùng PDO hoặc OOP)
- **Session**: Quản lý đăng nhập

### Frontend
- **HTML5**: Cấu trúc trang
- **CSS3**: Styling và responsive design
- **JavaScript (Vanilla)**: Tương tác người dùng, validation client-side

### Database
- **MySQL/MariaDB**: >= 10.4
- **Charset**: UTF8MB4 (hỗ trợ đầy đủ Unicode)

### Security
- **Password Hashing**: `password_hash()` với `PASSWORD_DEFAULT`
- **SQL Injection Prevention**: `mysqli_real_escape_string()`
- **XSS Prevention**: `htmlspecialchars()`
- **Session Security**: Kiểm tra đăng nhập trên mọi trang admin

## 🗄️ Database Schema

### Các bảng chính:

- **users**: Thông tin người dùng
- **admins**: Quản trị viên hệ thống
- **facility_admins**: Quản trị viên cơ sở y tế
- **facilities**: Cơ sở y tế (bệnh viện, phòng khám)
- **specialties**: Chuyên khoa
- **facility_specialty**: Liên kết giữa cơ sở y tế và chuyên khoa
- **appointments**: Lịch hẹn khám bệnh
- **contact_messages**: Tin nhắn liên hệ

### Quan hệ:

- `appointments.user_id` → `users.user_id` (NULL cho guest booking)
- `appointments.facility_id` → `facilities.facility_id`
- `appointments.specialty_id` → `specialties.specialty_id`
- `facility_admins.facility_id` → `facilities.facility_id`
- `facility_specialty.facility_id` → `facilities.facility_id`
- `facility_specialty.specialty_id` → `specialties.specialty_id`

## 🔒 Bảo mật

### Đã triển khai:

1. **SQL Injection Prevention**:
   - Sử dụng `mysqli_real_escape_string()` cho tất cả input
   - Prepared statements cho các query phức tạp (nếu cần)

2. **XSS Prevention**:
   - Sử dụng `htmlspecialchars()` khi hiển thị dữ liệu từ database

3. **Password Security**:
   - Hash mật khẩu bằng `password_hash()` với `PASSWORD_DEFAULT`
   - Verify bằng `password_verify()`

4. **Session Security**:
   - Kiểm tra đăng nhập trên mọi trang admin
   - Kiểm tra quyền truy cập (facility admin chỉ xem appointments của cơ sở mình)

5. **File Upload Security**:
   - Kiểm tra loại file (chỉ JPG, PNG, GIF)
   - Giới hạn kích thước file (5MB)
   - Tạo tên file unique để tránh conflict

6. **Input Validation**:
   - Server-side validation cho tất cả form
   - Client-side validation để cải thiện UX (không thay thế server-side)
