# SRS_ADMIN_DASHBOARD — Đặc tả chức năng Admin Dashboard

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Trang tổng quan quản trị hiển thị các chỉ số thống kê nhanh: số tin tuyển dụng, số ứng viên, số nhà tuyển dụng, số đơn ứng tuyển, tin chờ duyệt. Hỗ trợ Admin nắm tổng quan hệ thống.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập với vai trò Admin |
| Xác thực | Token Admin hợp lệ, qua `AdminMiddleware` |

---

## 3. Luồng xử lý chính

```
Admin truy cập public/admin.php
        │
        ▼
admin-auth.js kiểm tra token Admin trong localStorage
        │
     Không hợp lệ? ──► Redirect về admin_dangnhap.php
        │
        ▼
Gọi API: GET /public/api/admin_reports.php?action=dashboard
        │
        ▼
AdminReportController tổng hợp số liệu từ nhiều bảng
        │
        ▼
Render dashboard: thẻ thống kê + danh sách tin chờ duyệt gần đây
```

---

## 4. Use Cases

### UC-01: Xem tổng quan hệ thống

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Quản trị viên |
| **Mục tiêu** | Nắm nhanh tình trạng hệ thống |
| **Luồng chính** | 1. Đăng nhập admin → 2. Dashboard tải số liệu → 3. Hiển thị thẻ thống kê |
| **Kết quả** | Hiển thị: tổng tin, tổng ứng viên, tổng NTD, tin chờ duyệt, đơn hôm nay |

---

## 5. Output — Dữ liệu Dashboard

```json
{
  "tong_tin": 245,
  "tin_cho_duyet": 12,
  "tong_ungvien": 1840,
  "tong_nhatd": 95,
  "don_hom_nay": 37,
  "tin_moi_nhat": [...]
}
```

---

## 6. File liên quan

| File | Vai trò |
|------|---------|
| `public/admin.php` | Giao diện dashboard |
| `public/admin_dangnhap.php` | Trang đăng nhập admin |
| `public/api/admin_reports.php` | Endpoint thống kê |
| `public/js/admin.js` | Render dashboard |
| `public/js/admin-auth.js` | Kiểm tra phiên admin |
| `src/Controllers/Api/Admin/AdminReportController.php` | Logic thống kê |
| `src/Models/AdminReportModel.php` | Truy vấn số liệu |
| `src/Middleware/AdminMiddleware.php` | Kiểm tra quyền admin |
