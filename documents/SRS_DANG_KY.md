# SRS_DANG_KY — Đặc tả chức năng Đăng ký tài khoản

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép người dùng tạo tài khoản mới với vai trò Ứng viên hoặc Nhà tuyển dụng. Sau khi đăng ký thành công, hệ thống lưu thông tin vào database và chuyển người dùng đến trang đăng nhập.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Chưa có tài khoản, chưa đăng nhập |
| Hệ thống | Kết nối database ổn định |
| Email | Email chưa tồn tại trong `tbl_ungvien` hoặc `tbl_nhatuyendung` |

---

## 3. Luồng xử lý chính

```
Người dùng truy cập dangky.php
        │
        ▼
Chọn loại tài khoản: Ứng viên / Nhà tuyển dụng
        │
        ▼
Điền form đăng ký (họ tên, email, mật khẩu, xác nhận mật khẩu)
        │
        ▼
Client validate form (auth.js)
        │
     Lỗi? ──► Hiển thị thông báo lỗi ngay trên form
        │
        ▼
Gọi API: POST /public/api/auth.php  { action: "register", ... }
        │
        ▼
AuthController → UserModel::create()
        │
        ▼
Hash mật khẩu (password_hash) → Lưu vào database
        │
        ▼
Trả về token JWT → Lưu localStorage
        │
        ▼
Chuyển hướng sang utthanhcong.php
```

---

## 4. Use Cases

### UC-01: Đăng ký tài khoản Ứng viên

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Khách chưa có tài khoản |
| **Mục tiêu** | Tạo tài khoản ứng viên để tìm và nộp đơn xin việc |
| **Điều kiện trước** | Email chưa đăng ký, kết nối internet ổn định |
| **Luồng chính** | 1. Chọn "Ứng viên" → 2. Điền form → 3. Submit → 4. Nhận thông báo thành công |
| **Điều kiện sau** | Tài khoản được tạo trong `tbl_ungvien`, người dùng đăng nhập tự động |

### UC-02: Đăng ký tài khoản Nhà tuyển dụng

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Doanh nghiệp / cá nhân muốn đăng tin |
| **Mục tiêu** | Tạo tài khoản nhà tuyển dụng để đăng tin tuyển dụng |
| **Điều kiện trước** | Email chưa đăng ký, có tên công ty |
| **Luồng chính** | 1. Chọn "Nhà tuyển dụng" → 2. Điền form (thêm tên công ty) → 3. Submit → 4. Thành công |
| **Điều kiện sau** | Tài khoản được tạo trong `tbl_nhatuyendung` |

### UC-03: Đăng ký thất bại do email trùng

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Người dùng đã có tài khoản |
| **Luồng chính** | 1. Điền email đã tồn tại → 2. Submit → 3. API trả lỗi 409 |
| **Kết quả** | Hiển thị thông báo "Email đã được sử dụng" |

---

## 5. Input / Output

### Input — Form đăng ký Ứng viên
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `hoten` | string | ✅ | 2–100 ký tự |
| `email` | string | ✅ | Đúng định dạng email, chưa tồn tại |
| `matkhau` | string | ✅ | Tối thiểu 6 ký tự |
| `xacnhan_matkhau` | string | ✅ | Phải khớp với `matkhau` |
| `loai` | string | ✅ | `ungvien` hoặc `nhatuyendung` |

### Input bổ sung — Nhà tuyển dụng
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `tencongty` | string | ✅ | 2–200 ký tự |
| `sdt` | string | ❌ | 10–11 chữ số |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Thành công | 200 | `{ success: true, token: "...", user: {...} }` |
| Email trùng | 409 | `{ success: false, message: "Email đã được sử dụng" }` |
| Dữ liệu thiếu | 400 | `{ success: false, message: "Vui lòng điền đầy đủ thông tin" }` |
| Lỗi server | 500 | `{ success: false, message: "Lỗi hệ thống" }` |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Email đã tồn tại | Hiển thị lỗi dưới trường email: "Email này đã được đăng ký" |
| Mật khẩu không khớp | Hiển thị lỗi dưới trường xác nhận: "Mật khẩu không khớp" |
| Mật khẩu quá ngắn | Hiển thị lỗi: "Mật khẩu phải có ít nhất 6 ký tự" |
| Email sai định dạng | Hiển thị lỗi: "Email không hợp lệ" |
| Mất kết nối mạng | Hiển thị thông báo: "Không thể kết nối. Vui lòng thử lại" |
| Lỗi database | Ghi log, hiển thị thông báo lỗi chung |

---

## 7. Giao diện mô tả

```
┌─────────────────────────────────────┐
│         ĐĂNG KÝ TÀI KHOẢN          │
│                                     │
│  [● Ứng viên]  [○ Nhà tuyển dụng]  │
│                                     │
│  Họ và tên *                        │
│  [________________________]         │
│                                     │
│  Email *                            │
│  [________________________]         │
│                                     │
│  Mật khẩu *                         │
│  [________________________]         │
│                                     │
│  Xác nhận mật khẩu *                │
│  [________________________]         │
│                                     │
│       [    ĐĂNG KÝ    ]             │
│                                     │
│  Đã có tài khoản? Đăng nhập         │
└─────────────────────────────────────┘
```

---

## 8. File liên quan

| File | Vai trò |
|------|---------|
| `public/dangky.php` | Giao diện form đăng ký |
| `public/utthanhcong.php` | Trang thông báo đăng ký thành công |
| `public/api/auth.php` | Endpoint xử lý đăng ký |
| `public/js/auth.js` | Validate form, gọi API |
| `src/Controllers/Api/AuthController.php` | Logic đăng ký |
| `src/Models/UserModel.php` | Tạo tài khoản trong database |
| `src/Services/AuthTokenService.php` | Tạo JWT token sau đăng ký |
