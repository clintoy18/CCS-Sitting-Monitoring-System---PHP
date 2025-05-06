<?php
include "../includes/connection.php"; 
include "../includes/adminlayout.php"; // Include layout file

// Fetch all reservations
$query = "SELECT r.reservation_id, r.idno, r.room_id, r.computer_id, r.start_time, r.end_time, r.status, 
                s.fname, s.lname, s.course, c.computer_name, rm.room_name
          FROM reservations r
          JOIN studentinfo s ON r.idno = s.idno
          JOIN computers c ON r.computer_id = c.computer_id
          JOIN rooms rm ON r.room_id = rm.room_id
          WHERE r.status = 'pending'"; // Only pending reservations
$reservationResult = $conn->query($query);


?>

<div class="max-w-5xl p-6 mx-auto bg-gray-100 shadow-md rounded-lg">
    <!-- Reservation List Section -->
    <div class="bg-white shadow-md w-full rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Pending Reservations</h2>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Reservation ID</th>
                    <th class="border p-2">Student Name</th>
                    <th class="border p-2">Course</th>
                    <th class="border p-2">Computer</th>
                    <th class="border p-2">Room</th>
                    <th class="border p-2">Start Time</th>
                    <th class="border p-2">End Time</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reservationResult->num_rows > 0): ?>
                    <?php while ($row = $reservationResult->fetch_assoc()): ?>
                        <tr class="text-center">
                            <td class="border p-2"><?= htmlspecialchars($row['reservation_id']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['course']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['computer_name']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['room_name']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['start_time']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['end_time']) ?></td>
                            <td class="border p-2">
                                    <button class="px-4 py-2 bg-red-500 text-white rounded delete-btn" 
                                    onclick="approveReservation('<?= $row['reservation_id'] ?>')">Approve</button>
                                    <button class="px-4 py-2 bg-blue-500 text-white rounded reset-btn" 
                                    onclick="rejectReservation('<?= $row['reservation_id'] ?>')">Reject</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center p-4 text-gray-500">No pending reservations found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Approve Reservation
function approveReservation(reservationId) {
    if (confirm('Are you sure you want to approve this reservation?')) {
        fetch(`admin_approval.php?id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation approved successfully!');
                location.reload(); // Reload the page to see the changes
            } else {
                alert('Error approving reservation: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error)); // Handle network errors
    }
}

// Reject Reservation
function rejectReservation(reservationId) {
    if (confirm('Are you sure you want to reject this reservation?')) {
        fetch(`reject_reservation.php?id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation rejected successfully!');
                location.reload(); // Reload the page to see the changes
            } else {
                alert('Error rejecting reservation: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error)); // Handle network errors
    }
}
</script>
