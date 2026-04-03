# SLMS — Student Leave Management System
**Madhav Institute of Technology & Science, Gwalior**
Developed by **Tejas Pratap Singh** & **Shivanshu Yadav**

---

## What is this project?
A web-based Leave Management System where:
- **Students** apply for leaves online
- **Faculty** approves or rejects leave requests
- **Admin** manages faculty accounts

Built with: **PHP, MySQL, HTML, CSS** — runs on **XAMPP**

---

## Folder Structure

```
SLMS/
│
├── assets/                  ← Images used in the UI
│   ├── campus.jpg           ← College campus photo (login page background)
│   └── mits_logo.png        ← MITS college logo
│
├── includes/                ← Reusable PHP components shared across pages
│   ├── db_connect.php       ← Connects to the MySQL database
│   ├── navbar.php           ← Top navigation bar (shown on all dashboards)
│   └── session_check.php    ← Ensures user is logged in before accessing pages
│
├── index.php                ← LOGIN PAGE — entry point of the application
├── student_dashboard.php    ← STUDENT: Apply for leave, view leave history
├── faculty_dashboard.php    ← FACULTY: Register students, approve/reject leaves
├── admin_dashboard.php      ← ADMIN: Register new faculty members
├── logout.php               ← Destroys session and redirects to login
│
├── database_schema.sql      ← SQL file to create the database tables
└── README.md                ← This file
```

---

## How to Run (XAMPP Setup)

1. Copy the `SLMS` folder to `C:\xampp\htdocs\`
2. Start **Apache** and **MySQL** from the XAMPP Control Panel
3. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
4. Create a database named `slms_db`
5. Import `database_schema.sql` into `slms_db`
6. Open the app → `http://localhost/SLMS/`

---

## How Each File Works

### `index.php` — Login Page
- Accepts email + password via HTML form (`POST`)
- Queries `users` table in the database
- Verifies password using `password_verify()` (bcrypt hashing)
- Redirects to the correct dashboard based on user role

### `includes/db_connect.php` — Database Connection
- Uses PHP `mysqli` to connect to MySQL
- Included at the top of every page that needs database access

### `includes/session_check.php` — Session Guard
- Checks if `$_SESSION['user_id']` exists
- Redirects to login if not — prevents unauthorized access

### `includes/navbar.php` — Navigation Bar
- Shows the user's name, role, and department
- Has a **Logout** button linking to `logout.php`
- Included at the top of all dashboard pages

### `student_dashboard.php` — Student Page
- **Apply for Leave**: form sends reason, start date, end date → saved to `leaves` table as `Pending`
- **Leave History**: fetches all leaves for this student from `leaves` table, shows status badges

### `faculty_dashboard.php` — Faculty Page
- **Register Student**: inserts a new student user with hashed password into `users` table
- **Pending Requests**: lists all `Pending` leaves, faculty can Approve/Reject with a comment

### `admin_dashboard.php` — Admin Page
- **Register Faculty**: inserts a new faculty user with role, department, designation

### `logout.php`
- Calls `session_destroy()` and redirects to `index.php`

---

## Database Tables

### `users`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment user ID |
| name | VARCHAR | Full name |
| email | VARCHAR | Login email (unique) |
| password | VARCHAR | Bcrypt hashed password |
| role | ENUM | `student`, `faculty`, `admin` |
| department | VARCHAR | e.g. CS, IT, ECE |
| rollNumber | VARCHAR | Student roll number |
| designation | VARCHAR | Faculty designation |

### `leaves`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment leave ID |
| user_id | INT (FK) | References `users.id` |
| reason | TEXT | Reason for leave |
| startDate | DATE | Leave start date |
| endDate | DATE | Leave end date |
| status | ENUM | `Pending`, `Approved`, `Rejected` |
| facultyComment | TEXT | Faculty's response comment |
| created_at | TIMESTAMP | When the leave was applied |
