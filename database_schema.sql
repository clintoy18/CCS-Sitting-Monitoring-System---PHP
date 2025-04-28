-- Create database
CREATE DATABASE IF NOT EXISTS ccsmonitoringsystem;
USE ccsmonitoringsystem;

-- Drop tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS sit_in_records;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS computers;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS studentinfo;
DROP TABLE IF EXISTS admins;

-- Create admins table
CREATE TABLE `admins` (
  `admin_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create studentinfo table
CREATE TABLE `studentinfo` (
  `idno` varchar(20) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `midname` varchar(50) DEFAULT NULL,
  `course` varchar(20) NOT NULL,
  `year_level` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `session` int(11) DEFAULT 5,
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create rooms table
CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create computers table
CREATE TABLE `computers` (
  `computer_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `computer_name` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `last_used` datetime DEFAULT NULL,
  PRIMARY KEY (`computer_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `computers_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create announcements table
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create sit_in_records table
CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idno` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `course` varchar(20) NOT NULL,
  `sitin_purpose` varchar(100) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `computer` varchar(50) DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idno` (`idno`),
  CONSTRAINT `sit_in_records_ibfk_1` FOREIGN KEY (`idno`) REFERENCES `studentinfo` (`idno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create reservations table
CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `computer_id` int(11) DEFAULT NULL,
  `idno` varchar(20) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'reserved',
  PRIMARY KEY (`reservation_id`),
  KEY `room_id` (`room_id`),
  KEY `idno` (`idno`),
  KEY `computer_id` (`computer_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`idno`) REFERENCES `studentinfo` (`idno`) ON DELETE CASCADE,
  CONSTRAINT `fk_reservations_computer` FOREIGN KEY (`computer_id`) REFERENCES `computers` (`computer_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create feedbacks table
CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studentinfo` (`idno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data

-- Admin account
INSERT INTO `admins` (`admin_id`, `name`, `password`) VALUES
('admin123', 'Administrator', 'admin123');

-- Sample rooms
INSERT INTO `rooms` (`room_name`, `capacity`, `status`) VALUES
('524', 30, 'available'),
('526', 30, 'available'),
('528', 30, 'available'),
('530', 30, 'available'),
('542', 30, 'available'),
('544', 30, 'available'),
('517', 30, 'available');

-- Sample computers for each room
INSERT INTO `computers` (`room_id`, `computer_name`, `status`) VALUES
(1, 'PC-101', 'available'),
(1, 'PC-102', 'available'),
(1, 'PC-103', 'available'),
(1, 'PC-104', 'available'),
(1, 'PC-105', 'available'),
(2, 'PC-201', 'available'),
(2, 'PC-202', 'available'),
(2, 'PC-203', 'available'),
(2, 'PC-204', 'available'),
(2, 'PC-205', 'available'),
(3, 'PC-301', 'available'),
(3, 'PC-302', 'available'),
(3, 'PC-303', 'available'),
(3, 'PC-304', 'available'),
(3, 'PC-305', 'available'),
(4, 'PC-401', 'available'),
(4, 'PC-402', 'available'),
(4, 'PC-403', 'available'),
(4, 'PC-404', 'available'),
(4, 'PC-405', 'available'),
(5, 'PC-501', 'available'),
(5, 'PC-502', 'available'),
(5, 'PC-503', 'available'),
(5, 'PC-504', 'available'),
(5, 'PC-505', 'available'),
(6, 'PC-601', 'available'),
(6, 'PC-602', 'available'),
(6, 'PC-603', 'available'),
(6, 'PC-604', 'available'),
(6, 'PC-605', 'available'),
(7, 'PC-701', 'available'),
(7, 'PC-702', 'available'),
(7, 'PC-703', 'available'),
(7, 'PC-704', 'available'),
(7, 'PC-705', 'available');

-- Sample announcement
INSERT INTO `announcements` (`content`, `created_at`) VALUES
('Welcome to the CCS Sit-In Monitoring System! You can now select specific computers when making a reservation.', CURRENT_TIMESTAMP);

-- Note: You'll need to register students through the application interface
-- The system will hash their passwords and handle their registration 