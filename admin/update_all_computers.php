<?php
session_start();
include "../includes/adminauth.php";
include "../includes/connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate status
    $valid_statuses = ['available', 'in_use', 'maintenance', 'offline'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    // Update all computers status
    $query = "UPDATE computers SET status = ?, last_updated = NOW()";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $status);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 