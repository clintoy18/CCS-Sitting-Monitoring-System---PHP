<?php
include "connection.php"; 

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include "adminlayout.php"; // Include layout file

// Fetch current sit-in records
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL"; 
$result_current = $conn->query($query_current);

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC";
$result_timedout = $conn->query($query_timedout);
?>

<div class="max-w-7xl p-6 mx-auto bg-gray-100 shadow-xl rounded-lg space-y-8">

    <!-- Current Sit-In Students Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Current Sit-In Students</h2>

        <table class="w-full table-auto border-collapse border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-3 text-left">Student ID</th>
                    <th class="border p-3 text-left">Name</th>
                    <th class="border p-3 text-left">Course</th>
                    <th class="border p-3 text-left">Laboratory</th>
                    <th class="border p-3 text-left">Time In</th>
                    <th class="border p-3 text-left">Action</th>
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
                            <td class="border p-3">
                                <button class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300 ease-in-out flex items-center justify-center space-x-2 timeout-btn"
                                        data-id="<?= $row['sitin_id'] ?>" 
                                        data-student="<?= $row['idno'] ?>">
                                    <i class="fas fa-sign-out-alt"></i> <span>Timeout</span>
                                </button>
                            </td>
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
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">Timed-Out Sit-In Records</h2>

        <!-- Buttons for file generation -->
        <div class="text-right mt-6 space-x-4">
            <!-- PDF Button with icon -->
            <button id="generate-pdf" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out flex items-center justify-center space-x-2">
                <i class="fas fa-file-pdf"></i> <span> PDF</span>
            </button>
            
            <!-- CSV Button with icon -->
            <button id="generate-csv" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-300 ease-in-out flex items-center justify-center space-x-2">
                <i class="fas fa-file-csv"></i> <span> CSV</span>
            </button>
            
            <!-- DOCX Button with icon -->
            <button id="generate-docx" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-300 ease-in-out flex items-center justify-center space-x-2">
                <i class="fas fa-file-word"></i> <span>DOCX</span>
            </button>
        </div>

        <table class="w-full table-auto border-collapse border border-gray-300 rounded-lg mt-6">
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

    // Trigger PDF generation
    $("#generate-pdf").click(function () {
        window.location.href = "generate_pdf.php"; // Redirect to PDF generation page
    });

    // Trigger CSV generation
    $("#generate-csv").click(function () {
        window.location.href = "generate_csv.php"; // Redirect to CSV generation page
    });

    // Trigger DOCX generation
    $("#generate-docx").click(function () {
        window.location.href = "generate_docx.php"; // Redirect to DOCX generation page
    });
});
</script>
