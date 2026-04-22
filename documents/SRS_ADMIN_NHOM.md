# SRS_ADMIN_DUYET_TIN — Đặc tả chức năng Duyệt tin tuyển dụng

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Admin xem danh sách tin tuyển dụng đang chờ duyệt, xem nội dung chi tiết và quyết định duyệt (cho hiển thị công khai) hoặc từ chối (kèm lý do).

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập Admin |
| Dữ liệu | Có tin ở trạng thái `trangthai=0` |

---

## 3. Luồng xử lý chính

```
Admin vào mục "Duyệt tin" trên Dashboard
        │
        ▼
Gọi API: GET /public/api/admin_jobs.php?action=pending
        │
        ▼
Render danh sách tin chờ duyệt
        │
        ├── Click "Duyệt" → API cập nhật trangthai=1
        └── Click "Từ chối" → Nhập lý do → API cập nhật trangthai=2
```

---

## 4. Use Cases

### UC-01: Duyệt tin

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Admin |
| **Luồng chính** | 1. Xem chi tiết tin → 2. Click "Duyệt" → 3. `trangthai=1` → 4. Tin hiện công khai |

### UC-02: Từ chối tin

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Click "Từ chối" → 2. Nhập lý do → 3. `trangthai=2` |
| **Điều kiện sau** | NTD thấy tin bị "Từ chối" trong quản lý tin đăng |

---

## 5. Input / Output

### Input
| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | int | ID tin tuyển dụng |
| `action` | string | `approve` hoặc `reject` |
| `lydo` | string | Lý do từ chối (bắt buộc nếu `reject`) |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Duyệt thành công | 200 | `{ success: true, message: "Tin đã được duyệt" }` |
| Từ chối thành công | 200 | `{ success: true, message: "Tin đã bị từ chối" }` |

---

## 6. File liên quan

| File | Vai trò |
|------|---------|
| `public/admin.php` | Giao diện duyệt tin (tab/section) |
| `public/api/admin_jobs.php` | Endpoint duyệt/từ chối |
| `public/js/admin.js` | Xử lý thao tác duyệt |
| `src/Controllers/AdminJobsController.php` | Logic duyệt tin |
| `src/Models/AdminJobModel.php` | Cập nhật `tbl_tintuyendung` |

---
---

# SRS_ADMIN_QL_UNGVIEN — Đặc tả chức năng Quản lý ứng viên

---

## 1. Mô tả chức năng

Admin xem danh sách tất cả tài khoản ứng viên, tìm kiếm theo tên/email, xem thông tin chi tiết và khóa/mở khóa tài khoản.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập Admin |

---

## 3. Luồng xử lý chính

```
Admin vào mục "Quản lý ứng viên"
        │
        ▼
Gọi API: GET /public/api/admin_users.php?action=candidates&keyword=&page=
        │
        ▼
Render bảng danh sách ứng viên
        │
        ├── Click "Xem chi tiết" → Mở modal thông tin + lịch sử ứng tuyển
        └── Click "Khóa / Mở khóa" → Cập nhật trạng thái tài khoản
```

---

## 4. Use Cases

### UC-01: Xem danh sách ứng viên

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Tải danh sách → 2. Tìm kiếm theo tên/email → 3. Lọc theo trạng thái |

### UC-02: Khóa tài khoản ứng viên

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | 1. Click "Khóa" → 2. Xác nhận → 3. Tài khoản bị khóa, không đăng nhập được |

---

## 5. Input / Output

### Input
| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `keyword` | string | Tìm theo tên hoặc email |
| `action` | string | `lock` hoặc `unlock` |
| `id` | int | ID ứng viên |

---

## 6. File liên quan

| File | Vai trò |
|------|---------|
| `public/api/admin_users.php` | Endpoint quản lý người dùng |
| `src/Controllers/Api/Admin/AdminUserController.php` | Logic xử lý |
| `src/Models/AdminUserModel.php` | Truy vấn `tbl_ungvien` |

---
---

