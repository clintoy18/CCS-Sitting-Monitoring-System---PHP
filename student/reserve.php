<?php
include '../includes/connection.php';
session_start();

if (!isset($_SESSION["idno"])) {
    die("User not logged in.");
}

$idno = $_SESSION["idno"];
$room_id = $_POST["room_id"];
$computer_id = isset($_POST["computer_id"]) ? $_POST["computer_id"] : null;

// Check if computer_id is provided
if (!$computer_id) {
    echo "<script>alert('No computer selected.'); window.location.href='reservation.php';</script>";
    exit;
}

// Check if student has remaining sessions
$check_sessions = "SELECT session FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($check_sessions);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row["session"] <= 0) {
    echo "<script>alert('No more reservations allowed.'); window.location.href='reservation.php';</script>";
    exit;
}

// Check if the computer is still available
$check_computer = "SELECT status FROM computers WHERE computer_id = ? AND room_id = ?";
$stmt = $conn->prepare($check_computer);
$stmt->bind_param("ii", $computer_id, $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Computer not found in this room.'); window.location.href='reservation.php';</script>";
    exit;
}

$computer = $result->fetch_assoc();
if ($computer["status"] !== "available") {
    echo "<script>alert('This computer is no longer available.'); window.location.href='reservation.php';</script>";
    exit;
}

// Start a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Insert reservation
    $insert_reservation = "INSERT INTO reservations (room_id, idno, computer_id, start_time, end_time, status) 
                          VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'reserved')";
    $stmt = $conn->prepare($insert_reservation);
    $stmt->bind_param("iii", $room_id, $idno, $computer_id);
    $stmt->execute();

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
    
    // Update computer status
    $update_computer = "UPDATE computers SET status = 'in-use', last_used = NOW() WHERE computer_id = ?";
    $stmt = $conn->prepare($update_computer);
    $stmt->bind_param("i", $computer_id);
    $stmt->execute();
    
    // Get computer name for sitin record
    $get_computer_name = "SELECT computer_name FROM computers WHERE computer_id = ?";
    $stmt = $conn->prepare($get_computer_name);
    $stmt->bind_param("i", $computer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $computer_info = $result->fetch_assoc();
    $computer_name = $computer_info["computer_name"];
    
    // Insert sit-in record with computer information
    $student_info = "SELECT fname, lname, course FROM studentinfo WHERE idno = ?";
    $stmt = $conn->prepare($student_info);
    $stmt->bind_param("i", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    $name = $student["fname"] . " " . $student["lname"];
    $course = $student["course"];
    $sitin_purpose = "Self-Service Reservation";
    
    $insert_sitin = "INSERT INTO sit_in_records (idno, name, course, sitin_purpose, lab, computer, time_in) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sitin);
    $stmt->bind_param("ssssss", $idno, $name, $course, $sitin_purpose, $room_id, $computer_name);
    $stmt->execute();
    
    // Commit the transaction
    $conn->commit();
    
    echo "<script>alert('Reservation successful! You have reserved " . $computer_name . " in Room " . $room_id . "'); window.location.href='reservation.php';</script>";
} catch (Exception $e) {
    // Rollback the transaction if any step fails
    $conn->rollback();
    echo "<script>alert('Error reserving computer: " . $e->getMessage() . "'); window.location.href='reservation.php';</script>";
}

$stmt->close();
$conn->close();
?>

