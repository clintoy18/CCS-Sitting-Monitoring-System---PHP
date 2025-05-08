<?php
session_start();
include "../includes/adminlayout.php";
include "../includes/adminauth.php";
include "../includes/connection.php";
?>

<div class="p-6 bg-gray-100 min-h-screen">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-800">
            <i class="fas fa-desktop mr-3 text-blue-600"></i>PC Management
        </h1>
        <div class="text-sm text-gray-600">
            <i class="far fa-clock mr-2"></i><?= date('F d, Y h:i A') ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-8 flex flex-wrap gap-4">
        <button onclick="updateAllStatus('available')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>Set All Available
        </button>
        <button onclick="updateAllStatus('maintenance')" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-300 flex items-center">
            <i class="fas fa-tools mr-2"></i>Set All Maintenance
        </button>
        <button onclick="updateAllStatus('offline')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
            <i class="fas fa-power-off mr-2"></i>Set All Offline
        </button>
        <a href="update_computers.php" onclick="return confirm('This will delete all existing computer records and recreate them with standardized naming. Continue?')" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center">
            <i class="fas fa-sync-alt mr-2"></i>Standardize All Computers
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Lab Room</label>
                <select id="labFilter" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Labs</option>
                    <?php
                    $labQuery = "SELECT room_id, room_name, capacity, status FROM rooms ORDER BY room_name";
                    $labResult = mysqli_query($conn, $labQuery);
                    while ($lab = mysqli_fetch_assoc($labResult)) {
                        $statusClass = match($lab['status']) {
                            'available' => 'text-green-600',
                            'maintenance' => 'text-yellow-600',
                            'full' => 'text-red-600',
                            default => 'text-gray-600'
                        };
                        echo "<option value='" . htmlspecialchars($lab['room_name']) . "' 
                                  data-capacity='" . htmlspecialchars($lab['capacity']) . "'
                                  data-status='" . htmlspecialchars($lab['status']) . "'
                                  class='" . $statusClass . "'>
                                " . htmlspecialchars($lab['room_name']) . " (Capacity: " . htmlspecialchars($lab['capacity']) . " PCs)
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="statusFilter" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="in_use">In Use</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search by PC ID..." 
                       class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Computers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="computersGrid">
        <?php
        // First, let's create the computers table if it doesn't exist
        $createTableQuery = "CREATE TABLE IF NOT EXISTS computers (
            computer_id VARCHAR(50) PRIMARY KEY,
            room_name VARCHAR(50),
            status ENUM('available', 'in_use', 'maintenance', 'offline') DEFAULT 'available',
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (room_name) REFERENCES rooms(room_name)
        )";
        mysqli_query($conn, $createTableQuery);

        // Fetch computers with proper room information
        $computerQuery = "SELECT c.*, r.room_name, r.capacity 
                         FROM computers c 
                         LEFT JOIN rooms r ON c.room_id = r.room_id 
                         ORDER BY r.room_name, c.computer_name";
        $computerResult = mysqli_query($conn, $computerQuery);

        if ($computerResult && mysqli_num_rows($computerResult) > 0):
            while ($computer = mysqli_fetch_assoc($computerResult)):
                $statusClass = match($computer['status']) {
                    'available' => 'bg-green-100 text-green-800 border-green-200',
                    'in-use' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                };
        ?>
            <div class="computer-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6" 
                 data-lab="<?= htmlspecialchars($computer['room_name']) ?>"
                 data-status="<?= htmlspecialchars($computer['status']) ?>"
                 data-id="<?= htmlspecialchars($computer['computer_name']) ?>">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($computer['computer_name']) ?></h3>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($computer['room_name']) ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                        <?= ucfirst(htmlspecialchars($computer['status'])) ?>
                    </span>
                </div>
                <div class="space-y-3">
                    <div class="text-sm text-gray-600">
                        <i class="far fa-clock mr-2"></i>
                        Last used: <?= $computer['last_used'] ? date('M d, Y h:i A', strtotime($computer['last_used'])) : 'Never' ?>
                    </div>
                    <select onchange="updateStatus('<?= $computer['computer_id'] ?>', this.value)" 
                            class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="available" <?= $computer['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="in-use" <?= $computer['status'] == 'in-use' ? 'selected' : '' ?>>In Use</option>
                        <option value="maintenance" <?= $computer['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    </select>
                </div>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div class="col-span-full text-center p-8 bg-white rounded-xl shadow-lg">
                <i class="fas fa-desktop text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No computers found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateStatus(computerId, status) {
    fetch('update_computer_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `computer_id=${computerId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

function updateAllStatus(status) {
    if (confirm('Are you sure you want to set all computers to ' + status + '?')) {
        fetch('update_all_computers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating computers: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating computers');
        });
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const labFilter = document.getElementById('labFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const computerCards = document.querySelectorAll('.computer-card');

    function applyFilters() {
        const labValue = labFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        computerCards.forEach(card => {
            const lab = card.dataset.lab.toLowerCase();
            const status = card.dataset.status.toLowerCase();
            const id = card.dataset.id.toLowerCase();

            const labMatch = !labValue || lab === labValue;
            const statusMatch = !statusValue || status === statusValue;
            const searchMatch = !searchValue || id.includes(searchValue);

            card.style.display = labMatch && statusMatch && searchMatch ? 'block' : 'none';
        });

        // Update room status indicator
        const selectedOption = labFilter.options[labFilter.selectedIndex];
        if (selectedOption && selectedOption.dataset.status) {
            const status = selectedOption.dataset.status;
            const capacity = selectedOption.dataset.capacity;
            
            // You can add additional UI updates here based on room status and capacity
            console.log(`Selected room status: ${status}, Capacity: ${capacity}`);
        }
    }

    labFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});
</script> 