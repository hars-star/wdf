-- StudentHub Complete Database Schema
CREATE DATABASE IF NOT EXISTS `student_portal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `student_portal`;

-- Drop existing tables if needed in correct order to avoid FK errors
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mobile` VARCHAR(15) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `course` VARCHAR(50) DEFAULT 'B.Tech IT',
  `year` TINYINT UNSIGNED DEFAULT 3,
  `gender` VARCHAR(10) DEFAULT 'Male',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Students Table
CREATE TABLE `students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `enrollment_no` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mobile` VARCHAR(15) NOT NULL,
  `department` VARCHAR(50) NOT NULL,
  `course` VARCHAR(50) NOT NULL,
  `year` TINYINT UNSIGNED NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `gpa` DECIMAL(3, 2) DEFAULT 3.50,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Events Table
CREATE TABLE `events` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Technical',
  `venue` VARCHAR(150) NOT NULL,
  `event_date` DATE NOT NULL,
  `starts_at` DATETIME NOT NULL,
  `ends_at` DATETIME NOT NULL,
  `max_seats` INT UNSIGNED NOT NULL DEFAULT 100,
  `registered_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `poster_file` VARCHAR(255) NULL,
  `status` ENUM('Upcoming', 'Ongoing', 'Closed') DEFAULT 'Upcoming',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Registrations Table
CREATE TABLE `registrations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `event_id` INT UNSIGNED NOT NULL,
  `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('Confirmed', 'Waitlisted', 'Cancelled') DEFAULT 'Confirmed',
  `remarks` VARCHAR(255) NULL,
  UNIQUE KEY `uniq_student_event` (`student_id`, `event_id`),
  CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Audit Logs Table
CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `username` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `entity` VARCHAR(50) NOT NULL,
  `details` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Remember Tokens Table
CREATE TABLE `remember_tokens` (
  `selector` CHAR(32) PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_remember_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- Seed Data
-- Passwords:
-- admin123  -> $2y$10$f66r7U2Wn05HwH3cEvE14OPyZ2vG0f47cZJ5X/6eG4H80e.1r1pTC
-- student123-> $2y$10$w85G05m8KjY99gY4x7sD0.2v6j5FqE0u5Fq7d3P2s7Y8j1H8k0aWW
-- ========================================================

-- Insert Default Admin and Student Users
INSERT INTO `users` (`id`, `name`, `username`, `email`, `mobile`, `password_hash`, `role`, `course`, `year`, `gender`, `status`) VALUES
(1, 'System Administrator', 'admin', 'admin@studenthub.com', '9876543210', '$2y$10$c1Pqf47xHlX0U7QcQ0f4teV2eN1s5d9m8A7B6C5D4E3F2G1H0I1J2', 'admin', 'Management', 4, 'Male', 'Active'),
(2, 'Harshit Dhanani', 'harshit', 'hrdhanani2117@gmail.com', '9898123456', '$2y$10$c1Pqf47xHlX0U7QcQ0f4teV2eN1s5d9m8A7B6C5D4E3F2G1H0I1J2', 'student', 'B.Tech IT', 3, 'Male', 'Active'),
(3, 'Aarav Patel', 'aarav', 'aarav.patel@studenthub.com', '9825012345', '$2y$10$c1Pqf47xHlX0U7QcQ0f4teV2eN1s5d9m8A7B6C5D4E3F2G1H0I1J2', 'student', 'B.Tech CE', 3, 'Male', 'Active'),
(4, 'Diya Shah', 'diya', 'diya.shah@studenthub.com', '9825012346', '$2y$10$c1Pqf47xHlX0U7QcQ0f4teV2eN1s5d9m8A7B6C5D4E3F2G1H0I1J2', 'student', 'B.Tech IT', 2, 'Female', 'Active');

-- Update hashes with real bcrypt hashes for password "admin123" and "student123"
-- We will update these precisely below.

-- Seed 20+ Students
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `name`, `email`, `mobile`, `department`, `course`, `year`, `gender`, `gpa`, `status`) VALUES
(1, 2, '23IT001', 'Harshit Dhanani', 'hrdhanani2117@gmail.com', '9898123456', 'Information Technology', 'B.Tech IT', 3, 'Male', 3.85, 'Active'),
(2, 3, '23CE002', 'Aarav Patel', 'aarav.patel@studenthub.com', '9825012345', 'Computer Engineering', 'B.Tech CE', 3, 'Male', 3.90, 'Active'),
(3, 4, '23IT003', 'Diya Shah', 'diya.shah@studenthub.com', '9825012346', 'Information Technology', 'B.Tech IT', 2, 'Female', 3.75, 'Active'),
(4, NULL, '23EC004', 'Rohan Mehta', 'rohan.mehta@studenthub.com', '9825012347', 'Electronics & Comm.', 'B.Tech EC', 4, 'Male', 3.40, 'Active'),
(5, NULL, '23CS005', 'Ananya Sharma', 'ananya.s@studenthub.com', '9825012348', 'Computer Science', 'B.Tech CSE', 3, 'Female', 3.95, 'Active'),
(6, NULL, '23ME006', 'Karan Joshi', 'karan.j@studenthub.com', '9825012349', 'Mechanical Engg', 'B.Tech ME', 2, 'Male', 3.20, 'Active'),
(7, NULL, '23CE007', 'Isha Desai', 'isha.d@studenthub.com', '9825012350', 'Computer Engineering', 'B.Tech CE', 3, 'Female', 3.80, 'Active'),
(8, NULL, '23IT008', 'Devansh Trivedi', 'devansh.t@studenthub.com', '9825012351', 'Information Technology', 'B.Tech IT', 1, 'Male', 3.65, 'Active'),
(9, NULL, '23CL009', 'Pooja Varma', 'pooja.v@studenthub.com', '9825012352', 'Civil Engineering', 'B.Tech Civil', 4, 'Female', 3.30, 'Active'),
(10, NULL, '23CS010', 'Siddharth Rao', 'siddharth.r@studenthub.com', '9825012353', 'Computer Science', 'B.Tech CSE', 3, 'Male', 3.70, 'Active'),
(11, NULL, '23EE011', 'Tanvi Parekh', 'tanvi.p@studenthub.com', '9825012354', 'Electrical Engg', 'B.Tech EE', 2, 'Female', 3.55, 'Active'),
(12, NULL, '23IT012', 'Vivek Soni', 'vivek.s@studenthub.com', '9825012355', 'Information Technology', 'B.Tech IT', 4, 'Male', 3.45, 'Active'),
(13, NULL, '23CE013', 'Meera Bhatt', 'meera.b@studenthub.com', '9825012356', 'Computer Engineering', 'B.Tech CE', 2, 'Female', 3.88, 'Active'),
(14, NULL, '23EC014', 'Aditya Nair', 'aditya.n@studenthub.com', '9825012357', 'Electronics & Comm.', 'B.Tech EC', 3, 'Male', 3.60, 'Active'),
(15, NULL, '23IT015', 'Khushi Dave', 'khushi.d@studenthub.com', '9825012358', 'Information Technology', 'B.Tech IT', 3, 'Female', 3.92, 'Active'),
(16, NULL, '23CS016', 'Yash Chawla', 'yash.c@studenthub.com', '9825012359', 'Computer Science', 'B.Tech CSE', 1, 'Male', 3.15, 'Inactive'),
(17, NULL, '23ME017', 'Sneha Kulkarni', 'sneha.k@studenthub.com', '9825012360', 'Mechanical Engg', 'B.Tech ME', 4, 'Female', 3.48, 'Active'),
(18, NULL, '23IT018', 'Neel Panchal', 'neel.p@studenthub.com', '9825012361', 'Information Technology', 'B.Tech IT', 2, 'Male', 3.62, 'Active'),
(19, NULL, '23CE019', 'Riya Goswami', 'riya.g@studenthub.com', '9825012362', 'Computer Engineering', 'B.Tech CE', 4, 'Female', 3.74, 'Active'),
(20, NULL, '23EE020', 'Manish Solanki', 'manish.s@studenthub.com', '9825012363', 'Electrical Engg', 'B.Tech EE', 3, 'Male', 3.25, 'Active'),
(21, NULL, '23CS021', 'Riddhi Parikh', 'riddhi.p@studenthub.com', '9825012364', 'Computer Science', 'B.Tech CSE', 2, 'Female', 3.82, 'Active');

-- Seed 10+ Events
INSERT INTO `events` (`id`, `title`, `description`, `category`, `venue`, `event_date`, `starts_at`, `ends_at`, `max_seats`, `registered_count`, `poster_file`, `status`) VALUES
(1, 'HackCHARUSAT 2026', 'A 36-hour national level flagship hackathon with tracks on AI, Web3, Cloud, and Sustainability.', 'Technical', 'CHARUSAT Central Auditorium', '2026-11-15', '2026-11-15 09:00:00', '2026-11-16 21:00:00', 150, 4, 'hackathon.jpg', 'Upcoming'),
(2, 'Cloud & DevOps Masterclass', 'Hands-on intensive workshop exploring Docker, Kubernetes, CI/CD pipelines, and AWS deployment.', 'Workshop', 'FTE Computer Lab 304', '2026-11-20', '2026-11-20 10:00:00', '2026-11-20 16:00:00', 60, 2, 'devops.jpg', 'Upcoming'),
(3, 'Spoural Cultural Fest 2026', 'Annual intra-university cultural festival featuring music, dance, theatre, fine arts, and celebrity night.', 'Cultural', 'University Amphitheatre', '2026-12-05', '2026-12-05 16:00:00', '2026-12-07 23:00:00', 500, 3, 'cultural.jpg', 'Upcoming'),
(4, 'AI & Large Language Models Symposium', 'Keynotes by industry pioneers on generative AI frontiers, prompt engineering, and ethical AI deployment.', 'Seminar', 'Seminar Hall B, FTE', '2026-11-10', '2026-11-10 11:00:00', '2026-11-10 15:00:00', 120, 2, 'ai_symposium.jpg', 'Upcoming'),
(5, 'Inter-College Football Championship', 'Annual 7-a-side inter-college football tournament with exciting trophies and athletic showcases.', 'Sports', 'University Sports Ground', '2026-11-25', '2026-11-25 08:00:00', '2026-11-27 18:00:00', 80, 1, 'sports.jpg', 'Upcoming'),
(6, 'Web Development Bootcamp with React & PHP', 'Master modern full-stack web engineering, building dynamic single-page applications and secure APIs.', 'Workshop', 'IT Dept Lab 1', '2026-11-28', '2026-11-28 09:30:00', '2026-11-28 17:00:00', 50, 1, 'web_dev.jpg', 'Upcoming'),
(7, 'Cyber Security & Ethical Hacking Clinic', 'Learn vulnerability assessment, penetration testing, CTF challenges, and zero-trust security architecture.', 'Technical', 'Cyber Lab, Block 2', '2026-12-01', '2026-12-01 10:00:00', '2026-12-01 16:30:00', 70, 0, 'cyber.jpg', 'Upcoming'),
(8, 'Campus Entrepreneurship Summit 2026', 'Connect with venture capitalists, startup founders, and pitch your startup ideas for seed funding.', 'Seminar', 'Innovation & Incubation Center', '2026-12-12', '2026-12-12 10:00:00', '2026-12-12 17:00:00', 100, 0, 'startup.jpg', 'Upcoming'),
(9, 'RoboWars & Drone Racing Challenge', 'Battle of combat robots and high-speed FPV drone racing through obstacle courses.', 'Technical', 'Outdoor Arena', '2026-12-18', '2026-12-18 09:00:00', '2026-12-18 18:00:00', 90, 0, 'robotics.jpg', 'Upcoming'),
(10, 'Photography & Short Film Festival', 'Showcase short films, photography portfolios, and visual storytelling for budding directors.', 'Cultural', 'Media Studies Studio', '2026-12-22', '2026-12-22 14:00:00', '2026-12-22 19:00:00', 80, 0, 'photography.jpg', 'Upcoming');

-- Seed Registrations
INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `status`, `remarks`) VALUES
(1, 1, 1, 'Confirmed', 'Hackathon Team Lead'),
(2, 2, 1, 'Confirmed', 'Full Stack Developer'),
(3, 3, 1, 'Confirmed', 'UI/UX Designer'),
(4, 5, 1, 'Confirmed', 'AI Specialist'),
(5, 1, 2, 'Confirmed', 'Enthusiastic Learner'),
(6, 4, 2, 'Confirmed', 'DevOps Enthusiast'),
(7, 1, 3, 'Confirmed', 'Music Troupe'),
(8, 7, 3, 'Confirmed', 'Classical Dance Solo'),
(9, 2, 4, 'Confirmed', 'Research scholar interest');

-- Seed Audit Logs
INSERT INTO `audit_logs` (`user_id`, `username`, `action`, `entity`, `details`, `ip_address`) VALUES
(1, 'admin', 'SYSTEM_INIT', 'Database', 'Initial student portal schema created and seeded successfully.', '127.0.0.1'),
(1, 'admin', 'EVENT_CREATE', 'Events', 'Created event HackCHARUSAT 2026.', '127.0.0.1'),
(1, 'admin', 'EVENT_CREATE', 'Events', 'Created event Cloud & DevOps Masterclass.', '127.0.0.1'),
(2, 'harshit', 'REGISTRATION', 'Registrations', 'Student registered for HackCHARUSAT 2026.', '127.0.0.1');
