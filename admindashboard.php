<?php
session_start();
include "adminlayout.php";
include "adminauth.php";
include "connection.php";

$totalAdmins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_admins FROM admins"))['total_admins'];
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM studentinfo"))['total_students'];
$totalUsers = $totalAdmins + $totalStudents;

$yearData = mysqli_query($conn, "SELECT year_level, COUNT(*) AS count FROM studentinfo GROUP BY year_level");
$yearLevels = $studentCounts = [];
while ($row = mysqli_fetch_assoc($yearData)) {
    $yearLevels[] = "Year " . $row['year_level'];
    $studentCounts[] = $row['count'];
}

$sitinData = mysqli_query($conn, "SELECT sitin_purpose, COUNT(*) AS count FROM sit_in_records GROUP BY sitin_purpose");
$sitinPurposes = $sitinCounts = [];
while ($row = mysqli_fetch_assoc($sitinData)) {
    $sitinPurposes[] = ucfirst($row['sitin_purpose']);
    $sitinCounts[] = $row['count'];
}
?>

<div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <?php foreach ([
            ['Total Admins', $totalAdmins, 'fas fa-user-shield', 'text-blue-600'],
            ['Total Students', $totalStudents, 'fas fa-user-graduate', 'text-green-600'],
            ['Total Users', $totalUsers, 'fas fa-users', 'text-purple-600']
        ] as [$title, $count, $icon, $color]): ?>
        <div class="p-6 bg-white rounded-lg shadow-lg flex items-center">
            <div class="<?= $color ?> text-4xl mr-4"><i class="<?= $icon ?>"></i></div>
            <div>
                <h2 class="text-lg font-semibold"><?= $title ?></h2>
                <p class="text-3xl font-bold"><?= $count ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="p-6 bg-white rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">📊 Sit-in Records per Purpose</h2>
            <canvas id="sitinPurposeChart" class="w-full" style="height: 250px;"></canvas>
        </div>
        <div class="p-6 bg-white rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">📢 Announcements</h2>
            <form action="post_announcement.php" method="POST" class="mb-4">
                <textarea name="announcement" class="w-full p-3 border rounded-md mb-4" placeholder="Type an announcement..." required></textarea>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Post</button>
            </form>
            <div class="max-h-[300px] overflow-y-auto pr-2">
                <ul class="space-y-4">
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li class="p-4 bg-gray-50 border-l-4 border-blue-600 rounded-md flex justify-between items-center">';
                        echo '<div><p class="font-semibold text-blue-700">📢 Announcement</p>';
                        echo '<p>' . htmlspecialchars($row['content']) . '</p>';
                        echo '<p class="text-sm text-gray-400">Posted on ' . date('F d, Y h:i A', strtotime($row['created_at'])) . '</p></div>';
                        echo '<form action="delete_announcement.php" method="POST">';
                        echo '<input type="hidden" name="delete_id" value="' . $row["announcement_id"] . '">';
                        echo '<button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">🗑</button>';
                        echo '</form></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">📊 Number of Users</h2>
        <canvas id="usersBarChart" class="w-full" style="height: 250px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    new Chart(document.getElementById("sitinPurposeChart").getContext("2d"), {
        type: "pie",
        data: {
            labels: <?= json_encode($sitinPurposes) ?>,
            datasets: [{ data: <?= json_encode($sitinCounts) ?>, backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"], borderWidth: 1 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    new Chart(document.getElementById("usersBarChart").getContext("2d"), {
        type: "bar",
        data: {
            labels: ["Admins", "Students"],
            datasets: [{ data: [<?= $totalAdmins ?>, <?= $totalStudents ?>], backgroundColor: ["#4CAF50", "#FF9800"], borderWidth: 1 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});
</script>