<?php
session_start();
include "../includes/connection.php";

if (!isset($_SESSION["idno"])) {
    echo json_encode(['reservations' => []]);
    exit;
}

$userID = $_SESSION["idno"];

// Get approved reservations
$query = "SELECT r.*, c.computer_name, rm.room_name 
          FROM reservations r 
          JOIN computers c ON r.computer_id = c.computer_id 
          JOIN rooms rm ON r.room_id = rm.room_id 
          WHERE r.idno = ? AND r.status = 'approved' 
          ORDER BY r.start_time DESC 
          LIMIT 5";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$reservations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'computer_name' => $row['computer_name'],
            'room_name' => $row['room_name'],
            'start_time' => date('M d, Y h:i A', strtotime($row['start_time']))
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['reservations' => $reservations]);
?> 