-- Database Name: leave_management_db (Create this database first)

CREATE DATABASE IF NOT EXISTS leave_management_db;
USE leave_management_db;

-- Drop existing tables to recreate with new schema
DROP TABLE IF EXISTS leaves;
DROP TABLE IF EXISTS users;

-- 1. Users Table (Updated with all MERN stack fields)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    department VARCHAR(50),
    rollNumber VARCHAR(20),      -- For students only
    designation VARCHAR(50),     -- For faculty only (Coordinator, HOD)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Leaves Table (Updated with date fields and faculty comment)
CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    facultyComment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed Data with Hashed Passwords
-- All passwords are hashed using PHP password_hash() - bcrypt
-- Password for all users: 123456

-- Admin User
INSERT INTO users (name, email, password, role, department) VALUES 
('Super Admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL);

-- Faculty User
INSERT INTO users (name, email, password, role, department, designation) VALUES 
('Dr. Faculty', 'faculty@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 'CS', 'HOD');

-- Student User
INSERT INTO users (name, email, password, role, department, rollNumber) VALUES 
('John Student', 'student@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CS', 'CS001');

-- Sample Leave Applications for Testing
INSERT INTO leaves (user_id, reason, startDate, endDate, status, facultyComment) VALUES 
(3, 'Medical appointment', '2026-02-10', '2026-02-11', 'Pending', NULL),
(3, 'Family function', '2026-02-15', '2026-02-16', 'Approved', 'Approved for family emergency'),
(3, 'Personal work', '2026-01-20', '2026-01-21', 'Rejected', 'Insufficient reason provided');

-- Display seed credentials
SELECT '=== LOGIN CREDENTIALS ===' as '';
SELECT 'Admin:   admin@test.com   / 123456' as '';
SELECT 'Faculty: faculty@test.com / 123456' as '';
SELECT 'Student: student@test.com / 123456' as '';
