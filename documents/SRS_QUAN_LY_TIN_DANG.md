# SRS_QUAN_LY_TIN_DANG — Đặc tả chức năng Quản lý tin đã đăng

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Nhà tuyển dụng xem danh sách tất cả tin tuyển dụng mình đã đăng, xem trạng thái duyệt, chỉnh sửa nội dung và xóa tin.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập vai trò Nhà tuyển dụng |

---

## 3. Luồng xử lý chính

```
Nhà tuyển dụng truy cập quanlytindang.php
        │
        ▼
Gọi API: GET /public/api/jobs.php?action=my_jobs
        │
        ▼
Render bảng danh sách tin kèm badge trạng thái
        │
        ├── Click "Sửa" → suatin.php?id={id}
        ├── Click "Xóa" → Xác nhận → Gọi API xóa
        └── Click "Xem ứng viên" → xemungvien.php?idtin={id}
```

---

## 4. Use Cases

### UC-01: Xem danh sách tin đã đăng

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Nhà tuyển dụng |
| **Luồng chính** | 1. Truy cập trang → 2. Tải danh sách → 3. Hiển thị bảng |
| **Kết quả** | Danh sách tin kèm trạng thái, số lượng ứng viên |

### UC-02: Chỉnh sửa tin tuyển dụng

| Trường | Nội dung |
|--------|----------|
| **Điều kiện trước** | Tin ở trạng thái chờ duyệt hoặc đang hoạt động |
| **Luồng chính** | 1. Click "Sửa" → 2. Chỉnh sửa form tại `suatin.php` → 3. Lưu → 4. Tin chuyển về "Chờ duyệt" lại |
| **Điều kiện sau** | Tin được cập nhật, `trangthai` đặt lại về 0 |

### UC-03: Xóa tin tuyển dụng

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Click "Xóa" → 2. Popup xác nhận → 3. Xóa khỏi DB |
| **Điều kiện sau** | Tin và tất cả đơn liên quan bị xóa (CASCADE) |

---

## 5. Trạng thái tin tuyển dụng

| Trạng thái | Giá trị DB | Mô tả | Màu badge |
|------------|-----------|-------|-----------|
| Chờ duyệt | `0` | Admin chưa duyệt | Vàng |
| Đang hoạt động | `1` | Hiển thị công khai | Xanh lá |
| Bị từ chối | `2` | Admin từ chối | Đỏ |
| Hết hạn | *(kiểm tra hansotd)* | Qua hạn nộp | Xám |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Xóa tin có đơn ứng tuyển | Cảnh báo "Tin này có N đơn ứng tuyển. Xóa sẽ mất tất cả dữ liệu" |
| Sửa tin đã bị từ chối | Cho phép sửa, tin chuyển về "Chờ duyệt" |
| Chưa có tin nào | Hiển thị "Bạn chưa đăng tin nào" kèm nút "Đăng tin ngay" |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/quanlytindang.php` | Danh sách tin đã đăng |
| `public/suatin.php` | Form chỉnh sửa tin |
| `public/api/jobs.php` | Endpoint CRUD tin |
| `public/js/jobs.js` | Render bảng, xử lý sửa/xóa |
| `src/Controllers/Api/JobController.php` | Logic CRUD |
| `src/Models/JobModel.php` | Truy vấn database |
