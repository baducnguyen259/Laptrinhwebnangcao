# SRS_TIM_KIEM_VIEC_LAM — Đặc tả chức năng Tìm kiếm việc làm

**Dự án:** Website Tìm Kiếm Việc Làm  
**Nhóm:** Nhóm 13  
**Phiên bản:** 1.0  
**Ngày:** 08/04/2026

---

## 1. Mô tả chức năng

Cho phép người dùng tìm kiếm tin tuyển dụng theo từ khoá, ngành nghề, địa điểm, mức lương và hình thức làm việc. Kết quả hiển thị dạng danh sách card, hỗ trợ phân trang và cập nhật động khi thay đổi bộ lọc.

---

## 2. Điều kiện tiên quyết

| Điều kiện | Mô tả |
|-----------|-------|
| Người dùng | Không yêu cầu đăng nhập |
| Dữ liệu | Bảng `tbl_tintuyendung` (trangthai=1), `tbl_danhmuc`, `tbl_nhatuyendung` |

---

## 3. Luồng xử lý chính

```
Người dùng truy cập timkiem.php (có thể kèm query string)
        │
        ▼
Đọc tham số từ URL: ?keyword=&loai=&diachi=&mucluong=&hinhthuc=&page=
        │
        ▼
jobs.js gọi API: GET /public/api/jobs.php?{params}
        │
        ▼
JobController → JobModel::search()
Truy vấn SQL với các điều kiện lọc kết hợp + LIMIT/OFFSET phân trang
        │
        ▼
Trả về { jobs: [...], total, page, limit }
        │
        ▼
jobs.js render danh sách card tin tuyển dụng + phân trang
        │
        ▼
Người dùng thay đổi bộ lọc → jobs.js gọi lại API (debounce 300ms)
Cập nhật URL (pushState) → Render kết quả mới
```

---

## 4. Use Cases

### UC-01: Tìm kiếm theo từ khoá

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Tất cả người dùng |
| **Mục tiêu** | Tìm tin tuyển dụng theo tên vị trí / công ty |
| **Luồng chính** | 1. Nhập từ khoá → 2. API tìm trong `tieude`, `mota`, `tencongty` → 3. Render kết quả |
| **Kết quả** | Danh sách tin khớp từ khoá, sắp xếp theo ngày đăng mới nhất |

### UC-02: Lọc theo ngành nghề

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Tất cả người dùng |
| **Luồng chính** | 1. Chọn ngành nghề từ dropdown → 2. Cập nhật URL → 3. Gọi API → 4. Render |
| **Kết quả** | Chỉ hiển thị tin thuộc ngành đã chọn |

### UC-03: Lọc kết hợp nhiều tiêu chí

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Tất cả người dùng |
| **Mục tiêu** | Thu hẹp kết quả tìm kiếm |
| **Luồng chính** | 1. Chọn nhiều bộ lọc → 2. API kết hợp tất cả điều kiện WHERE → 3. Render |
| **Kết quả** | Kết quả lọc chính xác theo tất cả tiêu chí đã chọn |

### UC-04: Phân trang kết quả

| Trường | Nội dung |
|--------|----------|
| **Tác nhân** | Tất cả người dùng |
| **Luồng chính** | 1. Kết quả > 10 tin → 2. Hiển thị thanh phân trang → 3. Click trang → 4. Tải trang tiếp |
| **Kết quả** | Hiển thị đúng trang được chọn, URL cập nhật `?page=N` |

### UC-05: Không có kết quả

| Trường | Nội dung |
|--------|----------|
| **Luồng chính** | Tìm kiếm không khớp bất kỳ tin nào |
| **Kết quả** | Hiển thị thông báo "Không tìm thấy việc làm phù hợp" và gợi ý tìm kiếm khác |

---

## 5. Input / Output

### Input — Query string
| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `keyword` | string | ❌ | Từ khoá tìm kiếm (tên job, công ty) |
| `loai` | int | ❌ | ID ngành nghề từ `tbl_danhmuc` |
| `diachi` | string | ❌ | Địa điểm làm việc |
| `mucluong_min` | int | ❌ | Mức lương tối thiểu (triệu đồng) |
| `mucluong_max` | int | ❌ | Mức lương tối đa (triệu đồng) |
| `hinhthuc` | string | ❌ | `fulltime`, `parttime`, `remote`, `intern` |
| `page` | int | ❌ | Trang hiện tại (mặc định: 1) |
| `limit` | int | ❌ | Số tin mỗi trang (mặc định: 10) |

### Output
```json
{
  "success": true,
  "data": {
    "jobs": [
      {
        "id": 1,
        "tieude": "Lập trình viên PHP",
        "tencongty": "Công ty ABC",
        "logo": "uploads/company-logos/abc.png",
        "diadiemlamviec": "Hà Nội",
        "mucluong": "15-25 triệu",
        "hinhthuc": "fulltime",
        "hansotd": "2026-05-01",
        "ngaytao": "2026-04-01"
      }
    ],
    "total": 45,
    "page": 1,
    "limit": 10,
    "total_pages": 5
  }
}
```

---

## 6. Xử lý lỗi

| Tình huống | Xử lý |
|------------|-------|
| Không có kết quả | Hiển thị thông báo "Không tìm thấy việc làm phù hợp. Thử từ khoá khác?" |
| Tham số `page` không hợp lệ | Mặc định về trang 1 |
| Tham số `loai` không tồn tại | Bỏ qua bộ lọc ngành nghề, trả về tất cả |
| Lỗi API | Hiển thị "Đã xảy ra lỗi. Vui lòng thử lại" |
| Tin tuyển dụng hết hạn | Không hiển thị (lọc `hansotd >= NOW()`) |

---

## 7. Giao diện mô tả

```
┌──────────────────────────────────────────────────────┐
│  [Từ khoá...] [Địa điểm▼] [Ngành nghề▼] [Tìm kiếm] │
├────────────────┬─────────────────────────────────────┤
│  BỘ LỌC       │  KẾT QUẢ (45 việc làm)               │
│               │                                      │
│  Mức lương    │  ┌──────────────────────────────┐    │
│  [0──────30M] │  │ Lập trình viên PHP           │    │
│               │  │ Công ty ABC · Hà Nội         │    │
│  Hình thức    │  │ 15-25 triệu · Full-time      │    │
│  [x] Fulltime │  │ Hết hạn: 01/05/2026  [Nộp đơn]│   │
│  [ ] Parttime │  └──────────────────────────────┘    │
│  [ ] Remote   │                                      │
│               │  ┌──────────────────────────────┐    │
│               │  │ ...                          │    │
│               │  └──────────────────────────────┘    │
│               │                                      │
│               │  [1] [2] [3] [4] [5]                 │
└────────────────┴─────────────────────────────────────┘
```

---

## 8. File liên quan

| File | Vai trò |
|------|---------|
| `public/timkiem.php` | Giao diện tìm kiếm |
| `public/api/jobs.php` | Endpoint tìm kiếm + lọc |
| `public/js/jobs.js` | Gọi API, render card, phân trang, cập nhật URL |
| `src/Controllers/Api/JobController.php` | Xử lý logic tìm kiếm |
| `src/Models/JobModel.php` | Truy vấn SQL với bộ lọc kết hợp |
