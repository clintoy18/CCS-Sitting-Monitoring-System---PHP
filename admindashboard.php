<?php
session_start();
include "adminlayout.php";
include "adminauth.php";
include "auth.php";
include "connection.php"; // Database connection

// Fetch total users.
$userQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM studentinfo");
$userData = mysqli_fetch_assoc($userQuery);
$totalUsers = $userData['total_users'];

// Fetch student count per year level
$yearQuery = mysqli_query($conn, "SELECT year_level, COUNT(*) AS count FROM studentinfo GROUP BY year_level");

$yearLevels = [];
$studentCounts = [];
$colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50"]; // Different colors

while ($row = mysqli_fetch_assoc($yearQuery)) {
    $yearLevels[] = "Year " . $row['year_level']; // Example: "Year 1", "Year 2"
    $studentCounts[] = $row['count'];
}
?>

<!-- Main Content -->
<div class="flex-1 p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="space-y-6">
     
        <div class="grid grid-cols-1 gap-6">
            <!-- Total Users -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 rounded-lg shadow-md text-white">
                <h2 class="text-xl font-semibold">Total Users </h2>
                <p class="text-5xl font-bold"><?php echo $totalUsers ?></p>
            </div>
        </div>

       
        <div class="bg-white p-6 rounded-lg shadow-md max-h-full">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">📊 Reports Overview</h2>
            <canvas id="reportsChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">📢 Announcements</h2>

      
        <form action="post_announcement.php" method="POST" class="mb-4">
            <textarea name="announcement" class="w-full p-3 border rounded-md" placeholder="Type an announcement..." required></textarea>
            <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">📢 Post</button>
        </form>

        <div class="max-h-full overflow-y-auto pr-2">
            <ul class="space-y-4">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<li class="p-4 bg-gray-50 border-l-4 border-blue-600 flex justify-between items-center">';
                    echo '<div>';
                    echo '<p class="font-semibold text-blue-700">📢 Announcement</p>';
                    echo '<p class="text-gray-800">' . htmlspecialchars($row['content']) . '</p>';
                    echo '<p class="text-sm text-gray-400">Posted on ' . date('F d, Y', strtotime($row['created_at'])) . '</p>';
                    echo '</div>';
                    echo '<form action="delete_announcement.php" method="POST">';
                    echo '<input type="hidden" name="delete_id" value="' . $row["announcement_id"] . '">';
                    echo '<button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">🗑 Delete</button>';
                    echo '</form>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<!-- Student Year-Level Bar Chart -->
<div class="mt-6 p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">📊 Students per Year Level</h2>
    <canvas id="yearLevelChart"></canvas>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Reports Chart (Pie)
    const ctxReports = document.getElementById("reportsChart").getContext("2d");
    new Chart(ctxReports, {
        type: "pie",
        data: {
            labels: ["Admins", "Regular Users", "Guests"],
            datasets: [{
                label: "Users",
                data: [10, 50, 40], // Static values
                backgroundColor: ["#4CAF50", "#FF9800", "#03A9F4"],
            }]
        }
    });

    // Year Level Bar Chart
    const ctxYearLevel = document.getElementById("yearLevelChart").getContext("2d");
    new Chart(ctxYearLevel, {
        type: "bar",
        data: {
            labels: <?php echo json_encode($yearLevels); ?>, // Year Level Labels
            datasets: [{
                label: "Number of Students",
                data: <?php echo json_encode($studentCounts); ?>, // Student Count Data
                backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($yearLevels))); ?>, // Assign different colors
                borderColor: <?php echo json_encode(array_slice($colors, 0, count($yearLevels))); ?>,
                borderWidth: 1,
                barThickness: 50 // Adjust the width of bars
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top"
                }
            }
        }
    });
});
</script>
