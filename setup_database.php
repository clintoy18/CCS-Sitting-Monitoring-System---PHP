<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ccsmonitoringsystem3';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($database);

// Create tables
$tables = [
    // Admins table
    "CREATE TABLE IF NOT EXISTS `admins` (
        `admin_id` varchar(20) NOT NULL,
        `name` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        PRIMARY KEY (`admin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Announcements table
    "CREATE TABLE IF NOT EXISTS `announcements` (
        `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
        `content` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`announcement_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Rooms table
    "CREATE TABLE IF NOT EXISTS `rooms` (
        `room_id` int(11) NOT NULL AUTO_INCREMENT,
        `room_name` varchar(50) NOT NULL,
        `capacity` int(11) NOT NULL DEFAULT 30,
        `status` enum('available','maintenance','full') NOT NULL DEFAULT 'available',
        PRIMARY KEY (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Computers table
    "CREATE TABLE IF NOT EXISTS `computers` (
        `computer_id` int(11) NOT NULL AUTO_INCREMENT,
        `room_id` int(11) NOT NULL,
        `computer_name` varchar(50) NOT NULL,
        `status` enum('available','in-use','maintenance') NOT NULL DEFAULT 'available',
        `last_used` datetime DEFAULT NULL,
        PRIMARY KEY (`computer_id`),
        KEY `room_id` (`room_id`),
        CONSTRAINT `computers_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Student info table
    "CREATE TABLE IF NOT EXISTS `studentinfo` (
        `idno` varchar(20) NOT NULL,
        `fname` varchar(50) NOT NULL,
        `lname` varchar(50) NOT NULL,
        `midname` varchar(50) DEFAULT NULL,
        `course` enum('BSIT','BSCS','BSIS','BSEMC') NOT NULL,
        `year_level` enum('1','2','3','4') NOT NULL,
        `address` varchar(255) DEFAULT NULL,
        `password` varchar(255) NOT NULL,
        `session` int(11) DEFAULT 30,
        `profile_picture` varchar(255) DEFAULT NULL,
        `points` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`idno`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Lab schedules table
    "CREATE TABLE IF NOT EXISTS `lab_schedules` (
        `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
        `room_id` int(11) NOT NULL,
        `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
        `schedule_date` date DEFAULT NULL,
        `start_time` time NOT NULL,
        `end_time` time NOT NULL,
        `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `status` enum('active','inactive','cancelled') NOT NULL DEFAULT 'active',
        PRIMARY KEY (`schedule_id`),
        KEY `room_id` (`room_id`),
        CONSTRAINT `lab_schedules_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Reservations table
    "CREATE TABLE IF NOT EXISTS `reservations` (
        `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
        `room_id` int(11) NOT NULL,
        `computer_id` int(11) DEFAULT NULL,
        `idno` varchar(20) NOT NULL,
        `start_time` datetime NOT NULL,
        `end_time` datetime NOT NULL,
        `status` enum('approved','disapproved','completed','pending') NOT NULL DEFAULT 'pending',
        `sitin_purpose` enum('C Programming','C# Programming','Java Programming','Php Programming','Database','Digital Logic & Design','Embedded Systems & IoT','Python Programming','Systems Integration and Architecture','Computer Application','Web Design and Development','Self-Service Reservation') DEFAULT NULL,
        PRIMARY KEY (`reservation_id`),
        KEY `room_id` (`room_id`),
        KEY `idno` (`idno`),
        KEY `computer_id` (`computer_id`),
        CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
        CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`idno`) REFERENCES `studentinfo` (`idno`),
        CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`computer_id`) REFERENCES `computers` (`computer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Sit-in records table
    "CREATE TABLE IF NOT EXISTS `sit_in_records` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `idno` varchar(20) NOT NULL,
        `name` varchar(100) NOT NULL,
        `course` enum('BSIT','BSCS','BSIS','BSEMC') NOT NULL,
        `sitin_purpose` enum('C Programming','C# Programming','Java Programming','Php Programming','Database','Digital Logic & Design','Embedded Systems & IoT','Python Programming','Systems Integration and Architecture','Computer Application','Web Design and Development','Self-Service Reservation') DEFAULT NULL,
        `lab` varchar(20) DEFAULT NULL,
        `computer` varchar(50) DEFAULT NULL,
        `time_in` datetime NOT NULL,
        `time_out` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idno` (`idno`),
        CONSTRAINT `sit_in_records_ibfk_1` FOREIGN KEY (`idno`) REFERENCES `studentinfo` (`idno`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Feedbacks table
    "CREATE TABLE IF NOT EXISTS `feedbacks` (
        `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` varchar(20) NOT NULL,
        `feedback` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`feedback_id`),
        KEY `student_id` (`student_id`),
        CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studentinfo` (`idno`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    // Lab resources table
    "CREATE TABLE IF NOT EXISTS `lab_resources` (
        `resource_id` int(11) NOT NULL AUTO_INCREMENT,
        `resource_title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        `resource_link` varchar(255) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`resource_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
];

// Execute each table creation query
foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Populate rooms
$rooms = [
    ['room_name' => 'Lab 524', 'capacity' => 30],
    ['room_name' => 'Lab 526', 'capacity' => 30],
    ['room_name' => 'Lab 528', 'capacity' => 30],
    ['room_name' => 'Lab 544', 'capacity' => 30],
    ['room_name' => 'Lab 517', 'capacity' => 30]
];

foreach ($rooms as $room) {
    $sql = "INSERT IGNORE INTO rooms (room_name, capacity) VALUES ('{$room['room_name']}', {$room['capacity']})";
    if ($conn->query($sql) === TRUE) {
        echo "Room {$room['room_name']} added successfully<br>";
    } else {
        echo "Error adding room {$room['room_name']}: " . $conn->error . "<br>";
    }
}

// Populate computers for each room
$roomQuery = "SELECT room_id, room_name FROM rooms";
$roomResult = $conn->query($roomQuery);

if ($roomResult) {
    while ($room = $roomResult->fetch_assoc()) {
        // Add 30 computers for each room
        for ($i = 1; $i <= 30; $i++) {
            $computerName = "PC-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $sql = "INSERT IGNORE INTO computers (room_id, computer_name, status) 
                    VALUES ({$room['room_id']}, '{$computerName}', 'available')";
            
            if ($conn->query($sql) === TRUE) {
                echo "Computer {$computerName} added to {$room['room_name']}<br>";
            } else {
                echo "Error adding computer {$computerName}: " . $conn->error . "<br>";
            }
        }
    }
}

// Close connection
$conn->close();

echo "<br>Database setup and population completed!";
?> 