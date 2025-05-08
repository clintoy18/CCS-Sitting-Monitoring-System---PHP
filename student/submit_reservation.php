<?php
session_start();
include "../includes/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $computer_id = $_POST['computer_id'];
    $purpose = $_POST['purpose'];
    $idno = $_SESSION['idno'];
    
    // Insert reservation with pending status
    $reservation_sql = "INSERT INTO reservations (idno, room_id, computer_id, sitin_purpose, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($reservation_sql);
    $stmt->bind_param("iiis", $idno, $room_id, $computer_id, $purpose);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reservation submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting reservation']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 