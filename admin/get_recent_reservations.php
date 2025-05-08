<?php
include "../includes/connection.php";

// Get recent pending reservations
$query = "SELECT r.*, s.fname, s.lname, c.computer_name, rm.room_name 
          FROM reservations r 
          JOIN studentinfo s ON r.idno = s.idno 
          JOIN computers c ON r.computer_id = c.computer_id 
          JOIN rooms rm ON r.room_id = rm.room_id 
          WHERE r.status = 'pending' 
          ORDER BY r.start_time DESC 
          LIMIT 5";

$result = $conn->query($query);
$reservations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'computer_name' => $row['computer_name'],
            'room_name' => $row['room_name'],
            'start_time' => date('M d, Y h:i A', strtotime($row['start_time']))
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['reservations' => $reservations]);
?> 