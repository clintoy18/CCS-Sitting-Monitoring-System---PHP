<?php
include "connection.php"; 
include "adminlayout.php"; // Include layout file

$result = "SELECT * FROM studentinfo";

$studentresult = $conn->query($result);
?>
<div class="max-w-5xl p-6 mx-auto bg-gray-100 shadow-md rounded-lg">

<!-- Timed-Out Sit-In Records Section -->
<div class="bg-white shadow-md w-full rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Student List</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Student ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Year Level</th>
                <th class="border p-2">Remaining Session</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($studentresult->num_rows > 0): ?>
                <?php while ($row = $studentresult->fetch_assoc()): ?>
                    <tr class="text-center" >
                        <td class="border p-2"><?= htmlspecialchars($row['idno']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['course']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['year_level']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['session']) ?></td>
                        <td class="border p-2">
                        <button class="px-4 py-2 bg-red-500 text-white rounded timeout-btn" 
                                data-id="<?= $row['idno'] ?>" 
                                data-student="<?= $row['idno'] ?>">
                                Delete
                            </button>
                        </td>
             
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


