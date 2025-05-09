<?php
include "../includes/connection.php";  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idnos = $_POST['idno'];
    $sitin_purposes = $_POST['  '];
    $labs = $_POST['lab']; // Rename to $labs to avoid confusion
    $computers = isset($_POST['computer']) ? $_POST['computer'] : array_fill(0, count($idnos), null);

    foreach ($idnos as $index => $idno) {
        $sitin_purpose = $sitin_purposes[$index];
        $lab_value = $labs[$index]; // Get lab for current student
        $computer_value = isset($computers[$index]) ? $computers[$index] : null;

        // Check if student is already sit-in and has not logged out
        $checkStmt = $conn->prepare("SELECT * FROM sit_in_records WHERE idno = ? AND time_out IS NULL");
        $checkStmt->bind_param("s", $idno);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Student with ID $idno is still currently sitting in and has not logged out yet.'); window.location.href='admindashboard.php';</script>";
            continue; 
        }

        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Fetch student details
            $stmt = $conn->prepare("SELECT fname, lname, course FROM studentinfo WHERE idno = ?");
            $stmt->bind_param("s", $idno);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $name = $row['fname'] . " " . $row['lname'];
                $course = $row['course'];

                // Check if a computer was selected and update its status
                $computer_id = null;
                if (!empty($computer_value)) {
                    // Get computer ID and check availability
                    $compStmt = $conn->prepare("SELECT computer_id, status FROM computers WHERE computer_name = ? AND room_id = ?");
                    $compStmt->bind_param("si", $computer_value, $lab_value);
                    $compStmt->execute();
                    $compResult = $compStmt->get_result();
                    
                    if ($compRow = $compResult->fetch_assoc()) {
                        if ($compRow['status'] != 'available') {
                            throw new Exception("Computer $computer_value is not available.");
                        }
                        
                        $computer_id = $compRow['computer_id'];
                        
                        // Update computer status
                        $updateCompStmt = $conn->prepare("UPDATE computers SET status = 'in-use', last_used = NOW() WHERE computer_id = ?");
                        $updateCompStmt->bind_param("i", $computer_id);
                        $updateCompStmt->execute();
                    }
                }

                // Insert sit-in record
                $insertStmt = $conn->prepare("INSERT INTO sit_in_records (idno, name, course, sitin_purpose, lab, computer, time_in) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $insertStmt->bind_param("ssssss", $idno, $name, $course, $sitin_purpose, $lab_value, $computer_value);
                $insertStmt->execute();
                
                // Update room capacity
                $updateRoomStmt = $conn->prepare("UPDATE rooms SET capacity = capacity - 1 WHERE room_id = ?");
                $updateRoomStmt->bind_param("i", $lab_value);
                $updateRoomStmt->execute();
                
                // Insert reservation record if computer is used
                if ($computer_id) {
                    $insertReservation = $conn->prepare("INSERT INTO reservations (room_id, computer_id, idno, start_time, end_time, status) 
                                                        VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'reserved')");
                    $insertReservation->bind_param("iis", $lab_value, $computer_id, $idno);
                    $insertReservation->execute();
                }
            }
            
            // Commit the transaction
            $conn->commit();
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            echo "<script>alert('Error processing sit-in for student ID $idno: " . $e->getMessage() . "'); window.location.href='admindashboard.php';</script>";
            continue;
        }
    }

    echo "<script>alert('Sit-in record(s) added successfully!'); window.location.href='admindashboard.php';</script>";
}
?>
