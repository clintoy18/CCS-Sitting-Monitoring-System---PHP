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

    <!-- Leaderboard Section -->
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 p-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-trophy text-yellow-400 mr-3 animate-pulse"></i>
                Top 5 Students
            </h2>
            <p class="text-black mt-2 font-medium tracking-wide flex items-center">
                <i class="fas fa-chart-line text-blue-300 mr-2"></i>
                Best performing students based on 
                <span class="text-yellow-600 mx-1">points</span> 
                and 
                <span class="text-green-00 mx-1">sessions</span>
            </p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-5 gap-6">
                <?php
                // Calculate combined score: (points * 2) - sessions
                $leaderboardQuery = "SELECT idno, fname, lname, points, session, profile_picture,
                                   (points * 2) - session as combined_score
                                   FROM studentinfo 
                                   ORDER BY combined_score DESC, points DESC, session ASC 
                                   LIMIT 5";
                $leaderboardResult = $conn->query($leaderboardQuery);
                
                if ($leaderboardResult->num_rows > 0) {
                    $rank = 1;
                    while ($student = $leaderboardResult->fetch_assoc()) {
                        $medalClass = match($rank) {
                            1 => 'text-yellow-500 animate-bounce',
                            2 => 'text-gray-400',
                            3 => 'text-amber-600',
                            default => 'text-gray-300'
                        };
                        $rankClass = match($rank) {
                            1 => 'bg-gradient-to-br from-yellow-400/10 via-yellow-500/10 to-amber-500/10 border-yellow-400/20',
                            2 => 'bg-gradient-to-br from-gray-300/10 via-gray-400/10 to-gray-500/10 border-gray-300/20',
                            3 => 'bg-gradient-to-br from-amber-500/10 via-amber-600/10 to-amber-700/10 border-amber-500/20',
                            default => 'bg-gradient-to-br from-gray-100/10 via-gray-200/10 to-gray-300/10 border-gray-200/20'
                        };
                        ?>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative flex flex-col h-full p-5 bg-white/80 backdrop-blur-sm rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 <?= $rankClass ?> border backdrop-blur-sm">
                                <!-- Rank Badge -->
                                <div class="absolute -top-4 -right-4 w-12 h-12 flex items-center justify-center">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white rounded-full blur-sm opacity-50"></div>
                                        <i class="fas fa-medal <?= $medalClass ?> text-3xl drop-shadow-lg relative z-10"></i>
                                        <?php if ($rank <= 3): ?>
                                            <span class="absolute -top-2 -right-2 bg-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold text-gray-800 shadow-md">
                                                <?= $rank ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Profile Picture -->
                                <div class="flex justify-center mb-4">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full blur-md opacity-50"></div>
                                        <?php if (!empty($student['profile_picture'])): ?>
                                            <img src="<?= htmlspecialchars($student['profile_picture']) ?>" 
                                                 alt="Profile" 
                                                 class="relative w-20 h-20 rounded-full object-cover border-2 border-white shadow-lg">
                                        <?php else: ?>
                                            <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-lg border-2 border-white">
                                                <i class="fas fa-user text-gray-400 text-3xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Student Info -->
                                <div class="text-center mb-4">
                                    <h3 class="font-bold text-base truncate text-gray-900">
                                        <?= htmlspecialchars($student['fname'] . ' ' . $student['lname']) ?>
                                    </h3>
                                    <p class="text-xs font-semibold text-gray-700 mt-1">
                                        ID: <?= htmlspecialchars($student['idno']) ?>
                                    </p>
                                </div>

                                <!-- Stats -->
                                <div class="mt-auto space-y-3">
                                    <div class="px-3 py-2 bg-gradient-to-r from-purple-500/10 to-purple-600/10 backdrop-blur-sm rounded-xl text-xs font-bold shadow-sm flex items-center justify-center border border-purple-200/30">
                                        <i class="fas fa-calculator text-purple-600 mr-2"></i>
                                        <span class="text-gray-900">Score: <?= htmlspecialchars($student['combined_score']) ?></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="px-3 py-2 bg-gradient-to-r from-green-500/10 to-green-600/10 backdrop-blur-sm rounded-xl text-xs font-bold shadow-sm flex items-center justify-center border border-green-200/30">
                                            <i class="fas fa-clock text-green-600 mr-2"></i>
                                            <span class="text-gray-900"><?= htmlspecialchars($student['session']) ?></span>
                                        </div>
                                        <div class="px-3 py-2 bg-gradient-to-r from-blue-500/10 to-blue-600/10 backdrop-blur-sm rounded-xl text-xs font-bold shadow-sm flex items-center justify-center border border-blue-200/30">
                                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                                            <span class="text-gray-900"><?= htmlspecialchars($student['points']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $rank++;
                    }
                } else {
                    echo '<div class="col-span-5 text-center text-gray-500 py-8">
                            <i class="fas fa-trophy text-4xl text-gray-300 mb-3"></i>
                            <p class="text-base font-medium">No students found</p>
                          </div>';
                }
                ?>
            </div>
        </div>
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
    height: 6px;
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
