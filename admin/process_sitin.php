<?php
include "../includes/connection.php";  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idnos = $_POST['idno'];
    $sitin_purposes = $_POST['sitin_purpose'];
    $labs = $_POST['lab']; // Rename to $labs to avoid confusion

    foreach ($idnos as $index => $idno) {
        $sitin_purpose = $sitin_purposes[$index];
        $lab_value = $labs[$index]; // Get lab for current student

        // Check if student is already sit-in and has not logged out
        $checkStmt = $conn->prepare("SELECT * FROM sit_in_records WHERE idno = ? AND time_out IS NULL");
        $checkStmt->bind_param("s", $idno);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Student with ID $idno is still currently sitting in and has not logged out yet.'); window.location.href='admindashboard.php';</script>";
            continue; 
        }

        // Fetch student details
        $stmt = $conn->prepare("SELECT fname, lname, course FROM studentinfo WHERE idno = ?");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $name = $row['fname'] . " " . $row['lname'];
            $course = $row['course'];

            // Insert sit-in record
            $insertStmt = $conn->prepare("INSERT INTO sit_in_records (idno, name, course, sitin_purpose, lab, time_in) VALUES (?, ?, ?, ?, ?, NOW())");
            $insertStmt->bind_param("sssss", $idno, $name, $course, $sitin_purpose, $lab_value);
            $insertStmt->execute();
        }
    }

    echo "<script>alert('Sit-in record(s) added successfully!'); window.location.href='admindashboard.php';</script>";
}
?>
