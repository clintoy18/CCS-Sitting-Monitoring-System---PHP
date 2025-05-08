<?php
// Start session and include files before any output
session_start();
include "../includes/adminauth.php";
include "../includes/connection.php";

// Handle bulk status update
if (isset($_POST['action']) && $_POST['action'] === 'update_all_status') {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $valid_statuses = ['available', 'in-use', 'maintenance'];
    
    if (in_array($status, $valid_statuses)) {
        $query = "UPDATE computers SET status = ?, last_used = NOW()";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    // Use JavaScript for redirection instead of header
    echo "<script>window.location.href = 'pc_management.php';</script>";
    exit;
}

// Include layout after all header modifications
include "../includes/adminlayout.php";
?>

<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-desktop text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">PC Management</h1>
                    <p class="text-sm text-gray-600">Manage and monitor computer status across all laboratories</p>
                </div>
            </div>
            <div class="text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg">
                <i class="far fa-clock mr-2"></i><?= date('F d, Y h:i A') ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <button onclick="updateAllStatus('available')" 
                    class="px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-all duration-300 flex items-center gap-2 border border-green-200">
                <i class="fas fa-check-circle"></i>
                <span>Set All Available</span>
            </button>
            <button onclick="updateAllStatus('maintenance')" 
                    class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-all duration-300 flex items-center gap-2 border border-yellow-200">
                <i class="fas fa-tools"></i>
                <span>Set All Maintenance</span>
            </button>
            <a href="update_computers.php" 
               onclick="return confirm('This will delete all existing computer records and recreate them with standardized naming. Continue?')" 
               class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-all duration-300 flex items-center gap-2 border border-blue-200">
                <i class="fas fa-sync-alt"></i>
                <span>Standardize All Computers</span>
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Computers</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Laboratory</label>
                <select id="labFilter" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <option value="">All Laboratories</option>
                    <?php
                    $labQuery = "SELECT r.room_id, r.room_name, r.capacity, r.status, 
                                COUNT(c.computer_id) as computer_count 
                                FROM rooms r 
                                LEFT JOIN computers c ON r.room_id = c.room_id 
                                GROUP BY r.room_id 
                                ORDER BY r.room_name";
                    $labResult = mysqli_query($conn, $labQuery);
                    while ($lab = mysqli_fetch_assoc($labResult)) {
                        $statusClass = match($lab['status']) {
                            'available' => 'text-green-600',
                            'maintenance' => 'text-yellow-600',
                            'full' => 'text-red-600',
                            default => 'text-gray-600'
                        };
                        echo "<option value='" . htmlspecialchars($lab['room_id']) . "' 
                                  data-capacity='" . htmlspecialchars($lab['capacity']) . "'
                                  data-status='" . htmlspecialchars($lab['status']) . "'
                                  class='" . $statusClass . "'>
                                " . htmlspecialchars($lab['room_name']) . " (" . htmlspecialchars($lab['computer_count']) . " PCs)
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="in-use">In Use</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search by PC ID..." 
                           class="w-full p-2.5 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Computers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="computersGrid">
        <?php
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
            <div class="computer-card bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-6" 
                 data-lab="<?= htmlspecialchars($computer['room_id']) ?>"
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
                    <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg">
                        <i class="far fa-clock mr-2"></i>
                        Last used: <?= $computer['last_used'] ? date('M d, Y h:i A', strtotime($computer['last_used'])) : 'Never' ?>
                    </div>
                    <select onchange="updateStatus('<?= $computer['computer_id'] ?>', this.value)" 
                            class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
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
            <div class="col-span-full text-center p-12 bg-white rounded-xl shadow-sm">
                <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-desktop text-3xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 text-lg">No computers found</p>
                <p class="text-gray-400 text-sm mt-2">Try adjusting your filters or add new computers</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateStatus(computerId, status) {
    if (!computerId || !status) return;
    
    // Show loading state
    const select = event.target;
    const originalValue = select.value;
    select.disabled = true;

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
            // Update the status badge
            const card = select.closest('.computer-card');
            const statusBadge = card.querySelector('.px-3.py-1');
            let statusClass;
            switch(status) {
                case 'available':
                    statusClass = 'bg-green-100 text-green-800 border-green-200';
                    break;
                case 'in-use':
                    statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                    break;
                case 'maintenance':
                    statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    break;
                default:
                    statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
            }
            statusBadge.className = `px-3 py-1 rounded-full text-sm font-medium ${statusClass}`;
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            
            // Update last used time
            const lastUsed = card.querySelector('.text-sm.text-gray-600');
            lastUsed.innerHTML = `<i class="far fa-clock mr-2"></i>Last used: ${new Date().toLocaleString()}`;
            
            // Update data attribute
            card.dataset.status = status;
        } else {
            alert('Error updating status: ' + data.message);
            select.value = originalValue;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
        select.value = originalValue;
    })
    .finally(() => {
        select.disabled = false;
    });
}

function updateAllStatus(status) {
    if (!status) return;
    
    if (confirm('Are you sure you want to set all computers to ' + status + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_all_status">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const labFilter = document.getElementById('labFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const computerCards = document.querySelectorAll('.computer-card');

    function applyFilters() {
        const labValue = labFilter.value;
        const statusValue = statusFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        computerCards.forEach(card => {
            const lab = card.dataset.lab;
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
            
            // Update the filter section with room information
            const filterSection = document.querySelector('.bg-white.rounded-xl');
            if (selectedOption.value) {
                filterSection.classList.add('border-2');
                filterSection.classList.add(status === 'available' ? 'border-green-200' : 
                                         status === 'maintenance' ? 'border-yellow-200' : 
                                         'border-red-200');
            } else {
                filterSection.classList.remove('border-2', 'border-green-200', 'border-yellow-200', 'border-red-200');
            }
        }
    }

    labFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});
</script> 