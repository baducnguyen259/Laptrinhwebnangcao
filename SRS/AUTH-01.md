# Software Requirement Specification (SRS)

## Chức năng: Xác thực người dùng (User Authentication)

**Mã chức năng:** AUTH-01
**Trạng thái:** Completed
**Vai trò:** Admin / User

---

## 1. Mô tả

Cung cấp cơ chế đăng ký, đăng nhập và xác thực người dùng.

---

## 2. Workflow

| Bước | Hành động         | Hệ thống      |
| ---- | ----------------- | ------------- |
| 1    | Truy cập `/login` | Hiển thị form |
| 2    | Nhập thông tin    | Validate      |
| 3    | Submit            | Check DB      |
| 4    | Thành công        | Redirect      |
| 5    | Thất bại          | Báo lỗi       |

---

## 3. Data

### Input

- Email
- Password

### Database (`users`)

- id
- email
- password (hashed)
- role (admin/user)

---

## 4. Security

- HTTPS
- Bcrypt
- CSRF
- Limit login

---

## 5. Error

- Sai email/password
- Tài khoản bị khóa

---
