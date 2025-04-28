<?php
session_start();
include "../includes/layout.php"; 
include "../includes/auth.php";
?>

<div class="px-8 py-6">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Laboratory Room Reservations</h1>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Available Rooms</h2>
            <div class="text-sm text-gray-600">
                <span class="font-medium">Sessions Remaining:</span> 
                <?php
                    include '../includes/connection.php';
                    if (!isset($_SESSION["idno"])) {
                        echo "<span class='text-red-600'>Please log in to reserve a room</span>";
                    } else {
                        $userID = $_SESSION["idno"];
                        $check_sessions = "SELECT `session` FROM studentinfo WHERE idno = ?";
                        $stmt = $conn->prepare($check_sessions);
                        $stmt->bind_param("i", $userID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $student = $result->fetch_assoc();
                        $remaining_sessions = $student["session"] ?? 0;
                        echo "<span class='" . ($remaining_sessions > 0 ? "text-green-600" : "text-red-600") . " font-bold'>" . $remaining_sessions . "</span>";
                    }
                ?>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Room Number
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Room Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Available Computers
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php
                    if (!isset($_SESSION["idno"])) {
                        echo "<tr><td colspan='5' class='p-4 text-center text-red-600 font-medium'>Please log in to reserve a room.</td></tr>";
                    } else {
                        $userID = $_SESSION["idno"];
                        $remaining_sessions = $student["session"] ?? 0;

                        $sql = "SELECT * FROM rooms";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $room_id = $row["room_id"];

                                // Check if user already reserved this room
                                $check_reservation = "SELECT * FROM reservations WHERE idno = ? AND room_id = ? AND status = 'reserved'";
                                $stmt = $conn->prepare($check_reservation);
                                $stmt->bind_param("ii", $userID, $room_id);
                                $stmt->execute();
                                $reservation_result = $stmt->get_result();
                                $isReserved = $reservation_result->num_rows > 0;

                                // Get available computers count
                                $check_computers = "SELECT COUNT(*) as available_count FROM computers WHERE room_id = ? AND status = 'available'";
                                $comp_stmt = $conn->prepare($check_computers);
                                $comp_stmt->bind_param("i", $room_id);
                                $comp_stmt->execute();
                                $comp_result = $comp_stmt->get_result();
                                $comp_row = $comp_result->fetch_assoc();
                                $available_computers = $comp_row['available_count'];

                                $status_class = $row["status"] === 'available' ? 'text-green-600' : 'text-red-600';
                                
                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$room_id}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>Laboratory {$row["room_name"]}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$available_computers} computers</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><span class='font-medium {$status_class} capitalize'>{$row["status"]}</span></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-center'>";
                                
                                if ($remaining_sessions <= 0) {
                                    echo "<span class='inline-flex px-4 py-2 text-xs font-semibold rounded-md bg-red-100 text-red-800'>No More Reservations</span>";
                                } elseif ($row["capacity"] <= 0 || $available_computers <= 0) {
                                    echo "<span class='inline-flex px-4 py-2 text-xs font-semibold rounded-md bg-red-100 text-red-800'>Room is Full</span>";
                                } elseif ($isReserved) {
                                    echo "<span class='inline-flex px-4 py-2 text-xs font-semibold rounded-md bg-gray-100 text-gray-800'>Already Reserved</span>";
                                } else {
                                    echo "<button type='button' onclick='showComputers({$room_id})' class='inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors'>
                                            <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 mr-2' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7' />
                                            </svg>
                                            Select Computer
                                        </button>";
                                }
                                
                                echo "</td>";
                                echo "</tr>";

                                $stmt->close();
                                $comp_stmt->close();
                            }
                        } else {
                            echo "<tr><td colspan='5' class='p-4 text-center text-gray-500'>No laboratory rooms available at this time.</td></tr>";
                        }
                        mysqli_close($conn);
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Computer Selection Modal - Improved UI -->
<div id="computerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl p-6">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h3 class="text-2xl font-bold text-gray-800">Select a Computer</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <p id="roomInfoText" class="text-gray-600 mb-4">Loading room information...</p>
            
            <!-- Purpose Selection -->
            <div class="mb-4">
                <label for="purpose-select" class="block text-sm font-medium text-gray-700 mb-2">Select Purpose:</label>
                <select id="purpose-select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
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
            </div>
        </div>
        <div id="computersContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
            <!-- Computers will be loaded here via AJAX -->
        </div>
        <div class="flex justify-end border-t pt-4">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 mr-2">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    let currentRoomId = null;
    
    function showComputers(roomId) {
        currentRoomId = roomId;
        const modal = document.getElementById('computerModal');
        const computersContainer = document.getElementById('computersContainer');
        const roomInfoText = document.getElementById('roomInfoText');
        const purposeSelect = document.getElementById('purpose-select');
        
        // Show modal with animation
        modal.classList.remove('hidden');
        modal.classList.add('animate-fade-in');
        
        // Update room info text
        roomInfoText.textContent = `Loading computers for Laboratory Room ${roomId}...`;
        
        // Clear previous content and show loading
        computersContainer.innerHTML = `
            <div class="col-span-full flex justify-center items-center p-8">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-600">Loading computers...</span>
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
                    computerCard.className = `p-4 border rounded-lg text-center shadow-sm transition-all duration-200 ${computer.status === 'available' ? 'border-green-500 bg-green-50 hover:shadow-md' : 'border-red-500 bg-red-50'}`;
                    
                    computerCard.innerHTML = `
                        <div class="mb-3">
                            <i class="fas fa-desktop text-3xl ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'}"></i>
                        </div>
                        <p class="font-medium text-gray-800">${computer.computer_name}</p>
                        <p class="text-sm ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'} capitalize mb-3">${computer.status}</p>
                        ${computer.status === 'available' ? `
                        <button onclick="reserveComputer(${roomId}, ${computer.computer_id}, '${computer.computer_name}')" 
                                class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Reserve
                        </button>
                        ` : ''}
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
        modal.classList.add('animate-fade-out');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('animate-fade-in', 'animate-fade-out');
        }, 200);
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
    
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.2s ease-in forwards;
    }
</style>  