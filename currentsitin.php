<?php
include "connection.php"; 
include "adminlayout.php"; // Include layout file

// Fetch students currently sitting in
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL "; 

$result_current = $conn->query($query_current);

// sitin records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC";

$result_timedout = $conn->query($query_timedout);
?>
<div class="max-w-5xl p-6 mx-auto bg-gray-100 shadow-md rounded-lg">

<!-- Current Sit-In Students Section -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-bold mb-4">Current Sit-In Students</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Student ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Time In</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if ($result_current->num_rows > 0): ?>
                <?php while ($row = $result_current->fetch_assoc()): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($row['idno']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['course']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['time_in']) ?></td>
                        <td class="border p-2">
                            <button class="px-4 py-2 bg-red-500 text-white rounded timeout-btn" 
                                data-id="<?= $row['sitin_id'] ?>" 
                                data-student="<?= $row['idno'] ?>">
                                Timeout
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center p-4 text-gray-500">No active sit-ins</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Timed-Out Sit-In Records Section -->
<div class="bg-white shadow-md w-full rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Timed-Out Sit-In Records</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Student ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Time In</th>
                <th class="border p-2">Time Out</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if ($result_timedout->num_rows > 0): ?>
                <?php while ($row = $result_timedout->fetch_assoc()): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($row['idno']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['course']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['time_in']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['time_out']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center p-4 text-gray-500">No timed-out records</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>

<script>
$(document).ready(function () {
    $(".timeout-btn").click(function () {
        let sitinId = $(this).data("id");
        let studentId = $(this).data("student");

        if (confirm("Are you sure you want to log out this student?")) {
            $.ajax({
                url: "logout_sitin.php",
                type: "POST",
                data: { sitin_id: sitinId, student_id: studentId },
                success: function (response) {
                    alert(response);
                    location.reload(); // Refresh the page after logout
                }
            });
        }
    });
});
</script>

