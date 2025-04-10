<?php
include "connection.php"; 

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();  // Start the session to get the logged-in user info
if (!isset($_SESSION['idno'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['idno'];  // Get the logged-in user's ID

include "layout.php"; // Include layout file

// Fetch current sit-in records for the logged-in user
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL AND si.idno = ?";  // Filter by logged-in user
$stmt_current = $conn->prepare($query_current);
$stmt_current->bind_param("i", $user_id);
$stmt_current->execute();
$result_current = $stmt_current->get_result();

// Fetch timed-out sit-in records for the logged-in user
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL AND si.idno = ? 
                   ORDER BY time_out DESC";  // Filter by logged-in user
$stmt_timedout = $conn->prepare($query_timedout);
$stmt_timedout->bind_param("i", $user_id);
$stmt_timedout->execute();
$result_timedout = $stmt_timedout->get_result();
?>
<div class="max-w-7xl p-6 mx-auto bg-gray-100 shadow-xl rounded-lg space-y-8">
    <!-- Current Sit-In Students Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">My On-Going Sit-in</h2>

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
                    <tr><td colspan="6" class="text-center p-4 text-gray-500">No active sit-ins</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Timed-Out Sit-In Records Section -->
    <div class="bg-white shadow-md w-full rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Sit-In Records</h2>
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
                    <tr><td colspan="6" class="text-center p-4 text-gray-500">No timed-out records</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Feedback Section -->
    <div class="bg-white shadow-md w-full rounded-lg p-6 mt-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Submit Feedback</h2>
        <form action="submit_feedback.php" method="POST">
            <div class="mb-4">
                <label for="student_id" class="block text-gray-700 font-medium mb-2">Student ID</label>
                <input type="text" id="student_id" name="student_id" class="w-full p-3 border rounded-lg" value="<?= htmlspecialchars($user_id) ?>" readonly>
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

