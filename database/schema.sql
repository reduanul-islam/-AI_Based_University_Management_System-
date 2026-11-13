-- ============================================
-- AI-Based University Management System
-- Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS university_mgmt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE university_mgmt;

-- Users table (polymorphic: admin, student, faculty)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','student','faculty') NOT NULL DEFAULT 'student',
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    head_faculty_id INT DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    department_id INT,
    batch_year INT,
    semester INT DEFAULT 1,
    cgpa DECIMAL(4,2) DEFAULT 0.00,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    enrollment_date DATE,
    status ENUM('active','inactive','graduated','suspended') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Faculty
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    department_id INT,
    designation VARCHAR(100),
    specialization VARCHAR(200),
    phone VARCHAR(20),
    office_room VARCHAR(50),
    joining_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Courses
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    department_id INT,
    credits INT DEFAULT 3,
    description TEXT,
    syllabus TEXT,
    semester_offered INT,
    max_students INT DEFAULT 40,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Course Enrollments
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    faculty_id INT,
    semester VARCHAR(20),
    academic_year VARCHAR(10),
    grade VARCHAR(5) DEFAULT NULL,
    grade_point DECIMAL(3,2) DEFAULT NULL,
    attendance_percent DECIMAL(5,2) DEFAULT 0.00,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (student_id, course_id, academic_year),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id)
);

-- Attendance
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present','absent','late','excused') DEFAULT 'absent',
    marked_by INT,
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id),
    FOREIGN KEY (marked_by) REFERENCES users(id)
);

-- Assignments
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,
    max_marks INT DEFAULT 100,
    weight_percent DECIMAL(5,2) DEFAULT 10.00,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id)
);

-- Assignment Submissions
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255),
    content TEXT,
    marks_obtained DECIMAL(5,2) DEFAULT NULL,
    feedback TEXT,
    ai_feedback TEXT,
    status ENUM('submitted','graded','late','returned') DEFAULT 'submitted',
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Exams
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    type ENUM('midterm','final','quiz','practical') DEFAULT 'midterm',
    exam_date DATETIME,
    duration_minutes INT DEFAULT 120,
    venue VARCHAR(100),
    max_marks INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Exam Results
CREATE TABLE IF NOT EXISTS exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    marks_obtained DECIMAL(5,2),
    grade VARCHAR(5),
    remarks TEXT,
    FOREIGN KEY (exam_id) REFERENCES exams(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Notices / Announcements
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('general','academic','exam','event','urgent') DEFAULT 'general',
    posted_by INT NOT NULL,
    target_role ENUM('all','student','faculty','admin') DEFAULT 'all',
    is_pinned TINYINT(1) DEFAULT 0,
    expires_at DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id)
);

-- AI Chat History
CREATE TABLE IF NOT EXISTS ai_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(64),
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Library Resources
CREATE TABLE IF NOT EXISTS library_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    author VARCHAR(200),
    isbn VARCHAR(20),
    department_id INT,
    copies_total INT DEFAULT 1,
    copies_available INT DEFAULT 1,
    category VARCHAR(100),
    published_year INT,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Book Borrowings
