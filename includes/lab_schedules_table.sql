-- Drop the lab_schedules table if it exists
DROP TABLE IF EXISTS lab_schedules;

-- Create the lab_schedules table with proper relationships and constraints
CREATE TABLE `lab_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive','cancelled') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`schedule_id`),
  KEY `room_id` (`room_id`),
  KEY `schedule_date` (`schedule_date`),
  KEY `day_of_week` (`day_of_week`),
  KEY `is_recurring` (`is_recurring`),
  KEY `status` (`status`),
  CONSTRAINT `lab_schedules_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `chk_time_range` CHECK (`start_time` < `end_time`),
  CONSTRAINT `chk_recurring_date` CHECK ((`is_recurring` = 1 AND `schedule_date` IS NULL) OR (`is_recurring` = 0 AND `schedule_date` IS NOT NULL))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample lab schedules
-- Recurring schedules
INSERT INTO `lab_schedules` (`room_id`, `day_of_week`, `schedule_date`, `start_time`, `end_time`, `is_recurring`, `status`) VALUES
(1, 'Monday', NULL, '08:00:00', '10:00:00', 1, 'active'),
(1, 'Wednesday', NULL, '13:00:00', '15:00:00', 1, 'active'),
(2, 'Tuesday', NULL, '09:00:00', '11:00:00', 1, 'active'),
(2, 'Thursday', NULL, '14:00:00', '16:00:00', 1, 'active'),
(3, 'Monday', NULL, '10:00:00', '12:00:00', 1, 'active'),
(3, 'Friday', NULL, '13:00:00', '15:00:00', 1, 'active');

-- Specific date schedules
INSERT INTO `lab_schedules` (`room_id`, `day_of_week`, `schedule_date`, `start_time`, `end_time`, `is_recurring`, `status`) VALUES
(1, 'Monday', '2024-03-25', '08:00:00', '12:00:00', 0, 'active'),
(2, 'Wednesday', '2024-03-27', '13:00:00', '17:00:00', 0, 'active'),
(3, 'Friday', '2024-03-29', '09:00:00', '13:00:00', 0, 'active'); 