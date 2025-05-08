<?php
include "../includes/connection.php";

if (!isset($_GET['id'])) {
    header("Location: currentsitin.php");
    exit;
}

$sitin_id = $_GET['id'];

// Fetch the student ID associated with this sit-in record
$query = "SELECT idno FROM sit_in_records WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sitin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: currentsitin.php?error=invalid_sitin");
    exit;
}
$row = $result->fetch_assoc();
$student_id = $row['idno'];

// Fetch current points and session for the student
$query = "SELECT points, session FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$current_points = 0;
$current_session = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_points = $row['points'];
    $current_session = $row['session'];
}

// Start transaction
$conn->begin_transaction();

try {
    // Always increment points by 1
    $query = "UPDATE studentinfo SET points = points + 1 WHERE idno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    if (!$stmt->execute()) {
        throw new Exception("Error updating points: " . $stmt->error);
    }
    
    // After incrementing, check if we have exactly 3 points and session is below 30
    if ($current_points >= 2 && $current_session < 30) { // current_points is 2 because we just added 1
        // Convert 3 points to 1 session
        $query = "UPDATE studentinfo SET points = 0, session = session + 1 WHERE idno = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating session: " . $stmt->error);
        }
        error_log("Student $student_id: Converted 3 points to 1 session. Previous points: $current_points, Previous session: $current_session");
    } else {
        error_log("Student $student_id: Incremented points by 1. Current points: " . ($current_points + 1) . ", Current session: $current_session");
    }
    
    // Commit the transaction
    $conn->commit();
    
    header("Location: currentsitin.php?success=rewarded");
    exit;
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    error_log("Error in reward_student.php: " . $e->getMessage());
    header("Location: currentsitin.php?error=reward_failed");
    exit;
}
?> 