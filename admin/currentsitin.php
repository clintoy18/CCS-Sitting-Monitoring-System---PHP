<?php
include "../includes/connection.php"; 
include "../includes/adminlayout.php"; // Include layout file

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch current sit-in records
$query_current = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.computer, si.sitin_purpose, si.time_in, si.id AS sitin_id 
                  FROM sit_in_records si
                  JOIN studentinfo s ON si.idno = s.idno
                  WHERE si.time_out IS NULL
                  ORDER BY si.time_in DESC"; 
$result_current = $conn->query($query_current);

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.computer, si.sitin_purpose, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC
                   LIMIT 50";
$result_timedout = $conn->query($query_timedout);

// Get counts
$currentCount = $result_current->num_rows;
$timedoutCount = $result_timedout->num_rows;
?>

<div class="p-6 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-desktop mr-3 text-blue-600"></i>Sit-In Monitoring
                </h1>
                <p class="text-gray-600 mt-1">Track and manage student computer laboratory usage</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-gray-600">Active Sessions:</span>
                    <span class="font-bold text-green-600 ml-2"><?= $currentCount ?></span>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-gray-600">Recent Timeouts:</span>
                    <span class="font-bold text-blue-600 ml-2"><?= $timedoutCount ?></span>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'rewarded'): ?>
            <div id="success-message" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 flex items-center" role="alert">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-bold">Success!</p>
                    <p>Student has been rewarded successfully.</p>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <!-- Current Sit-In Students Section -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-users mr-2 text-blue-600"></i>Current Sit-In Students
                    </h2>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        <?= $currentCount ?> Active
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Computer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($result_current->num_rows > 0): ?>
                                <?php while ($row = $result_current->fetch_assoc()): ?>
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
                                            <?= htmlspecialchars($row['lab']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['computer'] ?: 'N/A') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['sitin_purpose']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                                <?= htmlspecialchars($row['time_in']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="timeoutStudent('<?= $row['sitin_id'] ?>')" 
                                                        class="text-red-600 hover:text-red-900 transition-colors duration-300"
                                                        title="Timeout Student">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                                <button onclick="rewardStudent('<?= $row['sitin_id'] ?>')" 
                                                        class="text-yellow-600 hover:text-yellow-900 transition-colors duration-300"
                                                        title="Reward Student">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <i class="fas fa-desktop text-4xl mb-2 text-gray-400"></i>
                                            <p>No active sit-ins</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Timed-Out Sit-In Records Section -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-blue-600"></i>Timed-Out Sit-In Records
                    </h2>
                    <div class="flex space-x-3">
                        <button onclick="generatePDF()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i>PDF
                        </button>
                        <button onclick="generateCSV()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-file-csv mr-2"></i>CSV
                        </button>
                        <button onclick="generateDOCX()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-file-word mr-2"></i>DOCX
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Computer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($result_timedout->num_rows > 0): ?>
                                <?php while ($row = $result_timedout->fetch_assoc()): ?>
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
                                            <?= htmlspecialchars($row['lab']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['computer'] ?: 'N/A') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['sitin_purpose']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                                <?= htmlspecialchars($row['time_in']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                                <?= htmlspecialchars($row['time_out']) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <i class="fas fa-history text-4xl mb-2 text-gray-400"></i>
                                            <p>No timed-out records</p>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function timeoutStudent(sitinId) {
    Swal.fire({
        title: 'Timeout Student',
        text: 'Are you sure you want to log out this student?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, timeout student',
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
            window.location.href = `logout_sitin.php?id=${sitinId}`;
        }
    });
}

function rewardStudent(sitinId) {
    Swal.fire({
        title: 'Reward Student',
        text: 'Are you sure you want to reward this student?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, reward student',
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
            window.location.href = `reward_student.php?id=${sitinId}`;
        }
    });
}

function generatePDF() {
    Swal.fire({
        title: 'Generating PDF',
        text: 'Please wait while we generate the PDF...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Set timeout to close the loader after 2 seconds
    setTimeout(() => {
        Swal.close();
        window.location.href = "generate_pdf.php";
    }, 2000);
}

function generateCSV() {
    Swal.fire({
        title: 'Generating CSV',
        text: 'Please wait while we generate the CSV...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
    window.location.href = "generate_csv.php";
}

function generateDOCX() {
    Swal.fire({
        title: 'Generating DOCX',
        text: 'Please wait while we generate the DOCX...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        background: '#ffffff',
        color: '#1F2937',
        customClass: {
            title: 'text-xl font-bold text-gray-800',
            content: 'text-gray-600'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
    window.location.href = "generate_docx.php";
}
</script>
