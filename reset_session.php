<?php
include "connection.php"; // Ensure this file connects to your database.

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $resetQuery = "UPDATE studentinfo SET session = 30 WHERE idno = ?";
    $stmt = $conn->prepare($resetQuery); // Prepare the SQL statement.
    $stmt->bind_param("s", $id); // Bind the student ID parameter.

    if ($stmt->execute()) { // Execute the query.
        echo json_encode(['success' => true]); // Return success response.
    } else {
        echo json_encode(['success' => false]); // Return failure response.
    }

    $stmt->close(); // Close the statement.
    $conn->close(); // Close the connection.
} else {
    echo json_encode(['success' => false]); // Handle missing ID parameter.
}
?>
