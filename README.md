# 💼 Website Tìm Kiếm Việc Làm

Dự án môn học xây dựng website tìm kiếm việc làm fullstack với PHP thuần, triển khai trên XAMPP. Hệ thống cung cấp đầy đủ chức năng cho **Ứng viên** (tìm việc, nộp CV, quản lý hồ sơ), **Nhà tuyển dụng** (đăng tin, quản lý ứng tuyển) và **Quản trị viên** (duyệt tin, quản lý người dùng, thống kê).

---

## 👥 Thành viên

**Lớp:** `[Tên lớp]`

| STT | Họ và tên           | Mã sinh viên    | Vai trò     |
| --- | ------------------- | --------------- | ----------- |
| 1   | `[Nguyễn Bá Đức]`   | `[23810310420]` | Nhóm trưởng |
| 2   | `[Nguyễn Văn Đại]`  | `[23810310422]` | Thành viên  |
| 3   | `[Nguyễn Ngọc Sơn]` | `[23810310424]` | Thành viên  |

---

## 📝 Phân công công việc

**Giao diện Ứng viên (Candidate) — `[Tên]`**
Tìm kiếm việc làm, Xem chi tiết tin tuyển dụng, Nộp CV, Quản lý hồ sơ cá nhân

**Giao diện Nhà tuyển dụng (Employer) — `[Tên]`**
Đăng tin tuyển dụng, Quản lý danh sách ứng viên, Quản lý tin đăng

**Hệ thống Quản trị & Hỗ trợ — `[Tên]`**
Trang chủ, Đăng nhập/Đăng ký, Quên mật khẩu, Admin Dashboard, Duyệt tin tuyển dụng

---

## 📋 Mục lục

