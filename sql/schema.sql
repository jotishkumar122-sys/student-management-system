-- ===========================================================
-- Student Management System — Database Schema
-- Devixo Solutions | Week 3 Task 03
-- ===========================================================

CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- ---------- Users (Authentication + Roles) ----------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- NOTE ON CREATING THE FIRST ADMIN
-- Passwords are hashed with PHP's password_hash(), so we can't safely hand-write
-- a working hash here. Easiest path: register a normal account from the app's
-- Register page, then promote that one account to admin with:
--
--   UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
--
-- Every account after that stays a regular 'user' unless an admin changes it.

-- ---------- Students ----------
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    course VARCHAR(100) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    enrollment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- A few sample rows so the dashboard isn't empty on first run
INSERT INTO students (full_name, email, phone, course, status, enrollment_date) VALUES
('Ayesha Khan', 'ayesha.khan@example.com', '03001234567', 'BS Computer Science', 'active', '2025-01-15'),
('Bilal Ahmed', 'bilal.ahmed@example.com', '03011234567', 'BS Information Technology', 'active', '2025-02-10'),
('Sara Malik', 'sara.malik@example.com', '03021234567', 'BS Software Engineering', 'inactive', '2024-09-05');
