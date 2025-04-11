<?php
include "connection.php";

if (isset($_POST['sitin_id']) && isset($_POST['student_id'])) {
    $sitin_id = $_POST['sitin_id'];
    $student_id = $_POST['student_id'];

    // Set time_out for the sit-in record
    $update_query = "UPDATE sit_in_records SET time_out = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $sitin_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Decrease session count in studentinfo
        $update_student = "UPDATE studentinfo SET session = session - 1 WHERE idno = ?";
        $stmt2 = $conn->prepare($update_student);
        $stmt2->bind_param("s", $student_id);
        $stmt2->execute();

        echo "Student logged out successfully.";
    } else {
        echo "Failed to log out the student.";
    }

    $stmt->close();
    $stmt2->close();
    $conn->close();
}
?>
