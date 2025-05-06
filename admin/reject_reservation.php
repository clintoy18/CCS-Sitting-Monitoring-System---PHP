<?php
include '../includes/connection.php';

header('Content-Type: application/json');

$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Check if the reservation exists
    $check_query = "SELECT * FROM reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Reservation not found.");
    }
    // Update reservation status to 'reject'
    $update_query = "UPDATE reservations SET status = 'disapproved' WHERE reservation_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Reservation disapproved successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
