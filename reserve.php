<?php
include 'connection.php';
session_start();

if (!isset($_SESSION["idno"])) {
    die("User not logged in.");
}

$idno = $_SESSION["idno"];
$room_id = $_POST["room_id"];

// Check if student has remaining sessions
$check_sessions = "SELECT session FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($check_sessions);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row["session"] <= 0) {
    echo "No more reservations allowed.";
    exit;
}

// Insert reservation
$insert_reservation = "INSERT INTO reservations (room_id, idno, start_time, end_time, status) 
                        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'reserved')";
$stmt = $conn->prepare($insert_reservation);
$stmt->bind_param("ii", $room_id, $idno);

if ($stmt->execute()) {
    // Deduct one session
    $update_sessions = "UPDATE studentinfo SET `session` = `session` - 1 WHERE idno = ?";
    $stmt = $conn->prepare($update_sessions);
    $stmt->bind_param("i", $idno);
    $stmt->execute();
    
     // Deduct 1 from room capacity
     $update_capacity = "UPDATE rooms SET capacity = capacity - 1 WHERE room_id = ?";
     $stmt = $conn->prepare($update_capacity);
     $stmt->bind_param("i", $room_id);
     $stmt->execute();
     header('Location: reservation.php');
} else {
    echo "Error reserving room.";
}

$stmt->close();
$conn->close();
?>

