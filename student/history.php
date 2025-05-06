<?php
session_start();
include "../includes/connection.php";
include "../includes/layout.php";

if (!isset($_SESSION['idno'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['idno'];

// Fetch current sit-in records (no pagination needed here)
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.computer, si.sitin_purpose, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL AND si.idno = ?";
$stmt_current = $conn->prepare($query_current);
$stmt_current->bind_param("i", $user_id);
$stmt_current->execute();
$result_current = $stmt_current->get_result();

// Pagination setup
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records count
$count_query = "SELECT COUNT(*) AS total FROM sit_in_records WHERE time_out IS NOT NULL AND idno = ?";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch paginated timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.computer, si.sitin_purpose, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL AND si.idno = ?
                   ORDER BY time_out DESC
                   LIMIT ? OFFSET ?";
$stmt_timedout = $conn->prepare($query_timedout);
$stmt_timedout->bind_param("iii", $user_id, $records_per_page, $offset);
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
                    <th class="border p-3 text-left">Computer</th>
                    <th class="border p-3 text-left">Purpose</th>
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
                            <td class="border p-3"><?= htmlspecialchars($row['computer'] ?: 'N/A') ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['sitin_purpose']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_in']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center p-4 text-gray-500">No active sit-ins</td></tr>
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
                    <th class="border p-3 text-left">Computer</th>
                    <th class="border p-3 text-left">Purpose</th>
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
                            <td class="border p-3"><?= htmlspecialchars($row['computer'] ?: 'N/A') ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['sitin_purpose']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_in']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($row['time_out']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center p-4 text-gray-500">No timed-out records</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                       class="px-4 py-2 border rounded <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
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
