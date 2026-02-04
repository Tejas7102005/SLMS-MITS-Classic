# Quick Setup Guide - SLMS PHP

## Step 1: Import Database

1. Start XAMPP/WAMP and ensure MySQL is running
2. Open phpMyAdmin or MySQL command line
3. Run the SQL file:

**Via phpMyAdmin**:
- Click "Import" tab
- Choose file: `database_schema.sql`
- Click "Go"

**Via Command Line**:
```bash
mysql -u root -p < database_schema.sql
```

## Step 2: Test the Application

Open your browser and navigate to:
```
http://localhost/micro project (Classic)/index.php
```

## Login Credentials

All passwords: **123456**

| Role | Email | Dashboard |
|------|-------|-----------|
| Admin | admin@test.com | Add Faculty Members |
| Faculty | faculty@test.com | Add Students, Approve Leaves |
| Student | student@test.com | Apply for Leave, View History |

## Quick Test Flow

1. **Login as Student** → Apply for leave
2. **Login as Faculty** → Approve/Reject the leave
3. **Login as Student** → See updated status and faculty comment
4. **Login as Admin** → Add a new faculty member
5. **Login as new Faculty** → Add a new student

---

## Files Created/Modified

### New Files
- `admin_dashboard.php` - Admin dashboard
- `faculty_dashboard.php` - Faculty dashboard  
- `student_dashboard.php` - Student dashboard
- `includes/session_check.php` - Session validation
- `includes/navbar.php` - Navigation bar
- `logout.php` - Logout handler

### Modified Files
- `database_schema.sql` - Updated schema with all MERN fields
- `index.php` - Enhanced authentication with password hashing
- `db_connect.php` - No changes needed

---

## Troubleshooting

**Issue**: "Connection failed" error
- **Fix**: Check MySQL is running in XAMPP/WAMP
- **Fix**: Verify database credentials in `db_connect.php`

**Issue**: "Table doesn't exist" error
- **Fix**: Re-run `database_schema.sql` to create tables

**Issue**: Login fails with correct credentials
- **Fix**: Ensure database was imported (check for hashed passwords)
- **Fix**: Password should be 60 characters long (bcrypt hash)

**Issue**: Redirected to login when accessing dashboard
- **Fix**: Check that session is started
- **Fix**: Clear browser cookies and try again
