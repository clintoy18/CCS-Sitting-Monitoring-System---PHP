<?php
session_start();
include "../includes/layout.php"; 
include "../includes/auth.php";
?>

<h1 class="text-3xl font-semibold text-center text-gray-800 p-8 ">Available Rooms</h1>
<div class="px-8 py-8 grid gap-2" data-hs-datatable='{
    "pageLength": 10,
    "pagingOptions": {
        "pageBtnClasses": "min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none"
    }
}'>
    <div class="overflow-x-auto min-h-[520px] bg-white rounded-lg shadow-lg p-4">
        <div class="min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Room Number
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Student/User ID
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                   Room Capacity
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Activity/Status
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-2 px-3 text-end font-normal text-center text-sm text-gray-500 --exclude-from-ordering">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php
                        include '../includes/connection.php';
                  
                        if (!isset($_SESSION["idno"])) {
                        echo "<tr><td colspan='5' class='p-3 whitespace-nowrap text-sm text-red-600'>Please log in to reserve a room.</td></tr>";
                        exit;
                        }

                        $userID = $_SESSION["idno"];

                        // Fetch student's remaining sessions
                        $check_sessions = "SELECT `session` FROM studentinfo WHERE idno = ?";
                        $stmt = $conn->prepare($check_sessions);
                        $stmt->bind_param("i", $userID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $student = $result->fetch_assoc();
                        $remaining_sessions = $student["session"];

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

                                echo "<tr>";
                                echo "<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800'>" . $room_id . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-sm text-gray-800'>" . $row["room_name"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-sm text-gray-800'>" . $row["capacity"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-sm text-gray-800'>" . $row["status"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-end text-sm font-medium text-center'>";
                                
                                if ($remaining_sessions <= 0) {
                                echo "<span class='text-red-600 font-semibold'>No More Reservations Allowed</span>";
                                } elseif ($row["capacity"] <= 0) {
                                echo "<span class='text-red-600 font-semibold'>Room is Full</span>";
                                } elseif ($isReserved) {
                                echo "<button disabled class='inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-gray-400 text-gray-500 cursor-not-allowed opacity-50'>Already Reserved</button>";
                                } else {
                                echo "<button type='button' onclick='showComputers(" . $room_id . ")' class='inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800'>
                                        View Computers
                                     </button>";
                                }
                                
                                echo "</td>";
                                echo "</tr>";

                                $stmt->close();
                        }
                        } else {
                        echo "<tr><td colspan='5' class='p-3 whitespace-nowrap text-sm text-gray-800'>No records found</td></tr>";
                        }

                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-1 mt-4 hidden" data-hs-datatable-paging="">
        <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" data-hs-datatable-ping-prev="">
            <span aria-hidden="true">«</span>
            <span class="sr-only">Previous</span>
        </button>
        <div class="flex items-center space-x-1 [&>.active]:bg-gray-100" data-hs-datatable-paging-pages=""></div>
        <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" data-hs-datatable-ping-next="">
            <span class="sr-only">Next</span>
            <span aria-hidden="true">»</span>
        </button>
    </div>
</div>

<!-- Computer Selection Modal -->
<div id="computerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-3xl rounded-lg shadow-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Select a Computer</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="computersContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4">
            <!-- Computers will be loaded here via AJAX -->
        </div>
    </div>
</div>

<script>
    function showComputers(roomId) {
        const modal = document.getElementById('computerModal');
        const computersContainer = document.getElementById('computersContainer');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Clear previous content
        computersContainer.innerHTML = '<div class="col-span-full text-center">Loading computers...</div>';
        
        // Fetch computers via AJAX
        fetch(`get_computers.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(computers => {
                computersContainer.innerHTML = '';
                
                if (computers.length === 0) {
                    computersContainer.innerHTML = '<div class="col-span-full text-center">No computers available in this room.</div>';
                    return;
                }
                
                computers.forEach(computer => {
                    const computerCard = document.createElement('div');
                    computerCard.className = `p-4 border rounded-lg text-center ${computer.status === 'available' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'}`;
                    
                    computerCard.innerHTML = `
                        <div class="mb-2">
                            <i class="fas fa-desktop text-2xl ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'}"></i>
                        </div>
                        <p class="font-medium">${computer.computer_name}</p>
                        <p class="text-sm ${computer.status === 'available' ? 'text-green-600' : 'text-red-600'} capitalize">${computer.status}</p>
                        ${computer.status === 'available' ? `
                        <form method="POST" action="reserve.php" class="mt-2">
                            <input type="hidden" name="room_id" value="${roomId}">
                            <input type="hidden" name="computer_id" value="${computer.computer_id}">
                            <button type="submit" class="w-full px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Reserve
                            </button>
                        </form>
                        ` : ''}
                    `;
                    
                    computersContainer.appendChild(computerCard);
                });
            })
            .catch(error => {
                console.error('Error fetching computers:', error);
                computersContainer.innerHTML = '<div class="col-span-full text-center text-red-600">Error loading computers. Please try again.</div>';
            });
    }
    
    function closeModal() {
        document.getElementById('computerModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside of it
    document.getElementById('computerModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });
</script>
</div>
</div>  