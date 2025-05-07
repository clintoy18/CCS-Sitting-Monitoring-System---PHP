<?php
session_start();
include "../includes/adminlayout.php";
include "../includes/adminauth.php";
include "../includes/connection.php";

// Counts
$totalAdmins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_admins FROM admins"))['total_admins'] ?? 0;
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM studentinfo"))['total_students'] ?? 0;
$totalUsers = $totalAdmins + $totalStudents;

// Year Level Chart
$yearData = mysqli_query($conn, "SELECT year_level, COUNT(*) AS count FROM studentinfo GROUP BY year_level");
$yearLevels = $studentCounts = [];
while ($row = mysqli_fetch_assoc($yearData)) {
    $yearLevels[] = "Year " . $row['year_level'];
    $studentCounts[] = $row['count'];
}

// Sit-in Purpose Chart
$sitinData = mysqli_query($conn, "SELECT sitin_purpose, COUNT(*) AS count FROM sit_in_records WHERE sitin_purpose IS NOT NULL AND sitin_purpose != '' GROUP BY sitin_purpose");
$sitinPurposes = $sitinCounts = [];
while ($row = mysqli_fetch_assoc($sitinData)) {
    $sitinPurposes[] = ucwords(trim($row['sitin_purpose']));
    $sitinCounts[] = $row['count'];
}

// Sit-in Lab Chart
$sitinData1 = mysqli_query($conn, "SELECT lab, COUNT(*) AS count FROM sit_in_records WHERE lab IS NOT NULL AND lab != '' GROUP BY lab");
$sitinLabs = $sitinLabCounts = [];
while ($row = mysqli_fetch_assoc($sitinData1)) {
    $sitinLabs[] = ucwords(trim($row['lab']));
    $sitinLabCounts[] = $row['count'];
}
?>

