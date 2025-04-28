<?php
include '../includes/connection.php';
session_start();

if (!isset($_SESSION["idno"])) {
    die("<script>
            alert('User not logged in.');
            window.location.href='reservation.php';
         </script>");
}

$idno = $_SESSION["idno"];
$room_id = $_POST["room_id"] ?? 0;
$computer_id = isset($_POST["computer_id"]) ? $_POST["computer_id"] : null;
$purpose = isset($_POST["purpose"]) ? $_POST["purpose"] : "Self-Service Reservation";

// Check if computer_id is provided
if (!$computer_id) {
    echo "<script>
            alert('No computer selected. Please try again.');
            window.location.href='reservation.php';
          </script>";
    exit;
}

// Check if student has remaining sessions
$check_sessions = "SELECT session FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($check_sessions);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row["session"] <= 0) {
    echo "<script>
            alert('You have no more reservations allowed. Please contact an administrator.');
            window.location.href='reservation.php';
          </script>";
    exit;
}

// Check if the computer is still available
$check_computer = "SELECT c.computer_name, c.status, r.room_name 
                   FROM computers c
                   JOIN rooms r ON c.room_id = r.room_id
                   WHERE c.computer_id = ? AND c.room_id = ?";
$stmt = $conn->prepare($check_computer);
$stmt->bind_param("ii", $computer_id, $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
            alert('Computer not found in this room.');
            window.location.href='reservation.php';
          </script>";
    exit;
}

$computer = $result->fetch_assoc();
if ($computer["status"] !== "available") {
    echo "<script>
            alert('This computer is no longer available. Someone may have just reserved it.');
            window.location.href='reservation.php';
          </script>";
    exit;
}

// Start a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Insert reservation
    $insert_reservation = "INSERT INTO reservations (room_id, idno, computer_id, start_time, end_time, status) 
                          VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'reserved')";
    $stmt = $conn->prepare($insert_reservation);
    $stmt->bind_param("iii", $room_id, $idno, $computer_id);
    $stmt->execute();

    // Deduct one session
    $update_sessions = "UPDATE studentinfo SET `session` = `session` - 1 WHERE idno = ?";
    $stmt = $conn->prepare($update_sessions);
    $stmt->bind_param("i", $idno);
    $stmt->execute();
    
    // Deduct 1 from room capacity
    $update_capacity = "UPDATE rooms SET capacity = capacity - 1 WHERE room_id = ?";
    $stmt = $conn->prepare($update_capacity);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    
    // Update computer status
    $update_computer = "UPDATE computers SET status = 'in-use', last_used = NOW() WHERE computer_id = ?";
    $stmt = $conn->prepare($update_computer);
    $stmt->bind_param("i", $computer_id);
    $stmt->execute();
    
    // Get computer name for sitin record
    $computer_name = $computer["computer_name"];
    $room_name = $computer["room_name"];
    
    // Insert sit-in record with computer information
    $student_info = "SELECT fname, lname, course FROM studentinfo WHERE idno = ?";
    $stmt = $conn->prepare($student_info);
    $stmt->bind_param("i", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    $name = $student["fname"] . " " . $student["lname"];
    $course = $student["course"];
    $sitin_purpose = $purpose;
    
    $insert_sitin = "INSERT INTO sit_in_records (idno, name, course, sitin_purpose, lab, computer, time_in) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sitin);
    $stmt->bind_param("ssssss", $idno, $name, $course, $sitin_purpose, $room_id, $computer_name);
    $stmt->execute();
    
    // Commit the transaction
    $conn->commit();
    
    // Successful reservation notification
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Reservation Successful</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gray-100 min-h-screen flex items-center justify-center p-4'>
        <div class='bg-white rounded-lg shadow-lg p-8 max-w-md w-full'>
            <div class='text-center mb-6'>
                <div class='inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4'>
                    <svg class='h-8 w-8' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path>
                    </svg>
                </div>
                <h2 class='text-2xl font-bold text-gray-800 mb-2'>Reservation Successful!</h2>
                <p class='text-gray-600'>Your computer has been reserved successfully.</p>
            </div>
            
            <div class='border-t border-b border-gray-200 py-4 my-4'>
                <div class='grid grid-cols-2 gap-4'>
                    <div>
                        <p class='text-sm text-gray-500 mb-1'>Room</p>
                        <p class='font-medium'>Laboratory {$room_name}</p>
                    </div>
                    <div>
                        <p class='text-sm text-gray-500 mb-1'>Computer</p>
                        <p class='font-medium'>{$computer_name}</p>
                    </div>
                    <div>
                        <p class='text-sm text-gray-500 mb-1'>Purpose</p>
                        <p class='font-medium'>{$purpose}</p>
                    </div>
                    <div>
                        <p class='text-sm text-gray-500 mb-1'>Start Time</p>
                        <p class='font-medium'>" . date('h:i A') . "</p>
                    </div>
                    <div class='col-span-2'>
                        <p class='text-sm text-gray-500 mb-1'>End Time</p>
                        <p class='font-medium'>" . date('h:i A', strtotime('+1 hour')) . "</p>
                    </div>
                </div>
            </div>
            
            <div class='text-center mt-6'>
                <a href='reservation.php' class='inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'>
                    Return to Reservations
                </a>
            </div>
            
            <p class='text-center text-sm text-gray-500 mt-4'>
                Check your reservation history in your dashboard
            </p>
        </div>
        
        <script>
            // Automatically redirect after 5 seconds
            setTimeout(() => {
                window.location.href = 'reservation.php';
            }, 5000);
        </script>
    </body>
    </html>";
    exit;
    
} catch (Exception $e) {
    // Rollback the transaction if any step fails
    $conn->rollback();
    echo "<script>
            alert('Error reserving computer: " . $e->getMessage() . "');
            window.location.href='reservation.php';
          </script>";
}

$stmt->close();
$conn->close();
?>

