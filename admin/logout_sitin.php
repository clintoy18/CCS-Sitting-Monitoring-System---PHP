<?php
include "../includes/connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Get the required information before updating
        $get_info = "SELECT r.idno, r.computer, r.lab
                     FROM sit_in_records r
                     WHERE r.id = ?";
        $stmt = $conn->prepare($get_info);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            throw new Exception("Sit-in record not found.");
        }

        // Update sit-in record with logout time
        $update_sitin = "UPDATE sit_in_records SET time_out = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_sitin);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Decrease one session for the student
        $update_sessions = "UPDATE studentinfo SET `session` = `session` - 1 WHERE idno = ?";
        $stmt = $conn->prepare($update_sessions);
        $stmt->bind_param("i", $row['idno']);
        $stmt->execute();

        // Increase room capacity
        if (!empty($row['lab'])) {
            $update_capacity = "UPDATE rooms SET capacity = capacity + 1 WHERE room_id = ?";
            $stmt = $conn->prepare($update_capacity);
            $stmt->bind_param("i", $row['lab']);
            $stmt->execute();
        }

        // Update computer status to 'available' and update last_used
        $computer_name = $row['computer'];
        $update_computer = "UPDATE computers SET status = 'available', last_used = NOW() WHERE computer_name = ?";
        $stmt = $conn->prepare($update_computer);
        $stmt->bind_param("s", $computer_name);
        $stmt->execute();

        // Get the latest reservation_id for this student
        $get_latest_reservation = "SELECT reservation_id FROM reservations WHERE idno = ? ORDER BY reservation_id DESC LIMIT 1";
        $stmt = $conn->prepare($get_latest_reservation);
        $stmt->bind_param("i", $row['idno']);
        $stmt->execute();
        $stmt->bind_result($latest_reservation_id);
        $stmt->fetch();
        $stmt->close();

        // Update latest reservation status to 'completed' if found
        if (!empty($latest_reservation_id)) {
            $update_reservation = "UPDATE reservations SET status = 'completed' WHERE reservation_id = ?";
            $stmt = $conn->prepare($update_reservation);
            $stmt->bind_param("i", $latest_reservation_id);
            $stmt->execute();
        }

        // Commit all changes
        $conn->commit();

        // Redirect back to the previous page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect to dashboard if no ID is provided
    header('Location: admindashboard.php');
}

$conn->close();
?>
