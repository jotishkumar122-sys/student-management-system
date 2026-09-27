[README.md](https://github.com/user-attachments/files/32694117/README.md)
# Student Management System — Devixo Solutions (Week 3, Task 03)

Full-stack CRUD app: **HTML + CSS + Vanilla JS (frontend)** and **PHP + MySQL (backend)** —
Option 1 from the task brief. Koi framework nahi use kiya, sab kuch plain aur readable rakha hai
taake code review mein easily samajh aaye.

## Kya bana hai (feature se feature)

- **Authentication** — Register, Login, Logout. Passwords `password_hash()` se bcrypt mein
  store hote hain, kabhi bhi plain text nahi.
- **Role-based access (bonus)** — Har user ka role `admin` ya `user` hota hai.
  - `admin` → students add/edit/delete kar sakta hai.
  - `user` → sirf dashboard dekh sakta hai, students search/view kar sakta hai, par CRUD nahi.
  - Ye check UI mein (buttons chhup jaate hain) **aur** server mein (`require_admin()`) dono
    jagah hota hai — sirf frontend pe hide karna kaafi nahi hota, kyunki koi bhi direct URL
    hit kar sakta hai.
- **Student Module** — Add, Edit, Delete, Search, View Details — sab PDO prepared statements
  ke through (SQL injection se bachne ke liye).
- **Dashboard** — Total Students, Active Students, New This Month, System Users ke stat cards,
  aur Recent Registrations ki table.
- **Pagination (bonus)** — Students list 8-8 records ke pages mein dikhti hai, search ke saath
  bhi kaam karta hai.
- **Form validation** — Dono taraf: JS se turant feedback (bina reload), aur PHP se dobara
  validate hota hai (kyunki client-side validation bypass ho sakti hai — never trust the client).
- **Loading indicators** — Save/Login/Register buttons submit hone pe disable ho jaate hain aur
  spinner + "Saving..." jaisa text dikhate hain, taake user ko pata chale kuch ho raha hai.
- **Responsive design** — Navbar mobile pe hamburger menu mein fold ho jaata hai, tables scroll
  karte hain, stat cards grid se stack ho jaate hain chhoti screen par.

## Folder structure

```
student-management-system/
├── config/db.php          # PDO connection (yahan apna DB host/user/pass daalein)
├── includes/auth.php      # session + role helper functions
├── includes/header.php    # shared navbar (role ke hisaab se links)
├── includes/footer.php
├── auth/login.php
├── auth/register.php
├── auth/logout.php
├── index.php              # login/dashboard pe redirect
├── dashboard.php
├── students/list.php      # search + pagination
├── students/add.php       # admin only
├── students/edit.php      # admin only
├── students/delete.php    # admin only, POST only
├── students/view.php
├── assets/css/style.css
├── assets/js/script.js
└── sql/schema.sql
```

## Setup (XAMPP / WAMP / Laragon)

1. Project folder ko `htdocs` (ya jo bhi tumhara web root hai) mein copy karo.
2. phpMyAdmin khol ke `sql/schema.sql` import kar do — ye `student_management` database,
   `users` aur `students` tables bana dega, saath mein 3 sample student records bhi.
3. `config/db.php` mein apna DB host/user/password check kar lo (default XAMPP settings
   already daale hain: host `localhost`, user `root`, password khaali).
4. Browser mein `http://localhost/student-management-system/` kholo — ye `auth/login.php`
   pe redirect ho jayega.
5. Pehle **Register** karo apna account — wo by default `user` role ke saath banega.
6. Admin banne ke liye phpMyAdmin mein ye query chalao:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'apna-email@example.com';
   ```
   (Password ko hand se hash likh ke seed nahi kiya, kyunki wo galat/unsafe hota — isliye
   register-then-promote wala safe tareeqa rakha hai.)
7. Dobara login karo — ab "Add Student" button aur Edit/Delete links dikhne lagenge.

## Developer mindset — kuch decisions ka reasoning

- **PDO with prepared statements** har jagah — raw string concatenation se query kabhi nahi
  banayi, taake SQL injection ka risk zero rahe.
- **Server-side validation kabhi skip nahi ki**, chahe JS validation already ho — client-side
  JS ko browser dev tools se bypass kiya ja sakta hai, isliye PHP hamesha final gatekeeper hai.
- **Role check do jagah** — UI mein (behtar UX ke liye, jo cheez use nahi kar sakte wo dikhana
  hi nahi) aur server mein (asli security ke liye).
- **Delete `POST` se hota hai, `GET` se nahi** — taake accidental link click ya crawler kisi
  record ko delete na kar de.
- **Shared header/footer includes** — 8 pages hain, agar navbar har jagah copy-paste karte to
  ek chhota sa change 8 jagah karna padta. Isliye ek hi `includes/header.php` sab pages use
  karte hain.

## Login for testing

- Sample students already seeded hain (`sql/schema.sql` se) — Ayesha Khan, Bilal Ahmed, Sara
  Malik. Dashboard aur list turant populated dikhengi.
- Apna khud ka account register karo, admin banao (upar wala step), phir CRUD test karo.
