<?php
include "../includes/connection.php";  // Ensure this file connects to your database.

$resetQuery = "UPDATE studentinfo SET session = 30"; // Reset session for all students
$stmt = $conn->prepare($resetQuery); // Prepare the SQL statement

if ($stmt->execute()) { // Execute the query
    echo json_encode(['success' => true]); // Return success response
} else {
    echo json_encode(['success' => false]); // Return failure response
}

$stmt->close(); // Close the statement
$conn->close(); // Close the connection
?>
