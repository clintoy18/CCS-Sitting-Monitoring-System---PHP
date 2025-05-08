<?php
include "../includes/connection.php"; 
include "../includes/adminlayout.php";

// Build the query with filters
$query = "SELECT r.reservation_id, r.idno, r.room_id, r.computer_id, r.start_time, r.end_time, r.status, 
                s.fname, s.lname, s.course, c.computer_name, rm.room_name
          FROM reservations r
          JOIN studentinfo s ON r.idno = s.idno
          JOIN computers c ON r.computer_id = c.computer_id
          JOIN rooms rm ON r.room_id = rm.room_id
          WHERE 1=1";

// Apply status filter if set
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $query .= " AND r.status = '$status'";
}

$query .= " ORDER BY r.start_time DESC";
$reservationResult = $conn->query($query);
?>

<div class="max-w-6xl p-6 mx-auto bg-gray-100 rounded-lg">
    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Reservation Logs</h2>

        <!-- Status Filter -->
        <div class="mb-6">
            <form action="" method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label for="status" class="block text-gray-600 font-medium mb-2">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>Filter by Status:
                    </label>
                    <select name="status" id="status" class="w-full border border-gray-200 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition-colors duration-200">
                        <option value="">All Status</option>
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i>Apply Filter
                </button>
            </form>
        </div>

        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-gray-200 text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-3 border">Reservation ID</th>
                    <th class="px-4 py-3 border">Student Name</th>
                    <th class="px-4 py-3 border">Course</th>
                    <th class="px-4 py-3 border">Computer</th>
                    <th class="px-4 py-3 border">Room</th>
                    <th class="px-4 py-3 border">Start Time</th>
                    <th class="px-4 py-3 border">End Time</th>
                    <th class="px-4 py-3 border">Status</th>
                    <th class="px-4 py-3 border text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($reservationResult->num_rows > 0): ?>
                    <?php while ($row = $reservationResult->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['reservation_id']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['course']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['computer_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['room_name']) ?></td>
                            <td class="px-4 py-2 border"><?= date('M d, Y h:i A', strtotime($row['start_time'])) ?></td>
                            <td class="px-4 py-2 border"><?= date('M d, Y h:i A', strtotime($row['end_time'])) ?></td>
                            <td class="px-4 py-2 border">
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
                            <td class="px-4 py-4 border text-center">
                                <?php if ($row['status'] == 'pending'): ?>
                                    <div class="flex space-x-2 justify-center">
                                        <button 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded-md transition duration-150"
                                            onclick="approveReservation('<?= $row['reservation_id'] ?>')">
                                            Approve
                                        </button>
                                        <button 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-md transition duration-150"
                                            onclick="rejectReservation('<?= $row['reservation_id'] ?>')">
                                            Disapprove
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-gray-500 py-4">No reservations found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Approve Reservation
function approveReservation(reservationId) {
    Swal.fire({
        title: 'Approve Reservation',
        text: 'Are you sure you want to approve this reservation?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, approve reservation',
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600',
            confirmButton: 'px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-300',
            cancelButton: 'px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`admin_approval.php?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Reservation approved successfully!',
                        icon: 'success',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error approving reservation: ' + data.message,
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600'
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while processing your request.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1F2937',
                    customClass: {
                        title: 'text-xl font-bold text-gray-800',
                        content: 'text-gray-600'
                    }
                });
                console.error('Error:', error);
            });
        }
    });
}

// Reject Reservation
function rejectReservation(reservationId) {
    Swal.fire({
        title: 'Reject Reservation',
        text: 'Are you sure you want to reject this reservation?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, reject reservation',
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600',
            confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300',
            cancelButton: 'px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`reject_reservation.php?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Reservation rejected successfully!',
                        icon: 'success',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error rejecting reservation: ' + data.message,
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600'
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while processing your request.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1F2937',
                    customClass: {
                        title: 'text-xl font-bold text-gray-800',
                        content: 'text-gray-600'
                    }
                });
                console.error('Error:', error);
            });
        }
    });
}
</script>
