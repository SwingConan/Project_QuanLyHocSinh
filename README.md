
# 🚀 HƯỚNG DẪN LÀM VIỆC NHÓM BẰNG GITHUB  
(Dành cho dự án PHP MVC – XAMPP – MySQL)  
👑 Nhóm trưởng: **Lợi**

---

## 1️⃣ Git & GitHub là gì? (Hiểu một lần là xong)

| Khái niệm | Giải thích siêu dễ |
|---------|------------------|
| **Git** | Lưu lịch sử code trên máy mỗi người |
| **GitHub** | Nơi đưa code lên mạng để nhiều người làm chung |
| **Repository (repo)** | Dự án chung của cả nhóm |
| **Branch** | Nhánh làm việc riêng để tránh giẫm code nhau |
| **Pull Request (PR)** | Yêu cầu ghép code từ nhánh vào `main` |

➡ Tư duy chính:  
> **Mỗi người làm riêng trên nhánh của mình → Xong mới ghép vào main qua PR**

---

## 2️⃣ Quy tắc vàng của nhóm

✅ Không ai được đụng code trực tiếp trong `main`  
✅ Mỗi tính năng → **1 nhánh riêng**  
✅ Code xong → **Push → Tạo Pull Request → Nhóm trưởng merge**  
✅ Khi main có cập nhật → ai cũng phải **pull** về

---

## 3️⃣ Tên nhánh mẫu cho dự án MVC

| Thành viên | Nhiệm vụ | Tên nhánh |
|-----------|---------|----------|
| Lợi | Auth + Quản lý merge | `feature/auth` |
| Duy | View – Giao diện home | `feature/view-home` |
| Tùng | Model + DB | `feature/model-product` |
| Bảo | Controller – Cart | `feature/controller-cart` |
| Diễn | UI sản phẩm | `feature/view-product` |
| Cơ | Navigation + Footer | `feature/layout` |

Bạn có thể tùy chỉnh theo phân công thực tế.

---

## 4️⃣ Hướng dẫn thực tế (Theo từng bước)

🟦 **Bước 1 — Lấy dự án về máy (chỉ làm 1 lần)**

```bash
git clone https://github.com/<your-team>/<repo>.git
cd <repo>
```

---

🟩 **Bước 2 — Trước khi bắt đầu code**

> Luôn cập nhật code mới nhất từ main

```bash
git checkout main
git pull origin main
```

---

🟨 **Bước 3 — Tạo nhánh riêng để làm tính năng**

```bash
git checkout -b feature/tinh-nang
```

VD:
```bash
git checkout -b feature/login
```

💡 Mỗi người chỉ làm trên **nhánh của mình**

---

🟧 **Bước 4 — Code xong thì lưu lại (commit)**

```bash
git add .
git commit -m "feat: xong giao dien login"
```

---

🟥 **Bước 5 — Đưa nhánh lên GitHub**

```bash
git push origin feature/login
```

---

🟪 **Bước 6 — Tạo Pull Request (PR)**

Trên GitHub:
- Menu **Pull Requests**
- Bấm **New Pull Request**
- Chọn nhánh của bạn → so với `main`
- Bấm **Create Pull Request**
- Ghi mô tả đã làm gì

➡ Chờ nhóm trưởng kiểm tra

---

🟫 **Bước 7 — Nhóm trưởng merge**

1. Kiểm tra code trong tab **Files changed**  
2. Nếu ổn → bấm **Merge Pull Request**  
3. **Xoá nhánh cũ** để tránh rối

---

♻️ **Bước 8 — Tất cả cập nhật code mới nhất**

Sau mỗi lần merge:

```bash
git checkout main
git pull origin main
```

→ Tất cả có phiên bản code mới nhất

---

# 5️⃣ Làm việc với Database chung

- Cấu trúc bảng được để trong:  
  `db/schema.sql`

- Nếu ai thay đổi bảng → phải:
  ✅ cập nhật lại `schema.sql`  
  ✅ thông báo nhóm pull & import lại DB

---

# 6️⃣ CRUD Git dành cho nhóm (dễ nhớ)

| Mục đích | Lệnh | Khi nào dùng |
|--------|------|-------------|
| Lấy code từ GitHub | `git pull origin main` | Mỗi lần chuẩn bị làm |
| Tạo nhánh mới | `git checkout -b feature/x` | Bắt đầu 1 tính năng |
| Chuyển nhánh | `git checkout main` | Khi muốn quay lại main |
| Lưu file | `git add .` | Sau khi chỉnh file |
| Ghi chú thay đổi | `git commit -m ""` | Khi hoàn thành 1 bước |
| Đẩy code lên GitHub | `git push origin feature/x` | Khi muốn gửi cho nhóm |

---

# 7️⃣ Lỗi thường gặp & cách xử lý

| Lỗi | Nguyên nhân | Cách xử lý |
|----|-------------|-----------|
| Push bị từ chối | Bạn đang push vào main | Tạo PR |
| Code bị ghi đè | Làm chung trên main | Tạo nhánh riêng |
| Xung đột code (merge conflict) | 2 người sửa 1 file | Ngồi đối chiếu rồi sửa lại |
| Không pull được | Lịch sử lệch | `git pull --rebase origin main` |

---

## ✅ Tóm tắt lại chỉ 3 dòng cho nhớ

> **1. Không đụng code main**  
> **2. Mỗi người 1 nhánh riêng**  
> **3. Merge qua Pull Request**

Tuân thủ 3 điều này → **Không bao giờ giẫm code nhau** ✅

---

# 📌 Checklist (nhắc lại cho nhóm)

| Hành động | Ai làm |
|---------|--------|
| Merge PR | Lợi 👑 |
| Review PR | Mọi người comment, Lợi approve |
| Quản lý branch | Nhóm trưởng |
| Cập nhật `schema.sql` khi đổi DB | Người chỉnh DB |
| Tất cả phải `pull origin main` hằng ngày | Mỗi thành viên |

---

