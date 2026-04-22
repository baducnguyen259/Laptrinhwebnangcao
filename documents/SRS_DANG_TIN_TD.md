# SRS_DANG_TIN_TD — Đặc tả chức năng Đăng tin tuyển dụng

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép Nhà tuyển dụng đã đăng nhập tạo mới tin tuyển dụng với đầy đủ thông tin: tiêu đề, mô tả, yêu cầu, quyền lợi, mức lương, địa điểm, hạn nộp hồ sơ. Tin mới tạo sẽ ở trạng thái "Chờ duyệt" cho đến khi Admin phê duyệt.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Đã đăng nhập vai trò Nhà tuyển dụng |
| Hệ thống | Danh mục ngành nghề (`tbl_danhmuc`) đã có dữ liệu |

---

## 3. Luồng xử lý chính

```
Nhà tuyển dụng truy cập dangtin.php
        │
        ▼
AuthMiddleware kiểm tra token + vai trò nhatuyendung
        │
        ▼
Tải danh sách ngành nghề (tbl_danhmuc) để render dropdown
        │
        ▼
NTD điền form → Submit
        │
        ▼
Client validate: tiêu đề, mô tả, hạn nộp không rỗng, hạn > hôm nay
        │
     Lỗi? ──► Hiển thị lỗi inline
        │
        ▼
Gọi API: POST /public/api/jobs.php { action: "create", ... }
        │
        ▼
JobController → JobModel::create()
Lưu tin với trangthai = 0 (chờ duyệt)
        │
        ▼
Thông báo "Đăng tin thành công. Tin đang chờ Admin duyệt"
Chuyển sang quanlytindang.php
```

---

## 4. Use Cases

### UC-01: Đăng tin tuyển dụng mới

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Nhà tuyển dụng đã đăng nhập |
| **Mục tiêu** | Tạo tin tuyển dụng để tìm ứng viên |
| **Điều kiện trước** | Đăng nhập đúng vai trò NTD |
| **Luồng chính** | 1. Điền form → 2. Submit → 3. Tin lưu trạng thái chờ duyệt |
| **Điều kiện sau** | Tin tạo trong `tbl_tintuyendung` với `trangthai=0` |

### UC-02: Hạn nộp hồ sơ không hợp lệ

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | NTD nhập ngày hạn nộp trong quá khứ |
| **Kết quả** | Lỗi: "Hạn nộp hồ sơ phải sau ngày hôm nay" |

---

## 5. Input / Output

### Input
| Trường | Kiểu | Bắt buộc | Validate |
|--------|------|----------|----------|
| `tieude` | string | ✅ | 5–200 ký tự |
| `idloai` | int | ✅ | ID ngành nghề hợp lệ |
| `mota` | text | ✅ | Tối thiểu 50 ký tự |
| `yeucau` | text | ❌ | Tối đa 5000 ký tự |
| `quyenloi` | text | ❌ | Tối đa 5000 ký tự |
| `mucluong` | string | ✅ | VD: "15-25 triệu" hoặc "Thỏa thuận" |
| `diadiemlamviec` | string | ✅ | Tối đa 255 ký tự |
| `kinhnghiem` | string | ❌ | VD: "1-2 năm", "Không yêu cầu" |
| `hinhthuc` | string | ✅ | `fulltime`, `parttime`, `remote`, `intern` |
| `hansotd` | date | ✅ | Phải > ngày hiện tại |

### Output
| Kết quả | HTTP Code | Mô tả |
|---------|-----------|-------|
| Thành công | 201 | `{ success: true, id: 123, message: "Đăng tin thành công" }` |
| Thiếu dữ liệu | 400 | `{ success: false, message: "Vui lòng điền đầy đủ thông tin bắt buộc" }` |
| Chưa đăng nhập | 401 | `{ success: false, message: "Vui lòng đăng nhập" }` |
| Sai vai trò | 403 | `{ success: false, message: "Chỉ nhà tuyển dụng mới được đăng tin" }` |

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Tiêu đề trống | Lỗi inline: "Tiêu đề không được để trống" |
| Hạn nộp quá khứ | Lỗi: "Hạn nộp hồ sơ phải sau ngày hôm nay" |
| Mô tả quá ngắn | Lỗi: "Mô tả phải có ít nhất 50 ký tự" |
| Đăng nhập sai vai trò | Redirect về `dangnhap.php` |

---

## 7. File liên quan

| File | Vai trò |
|------|---------|
| `public/dangtin.php` | Form đăng tin |
| `public/api/jobs.php` | Endpoint tạo tin |
| `public/js/jobs.js` | Validate, gọi API |
| `src/Controllers/Api/JobController.php` | Logic tạo tin |
| `src/Models/JobModel.php` | Lưu vào `tbl_tintuyendung` |
