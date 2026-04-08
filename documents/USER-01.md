# Software Requirement Specification (SRS)

## Chức năng: Quản lý hồ sơ người dùng

**Mã:** USER-01
**Vai trò:** User

---

## 1. Mô tả

Cho phép người dùng quản lý thông tin cá nhân và CV.

---

## 2. Workflow

| Bước | Hành động        | Hệ thống      |
| ---- | ---------------- | ------------- |
| 1    | Truy cập profile | Hiển thị info |
| 2    | Sửa thông tin    | Validate      |
| 3    | Lưu              | Update DB     |

---

## 3. Data

### users

- name
- email
- phone

### profiles

- user_id
- cv
- skill

---

## 4. Error

- Thiếu dữ liệu
- File CV sai

---