# SRS_ADMIN_QL_NHATD — Đặc tả chức năng Quản lý nhà tuyển dụng

---

## 1. Mô tả chức năng

Admin xem, tìm kiếm, khóa/mở khóa tài khoản nhà tuyển dụng. Có thể xem chi tiết thông tin công ty và danh sách tin đã đăng.

---

## 3. Luồng xử lý chính

```
Admin vào mục "Quản lý NTD"
        │
        ▼
Gọi API: GET /public/api/admin_users.php?action=employers
        │
        ▼
Render bảng + thao tác khóa/mở khóa
```

---

## 4. File liên quan

| File | Vai trò |
|------|---------|
| `public/api/admin_users.php` | Endpoint quản lý NTD |
| `src/Controllers/Api/Admin/AdminUserController.php` | Logic |
| `src/Models/AdminUserModel.php` | Truy vấn `tbl_nhatuyendung` |

---
---

# SRS_ADMIN_QL_DANHMUC — Đặc tả chức năng Quản lý danh mục ngành nghề

---

## 1. Mô tả chức năng

Admin thực hiện CRUD danh mục ngành nghề (`tbl_danhmuc`) — thêm, sửa, xóa ngành nghề được dùng để phân loại tin tuyển dụng.

---

## 3. Luồng xử lý chính

```
Admin vào mục "Danh mục"
        │
        ▼
Gọi API: GET /public/api/admin_jobs.php?action=categories
        │
        ▼
Render bảng danh mục + form thêm mới
        │
        ├── Thêm mới: POST với tenloai, mota
        ├── Sửa: PUT với id, tenloai, mota
        └── Xóa: DELETE với id (kiểm tra không có tin liên kết)
```

---

## 4. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Xóa danh mục đang có tin tuyển dụng | Thông báo "Không thể xóa danh mục đang có tin tuyển dụng" |
| Tên danh mục trùng | Lỗi: "Tên ngành nghề đã tồn tại" |

---

## 5. File liên quan

| File | Vai trò |
|------|---------|
| `public/api/admin_jobs.php` | Endpoint CRUD danh mục |
| `src/Controllers/AdminJobsController.php` | Logic danh mục |
| `src/Models/AdminJobModel.php` | Truy vấn `tbl_danhmuc` |

---
---

# SRS_ADMIN_THONGKE — Đặc tả chức năng Thống kê hệ thống

---

## 1. Mô tả chức năng

Admin xem biểu đồ thống kê: số tin tuyển dụng theo tháng, ngành nghề có nhiều tin nhất, tỉ lệ ứng tuyển theo trạng thái. Dữ liệu được lấy từ `admin_reports.php` và hiển thị dưới dạng biểu đồ (Chart.js hoặc tương đương).

---

## 3. Luồng xử lý chính

```
Admin vào mục "Thống kê"
        │
        ▼
Gọi API: GET /public/api/admin_reports.php?action=stats&period=month
        │
        ▼
AdminReportController tổng hợp dữ liệu theo khoảng thời gian
        │
        ▼
Render biểu đồ cột (tin theo tháng) + biểu đồ tròn (ngành hot)
```

---

## 4. Output — Dữ liệu thống kê

```json
{
  "tin_theo_thang": [
    { "thang": "01/2026", "so_tin": 18 },
    { "thang": "02/2026", "so_tin": 25 }
  ],
  "nganh_hot": [
    { "tenloai": "Công nghệ thông tin", "so_tin": 87 },
    { "tenloai": "Kế toán", "so_tin": 45 }
  ],
  "ti_le_ung_tuyen": {
    "cho_xet": 120,
    "phu_hop": 45,
    "khong_phu_hop": 30
  }
}
```

---

## 5. File liên quan

| File | Vai trò |
|------|---------|
| `public/api/admin_reports.php` | Endpoint thống kê |
| `public/js/admin.js` | Render biểu đồ |
| `src/Controllers/Api/Admin/AdminReportController.php` | Tổng hợp dữ liệu |
| `src/Models/AdminReportModel.php` | Truy vấn thống kê |
