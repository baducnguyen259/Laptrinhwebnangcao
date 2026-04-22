# SRS_ADMIN_QL_DONTD — Đặc tả chức năng Quản lý đơn ứng tuyển (Admin)

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Admin xem toàn bộ danh sách đơn ứng tuyển trong hệ thống, lọc theo trạng thái, tin tuyển dụng, ứng viên. Hỗ trợ xem chi tiết và xóa đơn khi cần thiết.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập Admin |

---

## 3. Luồng xử lý chính

```
Admin vào mục "Quản lý đơn ứng tuyển"
        │
        ▼
Gọi API: GET /public/api/admin_applications.php?action=list&page=&trangthai=
        │
        ▼
AdminApplicationController → AdminApplicationModel::getAll()
        │
        ▼
Render bảng: ứng viên, tin tuyển dụng, ngày nộp, trạng thái
        │
        ├── Click "Xem chi tiết" → Modal xem thông tin đơn + CV
        ├── Lọc theo trạng thái → Cập nhật danh sách
        └── Click "Xóa" → Xác nhận → Xóa đơn
```

---

## 4. Use Cases

### UC-01: Xem danh sách tất cả đơn ứng tuyển

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Mục tiêu** | Giám sát toàn bộ hoạt động ứng tuyển trên hệ thống |
| **Luồng chính** | 1. Vào mục quản lý → 2. Tải danh sách → 3. Lọc/tìm kiếm |
| **Kết quả** | Bảng đơn ứng tuyển với đầy đủ thông tin |

### UC-02: Lọc đơn theo trạng thái

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Chọn trạng thái từ dropdown → 2. API lọc → 3. Render danh sách mới |

### UC-03: Xóa đơn ứng tuyển

| Trường | Nội dung |
|--------|----------|
| **Điều kiện trước** | Admin có lý do hợp lệ (vi phạm, spam...) |
| **Luồng chính** | 1. Click "Xóa" → 2. Xác nhận → 3. Xóa khỏi `tbl_ungtuyen` |

---

## 5. Input / Output

### Input — Lấy danh sách
| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `action` | string | `list` |
| `trangthai` | string | Lọc theo trạng thái đơn (tuỳ chọn) |
| `idtin` | int | Lọc theo tin tuyển dụng (tuỳ chọn) |
| `keyword` | string | Tìm theo tên ứng viên / tên công ty |
| `page` | int | Phân trang |

### Output
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ungvien": { "id": 10, "hoten": "Nguyễn Văn A", "email": "a@gmail.com" },
      "tintd": { "id": 5, "tieude": "Lập trình viên PHP", "tencongty": "Công ty ABC" },
      "filecv": "uploads/cv_a.pdf",
      "trangthai": "cho_xet",
      "ngaynop": "2026-04-05"
    }
  ],
  "total": 150,
  "page": 1
}
```

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Không có đơn nào | Hiển thị "Chưa có đơn ứng tuyển nào" |
| File CV đã bị xóa | Hiển thị "File không tồn tại" thay vì link download |
| Lỗi xóa database | Thông báo lỗi chung, ghi log |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/admin.php` | Giao diện quản lý đơn (section/tab) |
| `public/api/admin_applications.php` | Endpoint quản lý đơn |
| `public/js/admin.js` | Render bảng, xử lý lọc/xóa |
| `src/Controllers/Api/Admin/AdminApplicationController.php` | Logic xử lý |
| `src/Models/AdminApplicationModel.php` | Truy vấn `tbl_ungtuyen` |
