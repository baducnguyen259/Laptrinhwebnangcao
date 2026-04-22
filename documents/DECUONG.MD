# ĐỀ CƯƠNG CHỨC NĂNG — Website Tìm Kiếm Việc Làm

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Tổng quan hệ thống

Website tìm kiếm việc làm là nền tảng kết nối **Ứng viên** (người tìm việc) và **Nhà tuyển dụng** (doanh nghiệp tuyển dụng) dưới sự giám sát của **Quản trị viên**.

### Các nhóm người dùng

| Vai trò            | Mô tả                                              |
| ------------------ | -------------------------------------------------- |
| **Khách**          | Chưa đăng nhập, chỉ xem được trang chủ và tìm kiếm |
| **Ứng viên**       | Tìm kiếm, xem chi tiết, nộp đơn, quản lý hồ sơ     |
| **Nhà tuyển dụng** | Đăng tin, quản lý tin, xem ứng viên                |
| **Quản trị viên**  | Duyệt tin, quản lý toàn bộ hệ thống                |

---

## 2. Danh sách chức năng toàn hệ thống

### 2.1 Chức năng chung (tất cả người dùng)

| Mã  | Chức năng                   | File SRS                 |
| --- | --------------------------- | ------------------------ |
| F01 | Xem trang chủ               | SRS_TRANG_CHU.md         |
| F02 | Tìm kiếm việc làm           | SRS_TIM_KIEM_VIEC_LAM.md |
| F03 | Xem chi tiết tin tuyển dụng | SRS_CHI_TIET_TIN_TD.md   |
| F04 | Đăng ký tài khoản           | SRS_DANG_KY.md           |
| F05 | Đăng nhập                   | SRS_DANG_NHAP.md         |
| F06 | Đăng xuất                   | SRS_DANG_XUAT.md         |
| F07 | Quên mật khẩu               | SRS_QUEN_MAT_KHAU.md     |

### 2.2 Chức năng Ứng viên

| Mã  | Chức năng              | File SRS                 |
| --- | ---------------------- | ------------------------ |
| F08 | Nộp đơn ứng tuyển      | SRS_NOP_DON_UNG_TUYEN.md |
| F09 | Lưu việc làm yêu thích | SRS_LUU_VIEC_LAM.md      |
| F10 | Xem lịch sử ứng tuyển  | SRS_LICH_SU_UNG_TUYEN.md |
| F11 | Quản lý hồ sơ cá nhân  | SRS_QUAN_LY_HO_SO.md     |

### 2.3 Chức năng Nhà tuyển dụng

| Mã  | Chức năng             | File SRS                |
| --- | --------------------- | ----------------------- |
| F12 | Đăng tin tuyển dụng   | SRS_DANG_TIN_TD.md      |
| F13 | Quản lý tin đã đăng   | SRS_QUAN_LY_TIN_DANG.md |
| F14 | Xem & tải CV ứng viên | SRS_XEM_UNGVIEN.md      |

### 2.4 Chức năng Quản trị viên

| Mã  | Chức năng              | File SRS                |
| --- | ---------------------- | ----------------------- |
| F15 | Dashboard tổng quan    | SRS_ADMIN_DASHBOARD.md  |
| F16 | Duyệt tin tuyển dụng   | SRS_ADMIN_DUYET_TIN.md  |
| F17 | Quản lý ứng viên       | SRS_ADMIN_QL_UNGVIEN.md |
| F18 | Quản lý nhà tuyển dụng | SRS_ADMIN_QL_NHATD.md   |
| F19 | Quản lý danh mục       | SRS_ADMIN_QL_DANHMUC.md |
| F20 | Quản lý đơn ứng tuyển  | SRS_ADMIN_QL_DONTD.md   |
| F21 | Thống kê hệ thống      | SRS_ADMIN_THONGKE.md    |

---

## 3. Ma trận phân quyền

| Chức năng             | Khách | Ứng viên | Nhà tuyển dụng | Admin |
| --------------------- | ----- | -------- | -------------- | ----- |
| Xem trang chủ         | ✅    | ✅       | ✅             | ✅    |
| Tìm kiếm việc làm     | ✅    | ✅       | ✅             | ✅    |
| Xem chi tiết tin      | ✅    | ✅       | ✅             | ✅    |
| Đăng ký / Đăng nhập   | ✅    | ❌       | ❌             | ❌    |
| Nộp đơn ứng tuyển     | ❌    | ✅       | ❌             | ❌    |
| Lưu việc làm          | ❌    | ✅       | ❌             | ❌    |
| Xem lịch sử ứng tuyển | ❌    | ✅       | ❌             | ❌    |
| Quản lý hồ sơ         | ❌    | ✅       | ✅             | ❌    |
| Đăng tin tuyển dụng   | ❌    | ❌       | ✅             | ❌    |
| Quản lý tin đã đăng   | ❌    | ❌       | ✅             | ✅    |
| Xem ứng viên          | ❌    | ❌       | ✅             | ✅    |
| Duyệt tin             | ❌    | ❌       | ❌             | ✅    |
| Quản lý người dùng    | ❌    | ❌       | ❌             | ✅    |
| Thống kê              | ❌    | ❌       | ❌             | ✅    |

---

## 4. Kiến trúc hệ thống

```
┌─────────────────────────────────────────────────┐
│                   FRONTEND                       │
│  public/*.php (Giao diện PHP render)             │
│  public/js/*.js (Logic phía client)              │
│  public/assets/css/tailwind.css                  │
└────────────────────┬────────────────────────────┘
                     │ Fetch / AJAX
┌────────────────────▼────────────────────────────┐
│                REST API LAYER                    │
│  public/api/*.php (Endpoint xử lý request)      │
│  src/Controllers/ (Business logic)              │
│  src/Middleware/ (Xác thực + phân quyền)        │
└────────────────────┬────────────────────────────┘
                     │ SQL
┌────────────────────▼────────────────────────────┐
│                DATA LAYER                        │
│  src/Models/ (Truy vấn database)                │
│  src/Services/ (Logic dịch vụ)                  │
│  MySQL: database vieclamphp                     │
└─────────────────────────────────────────────────┘
```

---

## 5. Quy ước chung

| Quy ước      | Mô tả                                                                                                      |
| ------------ | ---------------------------------------------------------------------------------------------------------- |
| Xác thực     | JWT Token lưu trong `localStorage`, kiểm tra qua `AuthMiddleware`                                          |
| API response | Luôn trả về `{ success: bool, data/message: ... }`                                                         |
| Mã HTTP      | 200 thành công, 400 dữ liệu sai, 401 chưa xác thực, 403 không có quyền, 404 không tìm thấy, 500 lỗi server |
| Mật khẩu     | Hash bằng `password_hash()` / `password_verify()` của PHP                                                  |
| Upload file  | Lưu tại `public/uploads/`, kiểm tra định dạng + dung lượng phía server                                     |
| Phân trang   | Mặc định 10 bản ghi / trang, tham số `?page=N&limit=10`                                                    |
