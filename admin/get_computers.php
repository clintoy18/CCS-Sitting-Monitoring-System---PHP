<?php
header('Content-Type: application/json');
include "../includes/connection.php";

// Get room_id from query string
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($room_id <= 0) {
    echo json_encode(['error' => 'Invalid room ID']);
    exit;
}

// Modified query to get ALL computers for the specified room, not just available ones
$stmt = $conn->prepare("SELECT 
    computer_id, 
    computer_name, 
    status 
FROM computers 
WHERE room_id = ?
ORDER BY computer_name");

// Check if prepare statement was successful
if (!$stmt) {
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $room_id);
$result = $stmt->execute();

// Check if execution was successful
if (!$result) {
    echo json_encode(['error' => 'Failed to execute query: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$computers = [];
while ($row = $result->fetch_assoc()) {
    $computers[] = [
        'computer_id' => $row['computer_id'],
        'computer_name' => $row['computer_name'],
        'status' => $row['status']
    ];
}

echo json_encode($computers);

$stmt->close();
$conn->close();
?> 