<?php
session_start();
include "../includes/connection.php"; // Updated path for connection.php
include "../includes/adminlayout.php"; // Updated path for admin layout

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../auth/login.php"); // Updated path for login.php
    exit();
}

$admin_id = $_SESSION['admin_id']; // Get the logged-in admin's ID

// Fetch current sit-in records
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL"; // Fetch all ongoing sit-ins
$result_current = $conn->query($query_current);

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL 
                   ORDER BY si.time_out DESC"; // Fetch all completed sit-ins
$result_timedout = $conn->query($query_timedout);
?>

<div class="max-w-7xl p-6 mx-auto bg-gray-100 shadow-xl rounded-lg space-y-8">
    <!-- Current Sit-In Students Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Ongoing Sit-In Records</h2>

        <table class="w-full table-auto border-collapse border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-3 text-left">Student ID</th>
                    <th class="border p-3 text-left">Name</th>
                    <th class="border p-3 text-left">Course</th>
                    <th class="border p-3 text-left">Laboratory</th>
                    <th class="border p-3 text-left">Time In</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php if ($result_current->num_rows > 0): ?>
                    <?php while ($row = $result_current->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-3"><?= htmlspecialchars($row['idno']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['course']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['lab']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_in']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center p-4 text-gray-500">No ongoing sit-ins</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Timed-Out Sit-In Records Section -->
    <div class="bg-white shadow-md w-full rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Completed Sit-In Records</h2>
        <table class="w-full table-auto border-collapse border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-3 text-left">Student ID</th>
                    <th class="border p-3 text-left">Name</th>
                    <th class="border p-3 text-left">Course</th>
                    <th class="border p-3 text-left">Laboratory</th>
                    <th class="border p-3 text-left">Time In</th>
                    <th class="border p-3 text-left">Time Out</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php if ($result_timedout->num_rows > 0): ?>
                    <?php while ($row = $result_timedout->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-3"><?= htmlspecialchars($row['idno']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['course']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['lab']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_in']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_out']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center p-4 text-gray-500">No completed sit-ins</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Feedback Section -->
    <div class="bg-white shadow-md w-full rounded-lg p-6 mt-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Submit Feedback</h2>
        <form action="submit_feedback.php" method="POST">
            <div class="mb-4">
                <label for="admin_id" class="block text-gray-700 font-medium mb-2">Admin ID</label>
                <input type="text" id="admin_id" name="admin_id" class="w-full p-3 border rounded-lg" value="<?= htmlspecialchars($admin_id) ?>" readonly>
            </div>
            <div class="mb-4">
                <label for="feedback" class="block text-gray-700 font-medium mb-2">Feedback</label>
                <textarea id="feedback" name="feedback" class="w-full p-3 border rounded-lg" placeholder="Write your feedback here..." required></textarea>
            </div>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out">
                Submit Feedback
            </button>
        </form>
    </div>
</div>