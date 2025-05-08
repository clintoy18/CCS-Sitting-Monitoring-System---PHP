<?php
session_start();
include "../includes/adminauth.php";
include "../includes/connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $computer_id = mysqli_real_escape_string($conn, $_POST['computer_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate status - match exact enum values from database
    $valid_statuses = ['available', 'in-use', 'maintenance'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    // Update computer status
    $query = "UPDATE computers SET status = ?, last_used = NOW() WHERE computer_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $computer_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 