- [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
- [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
- [Cơ sở dữ liệu](#-cơ-sở-dữ-liệu)
- [Chức năng chính](#-chức-năng-chính)
- [Hướng dẫn cài đặt](#-hướng-dẫn-cài-đặt)
- [Tài liệu SRS](#-tài-liệu-srs)

---

## 🛠 Công nghệ sử dụng

| Thành phần  | Công nghệ               | Lý do lựa chọn                                                                                                                                   |
| ----------- | ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| Ngôn ngữ    | PHP 8.x (thuần)         | Dễ triển khai, cú pháp trực quan, giúp sinh viên nắm vững kiến thức cốt lõi về backend, session và luồng xử lý web động trước khi học Framework. |
| Database    | MariaDB 10.4            | Hệ quản trị CSDL quan hệ mạnh mẽ, mã nguồn mở, tích hợp sẵn trong XAMPP, hỗ trợ truy vấn SQL chuẩn và tối ưu tốt với PHP.                        |
| Web Server  | Apache (XAMPP)          | Môi trường phát triển cục bộ trọn gói, dễ cài đặt và chạy ngay trên Windows mà không cần cấu hình service rời rạc.                               |
| Frontend    | HTML5, JS, Bootstrap 5  | Chuẩn web hiện đại, hỗ trợ dàn trang Grid/Flexbox và responsive UI trên di động nhanh chóng.                                                     |
| Font & Icon | Montserrat, FontAwesome | Phông chữ hiện đại cùng bộ icon đầy đủ giúp giao diện chuyên nghiệp, thân thiện và trải nghiệm người dùng tốt hơn.                               |
| Email       | PHPMailer (SMTP)        | Thư viện gửi email ổn định qua Gmail SMTP, khắc phục giới hạn của hàm `mail()` mặc định trên localhost.                                          |
| Upload CV   | PHP File Upload         | Cho phép ứng viên tải lên CV định dạng PDF/DOCX, kiểm tra loại file và giới hạn dung lượng phía server.                                          |

---

## 📁 Cấu trúc thư mục

```
WEBKIEMTHU/
├── index.php                       # Entry point – điều hướng chính
├── test_analyze.php                # Script kiểm thử phân tích
├── database.sql                    # 💾 File dump cơ sở dữ liệu
├── db.opt                          # Cấu hình tuỳ chọn database
├── composer.json                   # Khai báo dependencies PHP
├── composer.lock                   # Khóa phiên bản dependencies
├── .gitignore                      # Bỏ qua file khi commit
├── .editorconfig                   # Chuẩn hoá định dạng code editor
│
├── api/                            # 🔌 REST API Backend (PHP thuần)
│   ├── .htaccess                   # Rewrite rules cho Apache
│   ├── index.php                   # Entry point API
│   ├── auth.php                    # API xác thực (đăng nhập/đăng ký/token)
│   ├── jobs.php                    # API tin tuyển dụng (CRUD + lọc)
│   ├── applications.php            # API đơn ứng tuyển
│   ├── candidates.php              # API thông tin ứng viên
│   ├── analyze.php                 # API phân tích / thống kê dữ liệu
│   ├── admin_jobs.php              # API quản lý tin tuyển dụng (Admin)
│   ├── admin_applications.php      # API quản lý đơn ứng tuyển (Admin)
│   ├── admin_users.php             # API quản lý người dùng (Admin)
│   └── admin_reports.php           # API báo cáo & thống kê (Admin)
│
├── middleware/                     # 🛡 Middleware xác thực & bảo mật
│   ├── auth.php                    # Kiểm tra JWT / session người dùng
│   ├── admin.php                   # Kiểm tra quyền quản trị viên
│   └── cors.php                    # Xử lý CORS cho API
│
├── public/                         # 🌐 Giao diện Frontend (PHP render)
│   ├── js/                         # JavaScript phía client
│   ├── dangnhap.php                # Trang đăng nhập ứng viên / NTD
│   ├── dangky.php                  # Trang đăng ký tài khoản
│   ├── timkiem.php                 # Trang tìm kiếm việc làm
│   ├── chitietcongviec.php         # Trang chi tiết tin tuyển dụng
│   ├── dangtin.php                 # Trang đăng tin tuyển dụng (NTD)
│   ├── quanlytindang.php           # Trang quản lý tin đã đăng (NTD)
│   ├── suatin.php                  # Trang chỉnh sửa tin tuyển dụng
│   ├── xemungvien.php              # Trang xem danh sách ứng viên (NTD)
│   ├── trangthai.php               # Trang theo dõi trạng thái ứng tuyển
│   ├── utthanhcong.php             # Trang thông báo ứng tuyển thành công
│   ├── admin_dangnhap.php          # Trang đăng nhập quản trị viên
│   └── admin.php                   # Trang Dashboard quản trị (Admin Panel)
│
├── config/                         # ⚙️ Cấu hình hệ thống
├── cache/                          # 🗃 Bộ nhớ đệm (cache)
├── logs/                           # 📋 File log hệ thống
└── vendor/                         # 📦 Thư viện bên thứ ba (Composer)
```

---

## 🗄 Cơ sở dữ liệu

**Database:** `vieclamphp` · **Engine:** InnoDB · **Charset:** UTF-8

### Sơ đồ các bảng

```
┌──────────────────────┐     ┌──────────────────────┐
│      tbl_admin       │     │    tbl_ungvien       │
│   (Quản trị viên)    │     │     (Ứng viên)        │
├──────────────────────┤     ├──────────────────────┤
│ id (PK)              │     │ id (PK)              │
│ hoten                │     │ hoten                │
│ taikhoan (UNIQUE)    │     │ email (UNIQUE)       │
│ matkhau              │     │ matkhau              │
│ ngaytao              │     │ sdt                  │
└──────────────────────┘     │ diachi               │
                             │ gioitinh             │
                             │ ngaysinh             │
                             │ avatar               │
                             │ ngaytao              │
                             └──────────────────────┘

┌──────────────────────┐     ┌──────────────────────┐
│    tbl_nhatuyendung  │     │    tbl_danhmuc       │
│  (Nhà tuyển dụng)    │     │  (Ngành nghề/Lĩnh vực│
├──────────────────────┤     ├──────────────────────┤
│ id (PK)              │     │ id (PK)              │
│ tencongty            │     │ tenloai              │
│ email (UNIQUE)       │     │ mota                 │
│ matkhau              │     │ ngaytao              │
│ sdt                  │     └──────────────────────┘
│ diachi                                │ 1:N
│ mota                                  ▼
│ logo                      ┌──────────────────────┐
│ ngaytao                   │   tbl_tintuyendung   │
└──────────────────────┘    │  (Tin tuyển dụng)    │
           │ 1:N            ├──────────────────────┤
           └───────────────▶│ id (PK)              │
                            │ idnhatd (FK)         │
                            │ idloai (FK)          │
                            │ tieude               │
                            │ mota                 │
                            │ mucluong             │
                            │ diadiemlamviec       │
                            │ kinhnghiem           │
                            │ hinhthuc             │
                            │ hansotd (deadline)   │
                            │ trangthai (0/1/2)    │
                            │ ngaytao              │
                            └──────────┬───────────┘
                                       │ 1:N
                                       ▼
                            ┌──────────────────────┐
                            │     tbl_ungtuyen     │
                            │  (Đơn ứng tuyển)     │
                            ├──────────────────────┤
                            │ id (PK)              │
                            │ idtin (FK→tintd)     │
                            │ idungvien (FK→uv)    │
                            │ filecv               │
                            │ thuthem              │
                            │ trangthai            │
                            │ ngaynop              │
                            └──────────────────────┘

┌──────────────────────┐     ┌──────────────────────┐
│    tbl_luuviec       │     │    tbl_baidang       │
│  (Việc làm đã lưu)   │     │  (Blog / Tin tức)    │
├──────────────────────┤     ├──────────────────────┤
│ id (PK)              │     │ id (PK)              │
│ idungvien (FK)       │     │ tieude               │
│ idtin (FK)           │     │ noidung              │
│ ngayluu              │     │ anhdaidien           │
└──────────────────────┘     │ tacgia               │
                             │ chedo (Hiện/Ẩn)      │
                             │ ngaytao              │
                             └──────────────────────┘
```

### Quan hệ giữa các bảng

| Quan hệ                                          | Mô tả                                          |
| ------------------------------------------------ | ---------------------------------------------- |
| `tbl_tintuyendung.idnhatd → tbl_nhatuyendung.id` | Mỗi nhà tuyển dụng có nhiều tin đăng (CASCADE) |
| `tbl_tintuyendung.idloai → tbl_danhmuc.id`       | Mỗi tin thuộc một ngành nghề (SET NULL)        |
| `tbl_ungtuyen.idtin → tbl_tintuyendung.id`       | Mỗi tin có nhiều đơn ứng tuyển (CASCADE)       |
| `tbl_ungtuyen.idungvien → tbl_ungvien.id`        | Mỗi ứng viên có nhiều đơn ứng tuyển (CASCADE)  |
| `tbl_luuviec.idungvien → tbl_ungvien.id`         | Ứng viên lưu nhiều tin yêu thích (CASCADE)     |

---

## ⚡ Chức năng chính

### 👤 Phía Ứng viên (Candidate)

| Chức năng           | Mô tả                                                        |
| ------------------- | ------------------------------------------------------------ |
| Trang chủ           | Banner, thống kê số việc làm/công ty, tin tuyển dụng nổi bật |
| Tìm kiếm việc làm   | Lọc theo ngành nghề, địa điểm, mức lương, hình thức làm việc |
| Chi tiết tin TD     | Xem mô tả công việc, yêu cầu, quyền lợi, thông tin công ty   |
| Nộp đơn ứng tuyển   | Upload CV (PDF/DOCX), viết thư xin việc, theo dõi trạng thái |
| Lưu việc làm        | Bookmark tin yêu thích để xem lại sau                        |
| Quản lý hồ sơ       | Cập nhật thông tin cá nhân, avatar, mật khẩu                 |
| Lịch sử ứng tuyển   | Xem danh sách đơn đã nộp và trạng thái phản hồi              |
| Đăng ký / Đăng nhập | Tạo tài khoản, đăng nhập, quên mật khẩu qua email            |
| Blog / Tin tức      | Đọc bài viết về kinh nghiệm phỏng vấn, kỹ năng nghề nghiệp   |
| Liên hệ             | Gửi phản hồi, câu hỏi đến ban quản trị                       |

### 🏢 Phía Nhà tuyển dụng (Employer)

| Chức năng              | Mô tả                                                           |
| ---------------------- | --------------------------------------------------------------- |
| Đăng tin tuyển dụng    | Tạo tin với đầy đủ thông tin: lương, địa điểm, hạn nộp, yêu cầu |
| Quản lý tin đăng       | Xem, sửa, xóa, gia hạn các tin tuyển dụng đã đăng               |
| Xem danh sách ứng viên | Xem tất cả đơn ứng tuyển theo từng tin, lọc theo trạng thái     |
| Xem & Tải CV           | Xem thông tin chi tiết ứng viên, tải file CV về                 |
| Cập nhật hồ sơ công ty | Chỉnh sửa tên công ty, logo, địa chỉ, mô tả                     |

### 🔒 Phía Quản trị viên (Admin Panel)

| Chức năng              | Mô tả                                                              |
| ---------------------- | ------------------------------------------------------------------ |
| Dashboard              | Thống kê tổng quan: số tin, số ứng viên, số công ty, tin chờ duyệt |
| Duyệt tin tuyển dụng   | Xét duyệt / từ chối tin đăng từ nhà tuyển dụng                     |
| Quản lý tin tuyển dụng | Xem toàn bộ tin, chỉnh sửa, xóa, thay đổi trạng thái               |
| Quản lý ứng viên       | Xem danh sách tài khoản ứng viên, khóa/xóa tài khoản               |
| Quản lý nhà tuyển dụng | Xem, thêm, xóa, khóa tài khoản nhà tuyển dụng                      |
| Quản lý danh mục       | CRUD ngành nghề / lĩnh vực tuyển dụng                              |
| Quản lý bài viết       | CRUD bài viết blog, ẩn/hiện bài viết                               |
| Thống kê               | Biểu đồ tin theo tháng, ngành nghề hot, tỉ lệ ứng tuyển            |

---

## 🚀 Hướng dẫn cài đặt

### Yêu cầu hệ thống

- XAMPP (Apache + MariaDB/MySQL + PHP 8.x)
- Trình duyệt web hiện đại (Chrome, Firefox, Edge)

### Các bước cài đặt

**1. Clone hoặc tải source code vào thư mục `htdocs` của XAMPP:**

```bash
cd C:\xampp\htdocs
git clone https://github.com/baducnguyen259/Laptrinhwebnangcao.git
```

**2. Khởi động XAMPP → Bật Apache và MySQL**

**3. Tạo database bằng một trong hai cách:**

> **Cách 1 – phpMyAdmin:** Truy cập `http://localhost/phpmyadmin`, tạo database `webkiemthu` (charset `utf8_unicode_ci`), sau đó Import file `database.sql`

> **Cách 2 – Script tự động:** Truy cập `http://localhost/import_sql.php`

**4. Kiểm tra kết nối DB:** Mở thư mục `config/`, đảm bảo thông tin phù hợp:

```php
$con = mysqli_connect("127.0.0.1", "root", "", "webkiemthu");
```

**5. Truy cập website:**

| Trang                      | URL                                              |
| -------------------------- | ------------------------------------------------ |
| 🌐 Trang tìm kiếm việc làm | `http://localhost/WEBKIEMTHU/public/timkiem.php` |
| 🏢 Trang đăng tin (NTD)    | `http://localhost/WEBKIEMTHU/public/dangtin.php` |
| 🔐 Trang quản trị (Admin)  | `http://localhost/WEBKIEMTHU/public/admin.php`   |
| 🔌 API endpoint            | `http://localhost/WEBKIEMTHU/api/`               |

### Tài khoản mặc định

| Vai trò        | Tài khoản             | Mật khẩu   |
| -------------- | --------------------- | ---------- |
| Admin          | `admintest@gmail.com` | `12345678` |
| Nhà tuyển dụng | ``                    | `123456`   |
| Ứng viên       | `test01@gmail.com`    | `123456`   |

### Cấu hình Email (tùy chọn)

Chỉnh sửa file `includes/mailer.php` với thông tin Gmail SMTP:

```php
$mail->Host       = 'smtp.gmail.com';
$mail->Username   = 'your-email@gmail.com';
$mail->Password   = 'your-app-password';   // App Password của Google
$mail->Port       = 587;
```

---

## 📄 Tài liệu SRS

Tất cả tài liệu đặc tả yêu cầu phần mềm (SRS) được lưu trong thư mục `documents/`:

| File                       | Mô tả                                      |
| -------------------------- | ------------------------------------------ |
| `De_cuong_chuc_nang.md`    | Đề cương chức năng tổng quan toàn hệ thống |
| `SRS_TRANG_CHU.MD`         | Đặc tả trang chủ                           |
| `SRS_DANG_KY.MD`           | Đặc tả chức năng đăng ký                   |
| `SRS_DANG_NHAP.MD`         | Đặc tả chức năng đăng nhập                 |
| `SRS_DANG_XUAT.MD`         | Đặc tả chức năng đăng xuất                 |
| `SRS_QUAN_LY_HO_SO.MD`     | Đặc tả quản lý hồ sơ ứng viên              |
| `SRS_QUEN_MAT_KHAU.MD`     | Đặc tả quên mật khẩu                       |
| `SRS_TIM_KIEM_VIEC_LAM.MD` | Đặc tả tìm kiếm việc làm                   |
| `SRS_CHI_TIET_TIN_TD.MD`   | Đặc tả chi tiết tin tuyển dụng             |
| `SRS_NOP_DON_UNG_TUYEN.MD` | Đặc tả nộp đơn ứng tuyển                   |
| `SRS_LUU_VIEC_LAM.MD`      | Đặc tả lưu việc làm yêu thích              |
| `SRS_LICH_SU_UNG_TUYEN.MD` | Đặc tả lịch sử ứng tuyển                   |
| `SRS_DANG_TIN_TD.MD`       | Đặc tả đăng tin tuyển dụng                 |
| `SRS_QUAN_LY_TIN_DANG.MD`  | Đặc tả quản lý tin đã đăng                 |
| `SRS_XEM_UNGVIEN.MD`       | Đặc tả xem & tải CV ứng viên               |
| `SRS_BLOG_TIN_TUC.MD`      | Đặc tả blog / tin tức                      |
| `SRS_LIEN_HE.MD`           | Đặc tả trang liên hệ                       |
| `SRS_ADMIN_DASHBOARD.MD`   | Đặc tả dashboard admin                     |
| `SRS_ADMIN_DUYET_TIN.MD`   | Đặc tả duyệt tin tuyển dụng                |
| `SRS_ADMIN_QL_UNGVIEN.MD`  | Đặc tả quản lý ứng viên                    |
| `SRS_ADMIN_QL_NHATD.MD`    | Đặc tả quản lý nhà tuyển dụng              |
| `SRS_ADMIN_QL_DANHMUC.MD`  | Đặc tả quản lý danh mục ngành nghề         |
| `SRS_ADMIN_QL_BAIVIET.MD`  | Đặc tả quản lý bài viết                    |
| `SRS_ADMIN_THONGKE.MD`     | Đặc tả thống kê hệ thống                   |