<div class="p-6 bg-gray-100 min-h-screen">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-800">
            <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>Admin Dashboard
        </h1>
        <div class="text-sm text-gray-600">
            <i class="far fa-clock mr-2"></i><?= date('F d, Y h:i A') ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 mb-8">
        <?php foreach ([
            ['Total Admins', $totalAdmins, 'fas fa-user-shield', 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600'],
            ['Total Students', $totalStudents, 'fas fa-user-graduate', 'bg-gradient-to-br from-green-50 to-green-100 text-green-600'],
            ['Total Users', $totalUsers, 'fas fa-users', 'bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600']
        ] as [$title, $count, $icon, $color]): ?>
        <div class="p-6 <?= $color ?> rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center space-x-4">
            <div class="text-5xl"><i class="<?= $icon ?>"></i></div>
            <div>
                <h2 class="text-lg font-semibold"><?= htmlspecialchars($title) ?></h2>
                <p class="text-4xl font-bold"><?= htmlspecialchars($count) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts & Announcements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sit-in Chart -->
        <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-chart-pie mr-2 text-blue-600"></i>Sit-in Records
                </h2>
                <select id="chartTypeDropdown" class="p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="purpose">Per Purpose</option>
                    <option value="laboratory">Per Laboratory</option>
                </select>
            </div>
            <div class="h-[300px]">
                <canvas id="sitinChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Announcements -->
        <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">
                <i class="fas fa-bullhorn mr-2 text-blue-600"></i>Announcements
            </h2>
            <form action="post_announcement.php" method="POST" class="mb-4">
                <textarea name="announcement" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4" placeholder="Type an announcement..." required></textarea>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>Post Announcement
                </button>
            </form>
            <div class="max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                <ul class="space-y-4">
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                    if ($result):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <li class="p-4 bg-gray-50 border-l-4 border-blue-600 rounded-lg hover:bg-gray-100 transition-all duration-300 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-blue-700 flex items-center">
                                <i class="fas fa-bell mr-2"></i>Announcement
                            </p>
                            <p class="mt-2"><?= htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-sm text-gray-400 mt-2">
                                <i class="far fa-clock mr-1"></i>Posted on <?= date('F d, Y h:i A', strtotime($row['created_at'])) ?>
                            </p>
                        </div>
                        <form action="delete_announcement.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                            <input type="hidden" name="delete_id" value="<?= $row["announcement_id"] ?>">
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all duration-300">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </li>
                    <?php endwhile; endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">
            <i class="fas fa-chart-bar mr-2 text-blue-600"></i>Number of Users
        </h2>
        <div class="h-[300px]">
            <canvas id="usersBarChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Student Feedbacks -->
    <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">
            <i class="fas fa-comments mr-2 text-blue-600"></i>Student Feedbacks
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-4 text-left text-gray-700 font-semibold">Name & Profile</th>
                        <th class="p-4 text-left text-gray-700 font-semibold">Feedback</th>
                        <th class="p-4 text-left text-gray-700 font-semibold">Submitted At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $feedbackQuery = "SELECT f.feedback, f.created_at, 
                                     s.fname, s.lname, s.profile_picture 
                              FROM feedbacks f
                              JOIN studentinfo s ON f.student_id = s.idno
                              ORDER BY f.created_at DESC";
                    $feedbackResult = mysqli_query($conn, $feedbackQuery);

                    if ($feedbackResult && mysqli_num_rows($feedbackResult) > 0):
                        while ($feedback = mysqli_fetch_assoc($feedbackResult)):
                    ?>
                        <tr class="hover:bg-gray-50 transition-all duration-300">
                            <td class="p-4">
                                <div class="flex items-center space-x-4">
                                    <?php if (!empty($feedback['profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($feedback['profile_picture']) ?>" alt="Profile Picture" class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                                    <?php else: ?>
                                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-200 border-2 border-blue-100">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="text-gray-800 font-medium"><?= htmlspecialchars($feedback['fname'] . " " . $feedback['lname']) ?></span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600"><?= htmlspecialchars($feedback['feedback']) ?></td>
                            <td class="p-4 text-gray-500">
                                <i class="far fa-clock mr-2"></i><?= htmlspecialchars($feedback['created_at']) ?>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="3" class="text-center p-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No feedbacks available</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Computer Management -->
    <div class="mt-8 p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">
            <i class="fas fa-desktop mr-2 text-blue-600"></i>Computer Management
        </h2>
        <p class="mb-4 text-gray-600">This tool will standardize all laboratory computers, creating 30 computers (PC-1 to PC-30) in each room.</p>
        <div class="flex items-center">
            <a href="update_computers.php" onclick="return confirm('This will delete all existing computer records and recreate them with standardized naming. Continue?')" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center">
                <i class="fas fa-sync-alt mr-2"></i>Standardize All Computers
            </a>
            <span class="ml-3 text-sm text-gray-600">
                <i class="fas fa-exclamation-triangle mr-1 text-yellow-500"></i>Use with caution - this will reset all computer data
            </span>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("sitinChart").getContext("2d");
    let chart;

    // Datasets from PHP
    const dataPurpose = {
        labels: <?= json_encode($sitinPurposes) ?>,
        datasets: [{
            data: <?= json_encode($sitinCounts) ?>,
            backgroundColor: generateColors(<?= count($sitinPurposes) ?>),
            borderWidth: 1
        }]
    };

    const dataLaboratory = {
        labels: <?= json_encode($sitinLabs) ?>,
        datasets: [{
            data: <?= json_encode($sitinLabCounts) ?>,
            backgroundColor: generateColors(<?= count($sitinLabs) ?>),
            borderWidth: 1
        }]
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' }
        }
    };

    function renderChart(data) {
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: "doughnut",
            data: data,
            options: options
        });
    }

    // Color Generator for dynamic datasets
    function generateColors(count) {
        const colors = [
            "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40",
            "#FF8A80", "#4DB6AC", "#BA68C8", "#FFD54F", "#90CAF9", "#A1887F"
        ];
        // If more items than colors, loop over
        return Array.from({ length: count }, (_, i) => colors[i % colors.length]);
    }

    // Dropdown change event
    document.getElementById("chartTypeDropdown").addEventListener("change", function () {
        renderChart(this.value === "laboratory" ? dataLaboratory : dataPurpose);
    });

    // Initial render
    renderChart(dataPurpose);

    // Bar Chart for Users
    new Chart(document.getElementById("usersBarChart").getContext("2d"), {
        type: "bar",
        data: {
            labels: ["Admins", "Students"],
            datasets: [{
                label: "User Count",
                data: [<?= $totalAdmins ?>, <?= $totalStudents ?>],
                backgroundColor: ["#4CAF50", "#FF9800"],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
