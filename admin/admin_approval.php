<?php
include '../includes/connection.php';

$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Fetch reservation details to be inserted into sit_in_records
    $reservation_query = "
        SELECT r.idno, r.room_id, r.computer_id, r.sitin_purpose, s.fname, s.lname, s.course, 
               c.computer_name, rm.room_name
        FROM reservations r
        JOIN studentinfo s ON r.idno = s.idno
        JOIN computers c ON r.computer_id = c.computer_id
        JOIN rooms rm ON r.room_id = rm.room_id
        WHERE r.reservation_id = ?";
    
    $stmt = $conn->prepare($reservation_query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    if (!$reservation) {
        throw new Exception("Reservation not found.");
    }

    // Approve the reservation by updating its status to 'approved'
    $update_reservation = "UPDATE reservations SET status = 'approved' WHERE reservation_id = ?";
    $stmt = $conn->prepare($update_reservation);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();

    // Insert into sit_in_records with the actual purpose
    $insert_query = "INSERT INTO sit_in_records (idno, name, course, sitin_purpose, lab, computer, time_in) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($insert_query);
    $idno = $reservation['idno'];
    $name = $reservation['fname'] . ' ' . $reservation['lname'];
    $course = $reservation['course'];
    $purpose = $reservation['sitin_purpose'];  // Get purpose directly from reservation
    $lab = $reservation['room_name'];
    $computer = $reservation['computer_name'];
    $stmt->bind_param("ssssss", $idno, $name, $course, $purpose, $lab, $computer);
    $stmt->execute();

    // Get the computer ID from the reservation
    $computer_id = $reservation['computer_id'];

    // Update computer status to 'in-use' and update last_used timestamp
    $update_computer = "UPDATE computers SET status = 'in-use', last_used = NOW() WHERE computer_id = ?";
    $stmt = $conn->prepare($update_computer);
    $stmt->bind_param("i", $computer_id);
    $stmt->execute();

    // Check if reservation and sit-in record were updated/inserted successfully
    if ($stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to approve reservation and log sit-in record.");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
