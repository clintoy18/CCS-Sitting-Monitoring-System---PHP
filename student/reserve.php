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

// STRICT CHECK: Block if student has any pending or approved reservations
$today = date('Y-m-d');
$check_existing = "SELECT reservation_id, status 
                   FROM reservations 
                   WHERE idno = ? 
                   AND (status = 'pending' OR status = 'approved')";
$stmt = $conn->prepare($check_existing);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $reservation = $result->fetch_assoc();
    $status = ucfirst($reservation['status']);
    echo "<script>
            alert('You already have a {$status} reservation. You cannot make another reservation until this one is completed or rejected.');
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
                          VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'pending')";
    $stmt = $conn->prepare($insert_reservation);
    $stmt->bind_param("iii", $room_id, $idno, $computer_id);
    $stmt->execute();

    // Get computer name for sitin record
    $computer_name = $computer["computer_name"];
    $room_name = $computer["room_name"];


    //FETCH LAST STATUS OF RESERVATION
    $reservation_id = $conn->insert_id;

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
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    </head>
    <body class='bg-gray-50 min-h-screen flex items-center justify-center p-4'>
        <div class='bg-white rounded-xl shadow-xl p-8 max-w-md w-full border border-gray-100'>
            <div class='text-center mb-6'>
                <div class='inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-4 animate-bounce'>
                    <svg class='h-10 w-10' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path>
                    </svg>
                </div>
                <h2 class='text-2xl font-bold text-gray-800 mb-2'>Reservation Successful!</h2>
                <p class='text-gray-600'>Your computer has been reserved successfully.</p>
            </div>
            
            <div class='bg-blue-50 rounded-lg border border-blue-100 py-6 px-4 my-6'>
                <div class='grid grid-cols-2 gap-4'>
                    <div class='border-r border-blue-200 pr-4'>
                        <p class='text-sm text-blue-500 font-medium mb-1'>Room</p>
                        <p class='font-medium text-gray-700 flex items-center'>
                            <i class='fas fa-door-open text-blue-400 mr-2'></i>
                            Laboratory {$room_name}
                        </p>
                    </div>
                    <div>
                        <p class='text-sm text-blue-500 font-medium mb-1'>Computer</p>
                        <p class='font-medium text-gray-700 flex items-center'>
                            <i class='fas fa-desktop text-blue-400 mr-2'></i>
                            {$computer_name}
                        </p>
                    </div>
                    <div class='col-span-2 border-t border-blue-200 pt-3 mt-2'>
                        <p class='text-sm text-blue-500 font-medium mb-1'>Purpose</p>
                        <p class='font-medium text-gray-700 flex items-center'>
                            <i class='fas fa-tasks text-blue-400 mr-2'></i>
                            {$purpose}
                        </p>
                    </div>
                    <div class='flex flex-col'>
                        <p class='text-sm text-blue-500 font-medium mb-1'>Start Time</p>
                        <p class='font-medium text-gray-700 flex items-center'>
                            <i class='far fa-clock text-blue-400 mr-2'></i>
                            " . date('h:i A') . "
                        </p>
                    </div>
                    <div class='flex flex-col'>
                        <p class='text-sm text-blue-500 font-medium mb-1'>End Time</p>
                        <p class='font-medium text-gray-700 flex items-center'>
                            <i class='far fa-clock text-blue-400 mr-2'></i>
                            " . date('h:i A', strtotime('+1 hour')) . "
                        </p>
                    </div>
                </div>
            </div>
            
            <div class='flex flex-col items-center mt-6 space-y-3'>
                <a href='reservation.php' class='w-full inline-flex justify-center items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md'>
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 mr-2' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 19l-7-7m0 0l7-7m-7 7h18' />
                    </svg>
                    Return to Reservations
                </a>
                
                <a href='dashboard.php' class='w-full inline-flex justify-center items-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm'>
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 mr-2' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' />
                    </svg>
                    Go to Dashboard
                </a>
            </div>
            
            <div class='flex justify-between items-center mt-6 pt-4 border-t border-gray-100'>
                <div class='text-sm text-gray-500'>
                    <span class='font-medium'>Note:</span> Your session expires in 1 hour
                </div>
                <div class='flex items-center'>
                    <div class='w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse'></div>
                    <span class='text-xs text-green-600 font-medium'>Active</span>
                </div>
            </div>

            <div id='countdown' class='w-full bg-gray-200 rounded-full h-2.5 mt-3'>
                <div id='progress-bar' class='bg-blue-600 h-2.5 rounded-full' style='width: 100%'></div>
            </div>
        </div>
        
        <script>
            // Countdown timer animation
            const totalSeconds = 5;
            let secondsLeft = totalSeconds;
            const progressBar = document.getElementById('progress-bar');
            
            const interval = setInterval(() => {
                secondsLeft--;
                const percentage = (secondsLeft / totalSeconds) * 100;
                progressBar.style.width = percentage + '%';
                
                if (secondsLeft <= 0) {
                    clearInterval(interval);
                    window.location.href = 'reservation.php';
                }
            }, 1000);
            
            // Automatically redirect after 5 seconds
            setTimeout(() => {
                window.location.href = 'reservation.php';
            }, totalSeconds * 1000);
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

