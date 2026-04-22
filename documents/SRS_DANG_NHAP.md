# SRS_DANG_NHAP — Đặc tả chức năng Đăng nhập

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Ứng viên và Nhà tuyển dụng đăng nhập vào hệ thống bằng email và mật khẩu. Sau khi xác thực thành công, hệ thống cấp JWT token và lưu vào localStorage, sau đó chuyển hướng đến trang phù hợp.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã có tài khoản (đã đăng ký) |
| Trạng thái | Tài khoản chưa bị khóa |
| Hệ thống | Kết nối database ổn định |

---

## 3. Luồng xử lý chính

```
Người dùng truy cập dangnhap.php
        │
        ▼
Nhập email + mật khẩu → Submit form
        │
        ▼
Client validate (auth.js): email hợp lệ, mật khẩu không rỗng
        │
     Lỗi? ──► Hiển thị lỗi inline
        │
        ▼
Gọi API: POST /public/api/auth.php { action: "login", email, matkhau }
        │
        ▼
AuthController kiểm tra email tồn tại
        │
     Không tồn tại? ──► Trả lỗi 401
        │
        ▼
Kiểm tra password_verify(matkhau, hash)
        │
     Sai? ──► Trả lỗi 401
        │
        ▼
Tạo JWT token (AuthTokenService)
        │
        ▼
Trả về { token, user: { id, hoten, email, loai } }
        │
        ▼
Lưu token vào localStorage
        │
        ▼
Phân quyền chuyển hướng:
  - ungvien     → public/index.php
  - nhatuyendung → public/dangtin.php
```

---

## 4. Use Cases

### UC-01: Đăng nhập thành công — Ứng viên

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Ứng viên đã có tài khoản |
| **Mục tiêu** | Đăng nhập để tìm kiếm và nộp đơn |
| **Điều kiện trước** | Tài khoản tồn tại, chưa bị khóa |
| **Luồng chính** | 1. Nhập email + mật khẩu → 2. Submit → 3. Token lưu → 4. Về trang chủ |
| **Điều kiện sau** | Session/token hợp lệ, truy cập được các trang cần đăng nhập |

### UC-02: Đăng nhập thành công — Nhà tuyển dụng

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Nhà tuyển dụng đã đăng ký |
| **Luồng chính** | 1. Nhập email + mật khẩu → 2. Submit → 3. Chuyển về trang đăng tin |
| **Điều kiện sau** | Có thể đăng tin, xem ứng viên |

### UC-03: Đăng nhập thất bại — Sai mật khẩu

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Người dùng nhập sai thông tin |
| **Luồng chính** | 1. Nhập sai mật khẩu → 2. API trả 401 → 3. Hiển thị lỗi |
| **Kết quả** | Thông báo "Email hoặc mật khẩu không đúng" |

### UC-04: Tài khoản bị khóa

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Người dùng bị admin khóa tài khoản |
| **Luồng chính** | 1. Đăng nhập → 2. API kiểm tra trạng thái → 3. Trả lỗi 403 |
| **Kết quả** | Thông báo "Tài khoản đã bị khóa. Liên hệ quản trị viên" |

---

## 5. Input / Output

### Input
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `email` | string | ✅ | Đúng định dạng email |
| `matkhau` | string | ✅ | Không rỗng |
| `loai` | string | ✅ | `ungvien` hoặc `nhatuyendung` |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Thành công | 200 | `{ success: true, token: "jwt...", user: { id, hoten, email, loai } }` |
| Sai thông tin | 401 | `{ success: false, message: "Email hoặc mật khẩu không đúng" }` |
| Tài khoản bị khóa | 403 | `{ success: false, message: "Tài khoản đã bị khóa" }` |
| Thiếu dữ liệu | 400 | `{ success: false, message: "Vui lòng điền đầy đủ thông tin" }` |
| Lỗi server | 500 | `{ success: false, message: "Lỗi hệ thống" }` |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Email không tồn tại | Hiển thị: "Email hoặc mật khẩu không đúng" (không tiết lộ email có tồn tại hay không) |
| Mật khẩu sai | Hiển thị: "Email hoặc mật khẩu không đúng" |
| Tài khoản bị khóa | Hiển thị: "Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên" |
| Token hết hạn (đã đăng nhập trước đó) | AuthMiddleware redirect về dangnhap.php, xóa token cũ |
| Mất kết nối mạng | Hiển thị: "Không thể kết nối. Vui lòng thử lại" |

---

## 7. Giao diện mô tả

```
┌─────────────────────────────────────┐
│           ĐĂNG NHẬP                 │
│                                     │
│  [● Ứng viên]  [○ Nhà tuyển dụng]  │
│                                     │
│  Email *                            │
│  [________________________]         │
│                                     │
│  Mật khẩu *                         │
│  [________________________] [👁]    │
│                                     │
│  [x] Ghi nhớ đăng nhập              │
│                    Quên mật khẩu?   │
│                                     │
│       [    ĐĂNG NHẬP    ]           │
│                                     │
│  Chưa có tài khoản? Đăng ký ngay   │
└─────────────────────────────────────┘
```

---

## 8. File liên quan

| File | Vai trò |
|------|---------|
| `public/dangnhap.php` | Giao diện form đăng nhập |
| `public/api/auth.php` | Endpoint xử lý đăng nhập |
| `public/js/auth.js` | Validate form, gọi API, lưu token |
| `src/Controllers/Api/AuthController.php` | Logic xác thực |
| `src/Models/UserModel.php` | Truy vấn tài khoản |
| `src/Services/AuthTokenService.php` | Tạo và xác thực JWT |
| `src/Middleware/AuthMiddleware.php` | Kiểm tra token trên các trang bảo vệ |
