<?php
// Start session and include necessary files
session_start();
include "../includes/layout.php";
include "../includes/auth.php";

// Fetch selected status from the dropdown, or default to 'approved'
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'approved';

// Fetch all reservations filtered by status
$query = "SELECT r.reservation_id, r.idno, r.room_id, r.computer_id, r.start_time, r.end_time, r.status, r.sitin_purpose,
                 s.fname, s.lname, s.course, c.computer_name, rm.room_name
          FROM reservations r
          JOIN studentinfo s ON r.idno = s.idno
          JOIN computers c ON r.computer_id = c.computer_id
          JOIN rooms rm ON r.room_id = rm.room_id
          WHERE r.idno = ? AND r.status = ?";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("is", $userID, $selectedStatus);
    $stmt->execute();
    $reservationResult = $stmt->get_result();
    $stmt->close();
} else {
    die("Error preparing statement: " . $conn->error);
}
?>

<div class="px-8 py-6">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8 flex items-center justify-center">
        <i class="fas fa-desktop text-blue-600 mr-3"></i>Laboratory Room Reservations
    </h1>

    <?php
    // Check if student has any pending or approved reservations
    include '../includes/connection.php';
    $hasReservationToday = false;
    $reservationMessage = "";
    
    if (isset($_SESSION["idno"])) {
        $userID = $_SESSION["idno"];
        $today = date('Y-m-d');
        
        // STRICT CHECK: Block if student has any pending or approved reservations
        $check_existing = "SELECT r.reservation_id, r.room_id, r.computer_id, r.status, c.computer_name, ro.room_name 
                          FROM reservations r 
                          JOIN computers c ON r.computer_id = c.computer_id 
                          JOIN rooms ro ON r.room_id = ro.room_id
                          WHERE r.idno = ? 
                          AND (r.status = 'pending' OR r.status = 'approved')";
        $stmt = $conn->prepare($check_existing);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $hasReservationToday = true;
            $reservation = $result->fetch_assoc();
            $status = ucfirst($reservation['status']);
            $reservationMessage = "You have a <strong>{$status}</strong> reservation for <strong>PC {$reservation['computer_name']}</strong> in <strong>Laboratory {$reservation['room_name']}</strong>. You cannot make another reservation until this one is completed or rejected.";
        }
        $stmt->close();
    }
    
    // Display alert if student has a reservation today
    if ($hasReservationToday) {
        echo "<div class='bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm'>
                <div class='flex items-center'>
                    <div class='flex-shrink-0'>
                        <i class='fas fa-exclamation-triangle text-yellow-400 text-xl'></i>
                    </div>
                    <div class='ml-3'>
                        <p class='text-sm text-yellow-700'>
                            {$reservationMessage} <span class='font-medium'>Only one reservation at a time is allowed.</span>
                        </p>
                    </div>
                </div>
            </div>";
    }
    ?>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 transition-all duration-300 hover:shadow-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-3 sm:mb-0 flex items-center">
                <i class="fas fa-door-open text-blue-600 mr-2"></i>Available Rooms
            </h2>
            <div class="bg-gray-50 rounded-full px-4 py-2 flex items-center shadow-sm border border-gray-200">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                <span class="font-medium text-gray-600 mr-2">Sessions Remaining:</span> 
                <?php
                    if (!isset($_SESSION["idno"])) {
                        echo "<span class='text-red-600 font-bold'>Please log in to reserve a room</span>";
                    } else {
                        $userID = $_SESSION["idno"];
                        $check_sessions = "SELECT `session` FROM studentinfo WHERE idno = ?";
                        $stmt = $conn->prepare($check_sessions);
                        $stmt->bind_param("i", $userID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $student = $result->fetch_assoc();
                        $remaining_sessions = $student["session"] ?? 0;
                        $session_color = $remaining_sessions > 0 ? "text-green-600" : "text-red-600";
                        echo "<span class='$session_color font-bold text-lg px-2 py-1 rounded-full bg-white shadow-inner border border-gray-200'>" . $remaining_sessions . "</span>";
                    }
                ?>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-building text-blue-600 mr-2"></i>Room 
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-desktop text-blue-600 mr-2"></i>Available Computers
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-cog text-blue-600 mr-2"></i>Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php
                    if (!isset($_SESSION["idno"])) {
                        echo "<tr><td colspan='4' class='p-4 text-center text-red-600 font-medium'>
                                <i class='fas fa-exclamation-circle mr-2'></i>Please log in to reserve a room.
                            </td></tr>";
                    } else {
                        $userID = $_SESSION["idno"];
                        $remaining_sessions = $student["session"] ?? 0;

                        $sql = "SELECT * FROM rooms";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                $room_id = $row["room_id"];

                                // Check if user already reserved this room
                                $check_reservation = "SELECT * FROM reservations WHERE idno = ? AND room_id = ? AND status = 'approved'";
                                $stmt = $conn->prepare($check_reservation);
                                if ($stmt) {
                                    $stmt->bind_param("ii", $userID, $room_id);
                                    $stmt->execute();
                                    $reservation_result = $stmt->get_result();
                                    $isReserved = $reservation_result->num_rows > 0;
                                    $stmt->close();
                                }

                                // Get available computers count
                                $check_computers = "SELECT COUNT(*) as available_count FROM computers WHERE room_id = ? AND status = 'available'";
                                $comp_stmt = $conn->prepare($check_computers);
                                if ($comp_stmt) {
                                    $comp_stmt->bind_param("i", $room_id);
                                    $comp_stmt->execute();
                                    $comp_result = $comp_stmt->get_result();
                                    $comp_row = $comp_result->fetch_assoc();
                                    $available_computers = $comp_row['available_count'];
                                    $comp_stmt->close();
                                }

                                $status_class = $row["status"] === 'available' ? 'text-green-600' : 'text-red-600';
                                $hover_class = $row["status"] === 'available' ? 'hover:bg-green-50' : 'hover:bg-red-50';
                                
                                echo "<tr class='$hover_class transition-colors duration-150'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <i class='fas fa-door-open text-blue-600 mr-2'></i>Laboratory {$row["room_name"]}
                                    </td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='font-medium bg-blue-50 text-blue-700 rounded-full px-3 py-1 border border-blue-200'>
                                            <i class='fas fa-desktop mr-1'></i>{$available_computers} computers
                                        </span>
                                    </td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='font-medium {$status_class} capitalize bg-" . ($row["status"] === 'available' ? 'green' : 'red') . "-50 px-3 py-1 rounded-full border border-" . ($row["status"] === 'available' ? 'green' : 'red') . "-200'>
                                            <i class='fas fa-" . ($row["status"] === 'available' ? 'check' : 'times') . " mr-1'></i>{$row["status"]}
                                        </span>
                                    </td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-center'>";
                                
                                if ($hasReservationToday) {
                                    echo "<span class='inline-flex items-center px-4 py-2 text-xs font-semibold rounded-md bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm'>
                                            <i class='fas fa-exclamation-circle mr-1'></i>One Reservation a time
                                        </span>";
                                } else if ($remaining_sessions <= 0) {
                                    echo "<span class='inline-flex items-center px-4 py-2 text-xs font-semibold rounded-md bg-red-100 text-red-800 border border-red-200 shadow-sm'>
                                            <i class='fas fa-times-circle mr-1'></i>No More Reservations
                                        </span>";
                                } elseif ($row["capacity"] <= 0 || $available_computers <= 0) {
                                    echo "<span class='inline-flex items-center px-4 py-2 text-xs font-semibold rounded-md bg-red-100 text-red-800 border border-red-200 shadow-sm'>
                                            <i class='fas fa-door-closed mr-1'></i>Room is Full
                                        </span>";
                                } elseif ($isReserved) {
                                    echo "<span class='inline-flex items-center px-4 py-2 text-xs font-semibold rounded-md bg-gray-100 text-gray-800 border border-gray-200 shadow-sm'>
                                            <i class='fas fa-check-circle mr-1'></i>Already Reserved
                                        </span>";
                                } else {
                                    echo "<button type='button' onclick='showComputers({$room_id})' 
                                            class='inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm border border-blue-500'>
                                            <i class='fas fa-desktop mr-2'></i>Select Computer
                                        </button>";
                                }
                                
                                echo "</td>";
                                echo "</tr>";
                        }
                        } else {
                            echo "<tr><td colspan='4' class='p-4 text-center text-gray-500'>
                                    <i class='fas fa-info-circle mr-2'></i>No laboratory rooms available at this time.
                                </td></tr>";
                        }
                        mysqli_close($conn);
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reservations Logs Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-12 mt-8 transition-all duration-300 hover:shadow-xl">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-history text-blue-600 mr-2"></i>Reservations Logs
        </h2>

        <!-- Dropdown for selecting status -->
        <form action="" method="GET" class="mb-4">
            <label for="status" class="text-gray-600 font-medium flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i>Filter by Status:
            </label>
            <div class="flex items-center mt-2">
                <select name="status" id="status" class="border border-gray-200 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition-colors duration-200">
                    <option value="approved" <?php echo $selectedStatus == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $selectedStatus == 'disapproved' ? 'selected' : ''; ?>>Rejected</option>
                    <option value="pending" <?php echo $selectedStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="completed" <?php echo $selectedStatus == 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-hashtag text-blue-600 mr-2"></i>Reservation ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-user text-blue-600 mr-2"></i>Student Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>Course
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-desktop text-blue-600 mr-2"></i>Computer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-building text-blue-600 mr-2"></i>Room
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-sign-in-alt text-blue-600 mr-2"></i>Start Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-sign-out-alt text-blue-600 mr-2"></i>End Time
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    <?php if ($reservationResult->num_rows > 0): ?>
                        <?php while ($row = $reservationResult->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['reservation_id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['course']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['computer_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['room_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        <?php
                                        switch($row['status']) {
                                            case 'approved':
                                                echo 'bg-green-100 text-green-800 border border-green-200';
                                                break;
                                            case 'rejected':
                                                echo 'bg-red-100 text-red-800 border border-red-200';
                                                break;
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                                break;
                                            case 'completed':
                                                echo 'bg-blue-100 text-blue-800 border border-blue-200';
                                                break;
                                        }
                                        ?>">
                                        <i class="fas fa-<?php
                                            switch($row['status']) {
                                                case 'approved':
                                                    echo 'check';
                                                    break;
                                                case 'rejected':
                                                    echo 'times';
                                                    break;
                                                case 'pending':
                                                    echo 'clock';
                                                    break;
                                                case 'completed':
                                                    echo 'check-double';
                                                    break;
                                            }
                                        ?> mr-1"></i>
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['start_time']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?= htmlspecialchars($row['end_time']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>No reservations found for selected status
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Improved Computer Selection Modal -->
<div id="computerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-2xl p-6 transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-y-auto" id="modalContent">
        <div class="flex justify-between items-center mb-6 border-b pb-4 sticky top-0 bg-white z-10">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                Select a Computer
            </h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-6">
            <?php if ($hasReservationToday): ?>
            <div class="bg-yellow-50 rounded-lg p-4 mb-4 border-l-4 border-yellow-500">
                <p class="text-yellow-800 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    You have already reserved a computer. You can only make one reservation at a time.
                </p>
            </div>
            <?php endif; ?>
            <div class="bg-blue-50 rounded-lg p-4 mb-4 border-l-4 border-blue-500">
                <p id="roomInfoText" class="text-blue-800 font-medium">Loading room information...</p>
            </div>
            
            <!-- Purpose Selection - Improved UI -->
            <div class="mb-4">
                <label for="purpose-select" class="block text-sm font-medium text-gray-700 mb-2">Select Purpose:</label>
                <div class="relative">
                    <select id="purpose-select" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm appearance-none">
                        <option value="C Programming">C Programming</option>
                        <option value="C# Programming">C# Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="Php Programming">PHP Programming</option>
                        <option value="Database">Database</option>
                        <option value="Digital Logic & Design">Digital Logic & Design</option>
                        <option value="Embedded Systems & IoT">Embedded Systems & IoT</option>
                        <option value="Python Programming">Python Programming</option>
                        <option value="Systems Integration and Architecture">Systems Integration and Architecture</option>
                        <option value="Computer Application">Computer Application</option>
                        <option value="Web Design and Development">Web Design and Development</option>
                        <option value="Self-Study">Self-Study</option>
                        <option value="Project Work">Project Work</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Add this inside your form, before the computer selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Date</label>
                    <input type="date" name="reservation_date" required
                           min="<?= date('Y-m-d') ?>"
                           value="<?= date('Y-m-d') ?>"
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Select your preferred date</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Time</label>
                    <input type="time" name="reservation_time" required
                           min="07:30" max="22:00"
                           value="<?= date('H:i') ?>"
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Available: 7:30 AM - 10:00 PM</p>
                </div>
            </div>
        </div>
        <div id="computersContainer" class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-6">
            <!-- Computers will be loaded here via AJAX -->
        </div>
        <div class="flex justify-end border-t pt-4 sticky bottom-0 bg-white z-10">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 mr-2 transition-colors shadow-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    let currentRoomId = null;
    const hasReservationToday = <?php echo $hasReservationToday ? 'true' : 'false'; ?>;
    
    function showComputers(roomId) {
        // If student already has a reservation today, show alert and prevent opening the modal
        if (hasReservationToday) {
            alert("You have already reserved a computer today. You can only make one reservation per day.");
            return;
        }
        
        currentRoomId = roomId;
        const modal = document.getElementById('computerModal');
        const modalContent = document.getElementById('modalContent');
        const computersContainer = document.getElementById('computersContainer');
        const roomInfoText = document.getElementById('roomInfoText');
        const purposeSelect = document.getElementById('purpose-select');
        
        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Update room info text
        roomInfoText.textContent = `Loading computers for Laboratory Room ${roomId}...`;
        
        // Clear previous content and show loading
        computersContainer.innerHTML = `
            <div class="col-span-full flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                <span class="ml-3 text-gray-600 font-medium">Loading computers...</span>
            </div>
        `;
        
        // Fetch computers via AJAX
        fetch(`get_computers.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(computers => {
                // Update room info
                roomInfoText.textContent = `Select a computer for Laboratory Room ${roomId}`;
                
                computersContainer.innerHTML = '';
                
                if (computers.length === 0) {
                    computersContainer.innerHTML = `
                        <div class="col-span-full text-center p-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-600">No computers available in this room.</p>
                        </div>
                    `;
                    return;
                }
                
                computers.forEach(computer => {
                    const computerCard = document.createElement('div');
                    computerCard.className = `p-4 border rounded-lg text-center shadow-sm transition-all duration-200 ${computer.status === 'available' ? 'border-green-500 bg-green-50 hover:shadow-md transform hover:-translate-y-1' : 'border-red-500 bg-red-50'}`;
                    
                    computerCard.innerHTML = `
                        <div class="flex flex-col items-center">
                            <div class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="font-medium text-gray-800 text-sm sm:text-base">${computer.computer_name}</p>
                            <span class="text-xs sm:text-sm my-1 ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'} capitalize inline-block px-2 py-1 rounded-full ${computer.status === 'available' ? 'bg-green-100' : 'bg-red-100'}">${computer.status}</span>
                            ${computer.status === 'available' ? `
                            <button onclick="reserveComputer(${roomId}, ${computer.computer_id}, '${computer.computer_name}')" 
                                    class="w-full mt-2 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Reserve
                            </button>
                            ` : ''}
                        </div>
                    `;
                    
                    computersContainer.appendChild(computerCard);
                });
            })
            .catch(error => {
                console.error('Error fetching computers:', error);
                computersContainer.innerHTML = `
                    <div class="col-span-full text-center p-8">
                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2 text-red-600">Error loading computers. Please try again.</p>
                    </div>
                `;
            });
    }
    
    function reserveComputer(roomId, computerId, computerName) {
        // Double-check that the student doesn't have a reservation today
        if (hasReservationToday) {
            alert("You have already reserved a computer today. You can only make one reservation per day.");
            closeModal();
            return;
        }
        
        const purpose = document.getElementById('purpose-select').value;
        
        // Create and submit a form with the reservation details
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'reserve.php';
        
        // Room ID
        const roomInput = document.createElement('input');
        roomInput.type = 'hidden';
        roomInput.name = 'room_id';
        roomInput.value = roomId;
        form.appendChild(roomInput);
        
        // Computer ID
        const computerInput = document.createElement('input');
        computerInput.type = 'hidden';
        computerInput.name = 'computer_id';
        computerInput.value = computerId;
        form.appendChild(computerInput);
        
        // Purpose
        const purposeInput = document.createElement('input');
        purposeInput.type = 'hidden';
        purposeInput.name = 'purpose';
        purposeInput.value = purpose;
        form.appendChild(purposeInput);
        
        // Append form to body and submit
        document.body.appendChild(form);
        form.submit();
    }
    
    function closeModal() {
        const modal = document.getElementById('computerModal');
        const modalContent = document.getElementById('modalContent');
        
        // Hide with animation
        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    // Close modal when clicking outside of it
    document.getElementById('computerModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });

    // Add keyboard shortcut to close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('computerModal').classList.contains('hidden')) {
            closeModal();
        }
    });

    // Add this JavaScript after your existing scripts
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="reservation_date"]');
        const timeInput = document.querySelector('input[name="reservation_time"]');
        const today = new Date();
        const currentHour = today.getHours();
        const currentMinutes = today.getMinutes();

        // Set min date to today
        dateInput.min = today.toISOString().split('T')[0];

        // Handle date change
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const isToday = selectedDate.toDateString() === today.toDateString();

            if (isToday) {
                // If today, set min time to current time + 1 hour
                const minHour = currentHour + 1;
                const minTime = `${minHour.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`;
                timeInput.min = minTime;
                
                // If current time is after 9 PM, disable time selection
                if (currentHour >= 21) {
                    timeInput.disabled = true;
                    timeInput.value = '';
                    alert('Reservations are not available after 9 PM');
                }
            } else {
                // For future dates, allow all times
                timeInput.min = '07:30';
                timeInput.max = '22:00';
                timeInput.disabled = false;
            }
        });

        // Handle time change
        timeInput.addEventListener('change', function() {
            const selectedTime = this.value;
            const [hours, minutes] = selectedTime.split(':').map(Number);
            
            // Check if time is within business hours
            if (hours < 7 || (hours === 7 && minutes < 30) || hours >= 22) {
                alert('Please select a time between 7:30 AM and 10:00 PM');
                this.value = '';
            }
        });

        // Initial check for today's date
        if (dateInput.value === today.toISOString().split('T')[0]) {
            const minHour = currentHour + 1;
            const minTime = `${minHour.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`;
            timeInput.min = minTime;
        }
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    #modalContent {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Add responsive styles for different screen sizes */
    @media (max-width: 640px) {
        #modalContent {
            max-height: 85vh;
            margin: 0;
            width: 95%;
            padding: 1rem;
        }
        
        #computersContainer {
            gap: 0.5rem;
        }
    }
    
    /* For extremely small screens */
    @media (max-width: 480px) {
        #modalContent {
            max-height: 90vh;
            padding: 0.75rem;
            margin: 0;
        }
        
        #computersContainer {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        #computerModal {
            padding: 0.5rem;
        }
    }
    
    /* Custom scrollbar for the modal */
    #modalContent::-webkit-scrollbar {
        width: 8px;
    }
    
    #modalContent::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #modalContent::-webkit-scrollbar-thumb {
        background: #cdcdcd;
        border-radius: 10px;
    }
    
    #modalContent::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
</style>  