<?php
include "../includes/connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Start a transaction
    $conn->begin_transaction();
    
    try {
        // Get computer and room info before updating
        $get_info = "SELECT r.idno, r.computer, r.lab, c.computer_id 
                     FROM sit_in_records r
                     LEFT JOIN computers c ON c.computer_name = r.computer AND c.room_id = r.lab
                     WHERE r.id = ?";
        $stmt = $conn->prepare($get_info);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        // Update sit-in record with logout time
        $update_sitin = "UPDATE sit_in_records SET time_out = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_sitin);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Increase room capacity
        if (!empty($row['lab'])) {
            $update_room = "UPDATE rooms SET capacity = capacity + 1 WHERE room_id = ?";
            $stmt = $conn->prepare($update_room);
            $stmt->bind_param("i", $row['lab']);
            $stmt->execute();
        }
        
        // Update computer status if a computer was used
        if (!empty($row['computer_id'])) {
            $update_computer = "UPDATE computers SET status = 'available' WHERE computer_id = ?";
            $stmt = $conn->prepare($update_computer);
            $stmt->bind_param("i", $row['computer_id']);
            $stmt->execute();
        }
        
        // Update student's reservation status if applicable
        if (!empty($row['idno'])) {
            $update_reservation = "UPDATE reservations SET status = 'completed' 
                                  WHERE idno = ? AND room_id = ? AND status = 'reserved'";
            $stmt = $conn->prepare($update_reservation);
            $stmt->bind_param("ii", $row['idno'], $row['lab']);
            $stmt->execute();
        }
        
        // Commit all changes
        $conn->commit();
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: admindashboard.php');
}

$conn->close();
?>