CREATE TABLE IF NOT EXISTS book_borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    borrowed_at DATE NOT NULL,
    due_date DATE NOT NULL,
    returned_at DATE DEFAULT NULL,
    fine_amount DECIMAL(8,2) DEFAULT 0.00,
    FOREIGN KEY (book_id) REFERENCES library_books(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- ============================================
-- SEED DATA
-- ============================================

-- Default admin user (password for all accounts: password)
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@university.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin'),
('Dr. Sarah Ahmed', 'sarah@university.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'faculty'),
('Dr. Karim Hassan', 'karim@university.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'faculty'),
('Alice Rahman', 'alice@student.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'student'),
('Bob Islam', 'bob@student.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'student'),
('Priya Sharma', 'priya@student.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'student');

-- Departments
INSERT INTO departments (name, code, description) VALUES
('Computer Science & Engineering', 'CSE', 'Department of Computer Science and Engineering'),
('Electrical Engineering', 'EEE', 'Department of Electrical and Electronic Engineering'),
('Business Administration', 'BBA', 'School of Business Administration'),
('Mathematics', 'MATH', 'Department of Mathematics');

-- Faculty profiles
INSERT INTO faculty (user_id, employee_id, department_id, designation, specialization, phone, joining_date) VALUES
(2, 'FAC-001', 1, 'Associate Professor', 'Machine Learning, AI', '01711000001', '2018-01-15'),
(3, 'FAC-002', 1, 'Assistant Professor', 'Data Structures, Algorithms', '01711000002', '2020-06-01');

-- Student profiles
INSERT INTO students (user_id, student_id, department_id, batch_year, semester, cgpa, enrollment_date) VALUES
(4, 'STU-2021-001', 1, 2021, 7, 3.75, '2021-01-10'),
(5, 'STU-2021-002', 1, 2021, 7, 3.50, '2021-01-10'),
(6, 'STU-2022-001', 1, 2022, 5, 3.85, '2022-01-10');

-- Courses
INSERT INTO courses (course_code, title, department_id, credits, description, semester_offered, max_students) VALUES
('CSE-401', 'Artificial Intelligence', 1, 3, 'Introduction to AI concepts, search algorithms, and machine learning basics', 7, 40),
('CSE-402', 'Machine Learning', 1, 3, 'Supervised and unsupervised learning, neural networks', 7, 35),
('CSE-301', 'Database Systems', 1, 3, 'Relational databases, SQL, normalization, transactions', 5, 45),
('CSE-201', 'Data Structures', 1, 3, 'Arrays, linked lists, trees, graphs, sorting algorithms', 3, 50),
('CSE-403', 'Deep Learning', 1, 3, 'CNNs, RNNs, transformers, and modern deep learning techniques', 7, 30);

-- Enrollments
INSERT INTO enrollments (student_id, course_id, faculty_id, semester, academic_year, grade, grade_point, attendance_percent) VALUES
(1, 1, 1, '1st', '2024-25', 'A', 4.00, 92.5),
(1, 2, 1, '1st', '2024-25', 'A-', 3.70, 88.0),
(1, 3, 2, '1st', '2024-25', 'B+', 3.30, 85.0),
(2, 1, 1, '1st', '2024-25', 'B+', 3.30, 80.0),
(2, 2, 1, '1st', '2024-25', 'A-', 3.70, 90.0),
(3, 3, 2, '1st', '2024-25', 'A', 4.00, 95.0),
(3, 4, 2, '1st', '2024-25', 'A', 4.00, 97.0);

-- Notices
INSERT INTO notices (title, content, category, posted_by, target_role, is_pinned) VALUES
('Mid-term Examination Schedule', 'Mid-term examinations will be held from 15th June to 25th June 2025. Students are advised to check their hall tickets from the portal.', 'exam', 1, 'all', 1),
('AI Lab Session - New Timings', 'The AI laboratory sessions have been rescheduled. Please check the updated timetable on the portal.', 'academic', 2, 'student', 0),
('University Annual Sports Day', 'Annual sports day will be held on 5th July 2025. All students and faculty are encouraged to participate.', 'event', 1, 'all', 0),
('Scholarship Applications Open', 'Merit-based scholarship applications are now open. Eligible students with CGPA above 3.5 may apply before 30th June.', 'general', 1, 'student', 1);

-- Assignments
INSERT INTO assignments (course_id, faculty_id, title, description, due_date, max_marks) VALUES
(1, 1, 'Implement A* Search Algorithm', 'Implement the A* search algorithm in Python and apply it to a pathfinding problem. Submit source code and a brief report.', '2025-06-20 23:59:00', 100),
(2, 1, 'Linear Regression from Scratch', 'Implement linear regression without using sklearn. Include gradient descent, cost function, and plot the results.', '2025-06-25 23:59:00', 100),
(1, 1, 'AI Ethics Essay', 'Write a 2000-word essay on the ethical implications of AI in healthcare and education.', '2025-07-01 23:59:00', 50);

-- Exams
INSERT INTO exams (course_id, title, type, exam_date, duration_minutes, venue, max_marks) VALUES
(1, 'Artificial Intelligence Midterm', 'midterm', '2025-06-18 09:00:00', 120, 'Hall A-101', 50),
(2, 'Machine Learning Midterm', 'midterm', '2025-06-19 09:00:00', 120, 'Hall B-201', 50),
(1, 'AI Final Examination', 'final', '2025-07-20 09:00:00', 180, 'Main Exam Hall', 100);
