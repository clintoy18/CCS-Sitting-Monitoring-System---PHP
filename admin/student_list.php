<?php
include "../includes/connection.php"; 
include "../includes/adminlayout.php"; // Include layout file

// Fetch all student records
$query = "SELECT * FROM studentinfo";
$studentresult = $conn->query($query);
?>
<div class="max-w-5xl p-6 mx-auto bg-gray-100 shadow-md rounded-lg">

    <!-- Student List Section -->
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
                        <tr class="text-center">
                            <td class="border p-2"><?= htmlspecialchars($row['idno']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['course']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['year_level']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['session']) ?></td>
                            <td class="border p-2">
                                <button class="px-4 py-2 bg-red-500 text-white rounded delete-btn" 
                                    onclick="deleteStudent('<?= $row['idno'] ?>')">Delete</button>
                                <button class="px-4 py-2 bg-blue-500 text-white rounded reset-btn" 
                                    onclick="resetSession('<?= $row['idno'] ?>')">Reset</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center p-4 text-gray-500">No student records found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Reset All Students Button -->
        <div class="mt-4">
            <button class="px-6 py-3 bg-yellow-500 text-white rounded" onclick="resetAllSessions()">Reset All Sessions</button>
        </div>
    </div>
</div>

<script>
function resetSession(studentId) {
    if (confirm('Are you sure you want to reset this student\'s session?')) {
        fetch(`reset_session.php?id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Session reset successfully!');
                location.reload(); // Reload the page to see the changes.
            } else {
                alert('Error resetting session: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error)); // Handle network errors.
    }
}

function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        fetch(`delete_student.php?id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student deleted successfully!');
                location.reload(); // Reload the page to see the changes.
            } else {
                alert('Error deleting student: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error)); // Handle network errors.
    }
}

function resetAllSessions() {
    if (confirm('Are you sure you want to reset all student sessions?')) {
        fetch('reset_all_sessions.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All student sessions reset successfully!');
                location.reload(); // Reload the page to see the changes.
            } else {
                alert('Error resetting all sessions: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error)); // Handle network errors.
    }
}
</script>
