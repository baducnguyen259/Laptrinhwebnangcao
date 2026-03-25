# Software Requirement Specification (SRS)

## Chức năng: Ứng tuyển công việc

**Mã:** APP-01
**Vai trò:** User

---

## 1. Mô tả

Cho phép user ứng tuyển công việc.

---

## 2. Workflow

| Bước | Hành động   | Hệ thống    |
| ---- | ----------- | ----------- |
| 1    | Click Apply | Check login |
| 2    | Upload CV   | Validate    |
| 3    | Submit      | Save DB     |

---

## 3. Data

### applications

- user_id
- job_id
- cv
- status

---

## 4. Error

- Chưa login
- CV lỗi

---
