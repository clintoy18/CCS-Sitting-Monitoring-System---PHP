<?php
include "../includes/connection.php"; 
include "../includes/adminlayout.php";

// Fetch all student records
$query = "SELECT * FROM studentinfo ORDER BY lname, fname";
$studentresult = $conn->query($query);

// Get total students count
$totalStudents = $studentresult->num_rows;
?>

<div class="p-6 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-users mr-3 text-blue-600"></i>Student Management
                </h1>
                <p class="text-gray-600 mt-1">Manage student records and sessions</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-gray-600">Total Students:</span>
                    <span class="font-bold text-blue-600 ml-2"><?= $totalStudents ?></span>
                </div>
            </div>
        </div>

        <!-- Student List Section -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-list mr-2 text-blue-600"></i>Student List
                    </h2>
                    <div class="flex space-x-3">
                        <button onclick="resetAllSessions()" 
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all duration-300 flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i>Reset All Sessions
                        </button>
                        <button onclick="exportToCSV()" 
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 flex items-center">
                            <i class="fas fa-file-export mr-2"></i>Export to CSV
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Session</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($studentresult->num_rows > 0): ?>
                                <?php while ($row = $studentresult->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($row['idno']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($row['fname'] . " " . $row['lname']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['course']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Year <?= htmlspecialchars($row['year_level']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-sm rounded-full <?= $row['session'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= htmlspecialchars($row['session']) ?> sessions
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="resetSession('<?= $row['idno'] ?>')" 
                                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-300"
                                                        title="Reset Session">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button onclick="deleteStudent('<?= $row['idno'] ?>')" 
                                                        class="text-red-600 hover:text-red-900 transition-colors duration-300"
                                                        title="Delete Student">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <i class="fas fa-users-slash text-4xl mb-2 text-gray-400"></i>
                                            <p>No student records found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetSession(studentId) {
    Swal.fire({
        title: 'Reset Session',
        text: 'Are you sure you want to reset this student\'s session?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#EF4444',
        confirmButtonText: 'Yes, reset it!',
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600',
            confirmButton: 'px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-300',
            cancelButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`reset_session.php?id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Session has been reset.',
                        icon: 'success',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while resetting the session.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1F2937',
                    customClass: {
                        title: 'text-xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                    }
                });
            });
        }
    });
}

function deleteStudent(studentId) {
    Swal.fire({
        title: 'Delete Student',
        text: 'Are you sure you want to delete this student? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, delete it!',
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
            fetch(`delete_student.php?id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Student has been deleted.',
                        icon: 'success',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the student.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1F2937',
                    customClass: {
                        title: 'text-xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                    }
                });
            });
        }
    });
}

function resetAllSessions() {
    Swal.fire({
        title: 'Reset All Sessions',
        text: 'Are you sure you want to reset all student sessions?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, reset all!',
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
            fetch('reset_all_sessions.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'All sessions have been reset.',
                        icon: 'success',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1F2937',
                        customClass: {
                            title: 'text-xl font-bold text-gray-800',
                            content: 'text-gray-600',
                            confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while resetting all sessions.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1F2937',
                    customClass: {
                        title: 'text-xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300'
                    }
                });
            });
        }
    });
}

function exportToCSV() {
    window.location.href = 'generate_csv.php';
}
</script>

<!-- Add SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
