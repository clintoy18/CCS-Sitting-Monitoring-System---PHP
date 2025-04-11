<?php
include "../includes/connection.php"; // Ensure the connection file is included.

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $deleteQuery = "DELETE FROM studentinfo WHERE idno = ?";
    $stmt = $conn->prepare($deleteQuery); // Prepare the SQL query.
    $stmt->bind_param("s", $id); // Bind the student ID to the query.

    if ($stmt->execute()) { // Execute the query.
        echo json_encode(['success' => true]); // Respond with success.
    } else {
        echo json_encode(['success' => false]); // Respond with failure.
    }

    $stmt->close(); // Close the statement.
    $conn->close(); // Close the database connection.
} else {
    echo json_encode(['success' => false]); // Handle missing ID parameter.
}
?>
