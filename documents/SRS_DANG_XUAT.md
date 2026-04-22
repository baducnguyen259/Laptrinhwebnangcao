# SRS_DANG_XUAT — Đặc tả chức năng Đăng xuất

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép người dùng đang đăng nhập kết thúc phiên làm việc. Hệ thống xóa JWT token khỏi localStorage và chuyển người dùng về trang chủ hoặc trang đăng nhập.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đang đăng nhập (có token hợp lệ trong localStorage) |

---

## 3. Luồng xử lý chính

```
Người dùng click nút "Đăng xuất" trên header
        │
        ▼
auth.js: xóa token khỏi localStorage
        │
        ▼
Gọi API: POST /public/api/auth.php { action: "logout" }
(tuỳ chọn — vô hiệu hoá token phía server nếu dùng blacklist)
        │
        ▼
Chuyển hướng về public/dangnhap.php
```

---

## 4. Use Cases

### UC-01: Đăng xuất chủ động

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Ứng viên hoặc Nhà tuyển dụng đang đăng nhập |
| **Mục tiêu** | Kết thúc phiên làm việc an toàn |
| **Luồng chính** | 1. Click "Đăng xuất" → 2. Xóa token → 3. Về trang đăng nhập |
| **Điều kiện sau** | Token bị xóa, truy cập trang cần đăng nhập sẽ bị redirect |

### UC-02: Tự động đăng xuất khi token hết hạn

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Hệ thống (AuthMiddleware) |
| **Mục tiêu** | Bảo vệ tài khoản khi phiên hết hạn |
| **Luồng chính** | 1. Người dùng thao tác → 2. Middleware phát hiện token hết hạn → 3. Xóa token → 4. Redirect về đăng nhập |
| **Kết quả** | Thông báo "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại" |

---

## 5. Input / Output

### Input
| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `Authorization` header | string | JWT token hiện tại (gửi lên để blacklist nếu cần) |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Thành công | 200 | `{ success: true }` → redirect về dangnhap.php |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Token đã hết hạn trước khi đăng xuất | Vẫn xóa token local và redirect bình thường |
| Mất kết nối khi gọi API logout | Vẫn xóa token local và redirect (ưu tiên UX) |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/js/auth.js` | Xử lý xóa token, gọi API logout, redirect |
| `public/api/auth.php` | Endpoint logout (vô hiệu hoá token server-side nếu cần) |
| `src/Controllers/Api/AuthController.php` | Logic đăng xuất |
| `src/Middleware/AuthMiddleware.php` | Phát hiện token hết hạn |
| `src/Services/AuthTokenService.php` | Kiểm tra và hủy token |
