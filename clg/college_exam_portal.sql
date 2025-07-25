-- Create Database
CREATE DATABASE IF NOT EXISTS college_exam_portal;
USE college_exam_portal;

-- Users Table for storing login details
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    address TEXT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    marks_card_path VARCHAR(255) NOT NULL,
    experience_letter_path VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'HOD', 'principal', 'student') NOT NULL,
    subject_code VARCHAR(50) DEFAULT NULL, -- for staff subject allocation
    branch VARCHAR(50) DEFAULT NULL, -- for branch-specific staff in HOD view
    joining_date DATE, -- Added joining date
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects Table for storing subjects
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,  -- Corrected field name from 'subject_name' to 'name'
    branch VARCHAR(50) NOT NULL,
    semester INT NOT NULL, -- Added semester
    year INT NOT NULL, -- Added year
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Question Papers Table for storing question papers created by staff
CREATE TABLE question_papers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,  -- Changed from VARCHAR to INT to match users table id
    subject_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    frozen BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Timetables Table for storing class and IA timetables
CREATE TABLE timetables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('class', 'ia') NOT NULL, -- type can be class or internal assessment (IA)
    subject_id INT DEFAULT NULL, -- Optional, relevant for subject-specific timetables
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
);

-- Subject Allocation Table for assigning subjects to staff
CREATE TABLE subject_allocation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT,
    subject_id INT,
    FOREIGN KEY (staff_id) REFERENCES users(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Students Table for student records
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usn VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL
);



-- IA Results Table for storing internal assessment marks
CREATE TABLE ia_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT UNIQUE,
    subject VARCHAR(100),
    marks INT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Attendance Table for storing attendance records
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT UNIQUE,
    subject VARCHAR(100),
    total_classes INT,
    attended_classes INT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Questions Table for storing questions with creator_id reference
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    section VARCHAR(50) NOT NULL,
    question TEXT NOT NULL,
    marks INT NOT NULL,
    creator_id INT NOT NULL, -- Added creator_id to track who added the question
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE question_papers ADD COLUMN exam_time INT NOT NULL;


DROP TABLE IF EXISTS attendance;

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL DEFAULT 'present',
    marked_by INT NOT NULL, -- staff_id from users table
    marked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, subject_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE CASCADE
);


ALTER TABLE students ADD COLUMN branch VARCHAR(50) NOT NULL AFTER usn;


ALTER TABLE attendance 
ADD COLUMN total_classes INT DEFAULT 0, 
ADD COLUMN attended_classes INT DEFAULT 0;



CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE Enquiry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    course VARCHAR(100),
    message TEXT,
    enquiry_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `timetable` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `day` VARCHAR(20) NOT NULL,
  `period1` VARCHAR(100),
  `period2` VARCHAR(100),
  `period3` VARCHAR(100),
  `period4` VARCHAR(100),
  `period5` VARCHAR(100),
  `period6` VARCHAR(100),
  `branch` VARCHAR(50),
  `semester` VARCHAR(10),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


DROP TABLE IF EXISTS subject_allocation;

CREATE TABLE subject_allocation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    staff_id INT NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);
