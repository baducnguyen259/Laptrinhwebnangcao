# SRS_ADMIN_QL_BAIVIET — Đặc tả chức năng Quản lý bài viết (Admin)

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Admin tạo mới, chỉnh sửa, xóa và ẩn/hiện bài viết blog trên hệ thống. Bài viết được lưu vào bảng `tbl_baidang` và hiển thị công khai khi ở trạng thái `chedo = 'Hien'`.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập Admin |
| Xác thực | Token Admin hợp lệ, qua `AdminMiddleware` |

---

## 3. Luồng xử lý chính

```
Admin vào mục "Quản lý bài viết" trên Dashboard
        │
        ▼
Gọi API: GET /public/api/admin_reports.php?action=posts
        │
        ▼
Render bảng danh sách bài viết kèm trạng thái Hiện/Ẩn
        │
        ├── Click "Thêm bài viết" → Mở form tạo mới
        ├── Click "Sửa"          → Mở form chỉnh sửa điền sẵn dữ liệu
        ├── Click "Ẩn / Hiện"    → Toggle chedo Hien/An
        └── Click "Xóa"          → Xác nhận → Xóa khỏi DB
```

---

## 4. Use Cases

### UC-01: Tạo bài viết mới

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Mục tiêu** | Đăng bài viết mới lên blog |
| **Luồng chính** | 1. Click "Thêm bài viết" → 2. Điền form → 3. Upload ảnh đại diện → 4. Submit |
| **Điều kiện sau** | Bài viết lưu vào `tbl_baidang`, mặc định `chedo = 'Hien'` |

### UC-02: Chỉnh sửa bài viết

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Luồng chính** | 1. Click "Sửa" → 2. Form điền sẵn nội dung → 3. Chỉnh sửa → 4. Lưu |
| **Điều kiện sau** | Nội dung bài viết được cập nhật trong DB |

### UC-03: Ẩn / Hiện bài viết

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Mục tiêu** | Tạm ẩn bài viết không phù hợp mà không xóa |
| **Luồng chính** | 1. Click toggle "Ẩn" → 2. API cập nhật `chedo = 'An'` → 3. Bài không còn hiển thị công khai |
| **Điều kiện sau** | `tbl_baidang.chedo` được cập nhật |

### UC-04: Xóa bài viết

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Luồng chính** | 1. Click "Xóa" → 2. Popup xác nhận → 3. Xóa khỏi DB và xóa ảnh đại diện |
| **Điều kiện sau** | Bài viết bị xóa vĩnh viễn khỏi hệ thống |

---

## 5. Input / Output

### Input — Tạo / Sửa bài viết
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `tieude` | string | ✅ | 5–300 ký tự |
| `noidung` | text (HTML) | ✅ | Tối thiểu 100 ký tự |
| `anhdaidien` | file | ❌ | JPG/PNG, ≤ 3MB |
| `tacgia` | string | ❌ | Mặc định: tên Admin đang đăng nhập |
| `chedo` | string | ✅ | `Hien` hoặc `An` |

### Output — Danh sách bài viết
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tieude": "10 kỹ năng mềm cần có khi đi phỏng vấn",
      "tacgia": "Admin",
      "chedo": "Hien",
      "ngaytao": "2026-03-15",
      "anhdaidien": "uploads/blog/bai1.jpg"
    }
  ],
  "total": 24
}
```

### Output — Tạo / Sửa thành công
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Tạo thành công | 201 | `{ success: true, message: "Đăng bài viết thành công" }` |
| Sửa thành công | 200 | `{ success: true, message: "Cập nhật bài viết thành công" }` |
| Ẩn/Hiện thành công | 200 | `{ success: true, chedo: "An" }` |
| Xóa thành công | 200 | `{ success: true, message: "Đã xóa bài viết" }` |
| Thiếu dữ liệu | 400 | `{ success: false, message: "Vui lòng điền đầy đủ thông tin" }` |
| Không có quyền | 403 | `{ success: false, message: "Không có quyền thực hiện" }` |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Tiêu đề trống | Lỗi inline: "Tiêu đề không được để trống" |
| Nội dung quá ngắn | Lỗi: "Nội dung phải có ít nhất 100 ký tự" |
| Ảnh sai định dạng | Lỗi: "Chỉ chấp nhận JPG hoặc PNG" |
| Ảnh quá lớn (>3MB) | Lỗi: "Dung lượng ảnh tối đa 3MB" |
| Xóa bài đang hiển thị | Cảnh báo xác nhận: "Bài viết này đang hiển thị công khai. Bạn có chắc muốn xóa?" |
| Lỗi upload ảnh | Ghi log, thông báo lỗi upload, vẫn cho lưu bài không có ảnh |

---

## 7. Giao diện mô tả

```
┌────────────────────────────────────────────────────────┐
│  QUẢN LÝ BÀI VIẾT              [+ Thêm bài viết]      │
├────────┬──────────────────┬────────┬────────┬──────────┤
│ ID     │ Tiêu đề          │ Tác giả│ Trạng  │ Thao tác │
│        │                  │        │ thái   │          │
├────────┼──────────────────┼────────┼────────┼──────────┤
│ 1      │ 10 kỹ năng mềm.. │ Admin  │ 🟢Hiện │[Sửa][Ẩn]│
│        │                  │        │        │[Xóa]     │
├────────┼──────────────────┼────────┼────────┼──────────┤
│ 2      │ Cách viết CV..   │ Admin  │ 🔴 Ẩn  │[Sửa][Hiện│
│        │                  │        │        │][Xóa]    │
└────────┴──────────────────┴────────┴────────┴──────────┘
```

---

## 8. File liên quan

| File | Vai trò |
|------|---------|
| `public/admin.php` | Giao diện quản lý bài viết (section/tab) |
| `public/api/admin_reports.php` | Endpoint CRUD bài viết |
| `public/js/admin.js` | Render bảng, xử lý form tạo/sửa |
| `src/Controllers/Api/Admin/AdminReportController.php` | Logic quản lý bài viết |
| `src/Models/AdminReportModel.php` | Truy vấn `tbl_baidang` |
| `src/Middleware/AdminMiddleware.php` | Kiểm tra quyền Admin |
