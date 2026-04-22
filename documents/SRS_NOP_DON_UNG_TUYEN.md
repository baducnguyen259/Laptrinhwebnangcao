# SRS_NOP_DON_UNG_TUYEN — Đặc tả chức năng Nộp đơn ứng tuyển

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Ứng viên đã đăng nhập nộp đơn ứng tuyển vào một tin tuyển dụng. Ứng viên upload file CV (PDF/DOCX) và viết thư xin việc. Hệ thống lưu đơn vào `tbl_ungtuyen` và chuyển sang trang xác nhận thành công.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập với vai trò Ứng viên |
| Tin tuyển dụng | Đang hoạt động (`trangthai=1`), chưa hết hạn (`hansotd >= NOW()`) |
| Trùng đơn | Ứng viên chưa nộp đơn cho tin này trước đó |

---

## 3. Luồng xử lý chính

```
Ứng viên click "Nộp đơn ngay" trên trang chi tiết
        │
        ▼
Mở modal / chuyển form nộp đơn
        │
        ▼
Ứng viên upload file CV + viết thư xin việc (tuỳ chọn)
        │
        ▼
Client validate: định dạng file (PDF/DOCX), dung lượng ≤ 5MB
        │
     Lỗi? ──► Hiển thị lỗi inline
        │
        ▼
Gọi API: POST /public/api/applications.php
  { action: "apply", idtin, filecv (FormData), thuthem }
        │
        ▼
ApplicationController:
  - Kiểm tra token hợp lệ (AuthMiddleware)
  - Kiểm tra chưa nộp đơn trùng
  - Lưu file CV vào public/uploads/
  - Lưu đơn vào tbl_ungtuyen (trangthai = "cho_xet")
        │
        ▼
Chuyển hướng về utthanhcong.php
```

---

## 4. Use Cases

### UC-01: Nộp đơn thành công

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Ứng viên đã đăng nhập |
| **Mục tiêu** | Nộp đơn ứng tuyển vào vị trí mong muốn |
| **Điều kiện trước** | Chưa nộp đơn tin này, tin còn hạn |
| **Luồng chính** | 1. Upload CV → 2. Viết thư → 3. Xác nhận → 4. Thành công |
| **Điều kiện sau** | Đơn lưu trong `tbl_ungtuyen`, NTD nhận thông báo |

### UC-02: Nộp đơn bị từ chối — Đã nộp trước đó

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | Ứng viên cố nộp đơn lần 2 cho cùng tin |
| **Kết quả** | Thông báo "Bạn đã nộp đơn cho tin tuyển dụng này rồi" |

### UC-03: File CV không hợp lệ

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | Upload file .jpg hoặc file > 5MB |
| **Kết quả** | Thông báo "Chỉ chấp nhận file PDF/DOCX, dung lượng tối đa 5MB" |

---

## 5. Input / Output

### Input
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `idtin` | int | ✅ | Tin tồn tại, còn hạn |
| `filecv` | file | ✅ | PDF hoặc DOCX, ≤ 5MB |
| `thuthem` | string | ❌ | Tối đa 2000 ký tự |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Thành công | 200 | `{ success: true, message: "Nộp đơn thành công" }` |
| Đã nộp rồi | 409 | `{ success: false, message: "Bạn đã nộp đơn tin này" }` |
| File không hợp lệ | 400 | `{ success: false, message: "Định dạng hoặc dung lượng file không hợp lệ" }` |
| Chưa đăng nhập | 401 | `{ success: false, message: "Vui lòng đăng nhập" }` |
| Tin hết hạn | 400 | `{ success: false, message: "Tin tuyển dụng đã hết hạn" }` |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| File sai định dạng | Lỗi inline: "Chỉ chấp nhận PDF hoặc DOCX" |
| File quá lớn (>5MB) | Lỗi inline: "Dung lượng tối đa 5MB" |
| Nộp đơn trùng | Thông báo lỗi, nút "Xem đơn đã nộp" dẫn về `trangthai.php` |
| Chưa đăng nhập | Redirect về `dangnhap.php?redirect=chitietcongviec.php?id={id}` |
| Lỗi upload file | Ghi log, thông báo "Không thể upload file. Vui lòng thử lại" |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/chitietcongviec.php` | Chứa form/modal nộp đơn |
| `public/utthanhcong.php` | Trang xác nhận thành công |
| `public/api/applications.php` | Endpoint nộp đơn |
| `public/uploads/` | Lưu file CV |
| `public/js/applications.js` | Xử lý upload, gọi API |
| `src/Controllers/Api/ApplicationController.php` | Logic nộp đơn |
| `src/Models/ApplicationModel.php` | Lưu đơn vào database |
