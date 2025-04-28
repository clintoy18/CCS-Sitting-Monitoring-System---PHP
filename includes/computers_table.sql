-- SQL to create computers table
CREATE TABLE IF NOT EXISTS `computers` (
  `computer_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `computer_name` varchar(50) NOT NULL,
  `status` enum('available','in-use','maintenance') NOT NULL DEFAULT 'available',
  `last_used` datetime DEFAULT NULL,
  PRIMARY KEY (`computer_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `computers_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample computers for each room
-- Replace the room_id values with your actual room IDs
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
(2, 'PC-205', 'available'); 