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

        // Undo actions when sit-in is logged out

        // Increase one session for the student (opposite of deducting)
        $update_sessions = "UPDATE studentinfo SET `session` = `session` - 1 WHERE idno = ?";
        $stmt = $conn->prepare($update_sessions);
        $stmt->bind_param("i", $row['idno']);
        $stmt->execute();

        // Increase room capacity (opposite of deducting)
        if (!empty($row['lab'])) {
            $update_capacity = "UPDATE rooms SET capacity = capacity + 1 WHERE room_id = ?";
            $stmt = $conn->prepare($update_capacity);
            $stmt->bind_param("i", $row['lab']);
            $stmt->execute();
        }

        // Get the computer name from the sit-in record
        $computer_name = $row['computer'];

        // Update computer status to 'available' and update last_used timestamp
        $update_computer = "UPDATE computers SET status = 'available', last_used = NOW() WHERE computer_name = ?";
        $stmt = $conn->prepare($update_computer);
        $stmt->bind_param("s", $computer_name);
        $stmt->execute();

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
