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

// Fetch current points for the student
$query = "SELECT points FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$current_points = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_points = $row['points'];
}

// Start transaction
$conn->begin_transaction();

try {
    // Check if student has 3 points
    if ($current_points == 2) {
        // Convert 3 points to +1 session
        $query = "UPDATE studentinfo SET points = 0, session = session + 1 WHERE idno = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating session: " . $stmt->error);
        }
        
        // Log the conversion
        error_log("Student $student_id: Converted 3 points to +1 session. Previous points: $current_points");
    } else {
        // Increment points by 1
        $query = "UPDATE studentinfo SET points = points + 1 WHERE idno = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating points: " . $stmt->error);
        }
        
        // Log the point increment
        error_log("Student $student_id: Incremented points by 1. Previous points: $current_points");
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