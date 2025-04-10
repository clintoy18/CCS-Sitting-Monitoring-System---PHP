<?php
session_start();
include "adminlayout.php";
include "adminauth.php";
include "connection.php";

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
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <?php foreach ([
            ['Total Admins', $totalAdmins, 'fas fa-user-shield', 'text-blue-600'],
            ['Total Students', $totalStudents, 'fas fa-user-graduate', 'text-green-600'],
            ['Total Users', $totalUsers, 'fas fa-users', 'text-purple-600']
        ] as [$title, $count, $icon, $color]): ?>
        <div class="p-6 bg-white rounded-lg shadow-lg flex items-center">
            <div class="<?= $color ?> text-4xl mr-4"><i class="<?= $icon ?>"></i></div>
            <div>
                <h2 class="text-lg font-semibold"><?= htmlspecialchars($title) ?></h2>
                <p class="text-3xl font-bold"><?= htmlspecialchars($count) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts & Announcements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Sit-in Chart -->
        <div class="p-6 bg-white rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4"> Sit-in Records</h2>
            <select id="chartTypeDropdown" class="mb-4 p-2 border rounded-md">
                <option value="purpose">Per Purpose</option>
                <option value="laboratory">Per Laboratory</option>
            </select>
            <div class="h-[300px]">
                <canvas id="sitinChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Announcements -->
        <div class="p-6 bg-white rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4"> Announcements</h2>
            <form action="post_announcement.php" method="POST" class="mb-4">
                <textarea name="announcement" class="w-full p-3 border rounded-md mb-4" placeholder="Type an announcement..." required></textarea>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Post</button>
            </form>
            <div class="max-h-[300px] overflow-y-auto pr-2">
                <ul class="space-y-4">
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                    if ($result):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <li class="p-4 bg-gray-50 border-l-4 border-blue-600 rounded-md flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-blue-700">Announcement</p>
                            <p><?= htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-sm text-gray-400">
                                Posted on <?= date('F d, Y h:i A', strtotime($row['created_at'])) ?>
                            </p>
                        </div>
                        <form action="delete_announcement.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                            <input type="hidden" name="delete_id" value="<?= $row["announcement_id"] ?>">
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">🗑</button>
                        </form>
                    </li>
                    <?php endwhile; endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4"> Number of Users</h2>
        <div class="h-[300px]">
            <canvas id="usersBarChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>
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
