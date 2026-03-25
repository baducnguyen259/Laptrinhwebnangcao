# Software Requirement Specification (SRS)

## Chức năng: Quản lý công việc

**Mã:** JOB-01
**Vai trò:** Admin

---

## 1. Mô tả

Admin quản lý danh sách việc làm.

---

## 2. Workflow

| Bước | Hành động    | Hệ thống |
| ---- | ------------ | -------- |
| 1    | Thêm job     | Form     |
| 2    | Nhập dữ liệu | Validate |
| 3    | Lưu          | DB       |

---

## 3. Data

### jobs

- id
- title
- description
- salary
- location

---

## 4. Error

- Thiếu thông tin

---
