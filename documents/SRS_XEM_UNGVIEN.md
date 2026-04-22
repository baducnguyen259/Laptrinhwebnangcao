# SRS_XEM_UNGVIEN — Đặc tả chức năng Xem & Tải CV ứng viên

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Nhà tuyển dụng xem danh sách ứng viên đã nộp đơn vào các tin tuyển dụng của mình, xem thông tin chi tiết ứng viên, tải file CV và cập nhật trạng thái đơn.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập vai trò Nhà tuyển dụng |
| Dữ liệu | Có ứng viên đã nộp đơn vào tin của NTD |

---

## 3. Luồng xử lý chính

```
NTD truy cập xemungvien.php?idtin={id}
        │
        ▼
AuthMiddleware + kiểm tra tin thuộc sở hữu NTD
        │
        ▼
Gọi API: GET /public/api/applications.php?action=job_applications&idtin={id}
        │
        ▼
Render danh sách ứng viên dạng bảng + badge trạng thái
        │
        ├── Click "Xem CV" → Mở file PDF/DOCX trong tab mới
        ├── Click "Tải CV" → Download file CV
        └── Đổi trạng thái dropdown → Gọi API cập nhật trạng thái
```

---

## 4. Use Cases

### UC-01: Xem danh sách ứng viên

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Nhà tuyển dụng |
| **Luồng chính** | 1. Từ quản lý tin → click "Xem ứng viên" → 2. Tải danh sách → 3. Hiển thị |
| **Kết quả** | Bảng ứng viên: họ tên, email, ngày nộp, trạng thái, link CV |

### UC-02: Tải file CV ứng viên

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Click "Tải CV" → 2. Trình duyệt download file từ `public/uploads/` |
| **Kết quả** | File CV được tải về máy NTD |

### UC-03: Cập nhật trạng thái đơn

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Chọn trạng thái mới từ dropdown → 2. API cập nhật → 3. Badge cập nhật |
| **Điều kiện sau** | `tbl_ungtuyen.trangthai` được cập nhật |

---

## 5. Input / Output

### Input
| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `idtin` | int | ID tin tuyển dụng |
| `trangthai` | string | Trạng thái mới khi cập nhật |

### Output — Danh sách ứng viên
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "hoten": "Nguyễn Văn A",
      "email": "a@gmail.com",
      "sdt": "0912345678",
      "filecv": "uploads/cv_a.pdf",
      "thuthem": "Kính gửi...",
      "trangthai": "cho_xet",
      "ngaynop": "2026-04-05"
    }
  ]
}
```

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Chưa có ứng viên nào | Hiển thị "Chưa có ứng viên nào nộp đơn cho tin này" |
| Tin không thuộc NTD | Trả về lỗi 403, redirect về `quanlytindang.php` |
| File CV bị xóa khỏi server | Hiển thị "File không còn tồn tại" thay vì link |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/xemungvien.php` | Giao diện danh sách ứng viên |
| `public/api/applications.php` | Endpoint lấy / cập nhật đơn |
| `public/js/applications.js` | Render bảng, cập nhật trạng thái |
| `src/Controllers/Api/ApplicationController.php` | Logic xử lý |
| `src/Models/ApplicationModel.php` | Truy vấn `tbl_ungtuyen` |
