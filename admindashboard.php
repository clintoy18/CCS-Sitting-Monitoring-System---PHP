<?php
session_start();
include "adminlayout.php";
include "adminauth.php";
?>
<!-- Main Content (Body) -->
<div class="flex-1 p-6">

    <!-- Header -->
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800">Welcome,<?php echo $adminData['name'] ?><h1>
            <p class="text-sm text-gray-600">Your admin dashboard overview</p>
        </div>

    </header>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1: Total Users -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800">Total Users</h2>
            <p class="text-4xl font-bold text-blue-600">120</p>
        </div>

        <!-- Card 2: Total Reports -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800">Total Reports</h2>
            <p class="text-4xl font-bold text-blue-600">45</p>
        </div>

        <!-- Card 3: Pending Requests -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800">Pending Requests</h2>
            <p class="text-4xl font-bold text-red-600">10</p>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Activities</h2>
        <ul class="space-y-4">
            <li class="flex justify-between items-center">
                <div class="text-gray-600">
                    <p class="font-semibold">John Doe</p>
                    <p class="text-sm">Updated profile</p>
                </div>
                <p class="text-sm text-gray-400">3 hours ago</p>
            </li>
            <li class="flex justify-between items-center">
                <div class="text-gray-600">
                    <p class="font-semibold">Jane Smith</p>
                    <p class="text-sm">Requested a report</p>
                </div>
                <p class="text-sm text-gray-400">5 hours ago</p>
            </li>
            <li class="flex justify-between items-center">
                <div class="text-gray-600">
                    <p class="font-semibold">Bob Johnson</p>
                    <p class="text-sm">Logged in</p>
                </div>
                <p class="text-sm text-gray-400">8 hours ago</p>
            </li>
        </ul>
    </div>

</div>
