<?php
session_start();
include "layout.php";
include "auth.php";
?>

<div class="px-4 py-8 grid xs:grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 items-stretch animate-fade-in">

    <!-- Student Information Card -->
    <div class="rounded-lg border bg-white border-gray-300 p-6 shadow-lg 
                transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up">
        <h1 class="text-center text-2xl font-extrabold pb-4 text-gray-800 dark:text-white">Student Information</h1>

        <div class="flex justify-center mb-6">
            <?php if (!empty($userData['profile_picture'])): ?>
                <img src="<?php echo $userData['profile_picture']; ?>" 
                    alt="Profile Picture" 
                    class="w-40 h-40 rounded-full border-4 border-blue-500 object-cover transition-all duration-300 hover:scale-110 hover:ring-4 hover:ring-blue-400 aspect-square shadow-md">
            <?php else: ?>
                <div class="w-40 h-40 flex items-center justify-center rounded-full border-4 border-gray-300 bg-gray-100 aspect-square shadow-md">
                    <span class="text-gray-500">No Image</span>
                </div>
            <?php endif; ?>
        </div>


        <div class="space-y-4 text-lg font-semibold text-gray-800 dark:text-gray-300 flex-1 text-justify">
            <p><b class="text-blue-600 dark:text-blue-400">ID No:</b> <?php echo $userData['idno']; ?></p>
            <p><b class="text-blue-600 dark:text-blue-400">Name:</b> <?php echo $userData['fname'] . " " . $userData['lname']; ?></p>
            <p><b class="text-blue-600 dark:text-blue-400">Course:</b> <?php echo $userData['course']; ?></p>
            <p><b class="text-blue-600 dark:text-blue-400">Year:</b> <?php echo $userData['year_level']; ?></p>
            <p><b class="text-blue-600 dark:text-blue-400">Address:</b> <?php echo $userData['address']; ?></p>
            <p><b class="text-blue-600 dark:text-blue-400">Session:</b> <?php echo $userData['session']; ?></p>
        </div>
    </div>

    <!-- Announcements Card -->
    <div class="rounded-lg border bg-white border-gray-300 p-6 shadow-lg 
                transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.3s]">
        <h1 class="text-center text-2xl font-extrabold pb-4 text-gray-800 dark:text-white">Announcements</h1>
        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 flex-1 leading-relaxed text-justify">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sed justo in lacus accumsan molestie. <br><br>
            Suspendisse potenti. Curabitur auctor, turpis ut vehicula eleifend, risus nisl scelerisque arcu, 
            in pharetra risus libero in est. Aliquam erat volutpat. Donec scelerisque lectus quis sapien egestas, 
            a maximus augue tempor.
            Nulla facilisi. Aenean eget justo vel leo egestas bibendum a et elit. Vivamus eget libero et neque 
            dictum feugiat eget at lacus. 
            Proin rhoncus, velit sit amet vehicula tincidunt, orci velit tincidunt lectus, a tempus turpis erat 
            at libero. Suspendisse quis leo at dolor sollicitudin efficitur.
        </p>
    </div>

    <!-- Rules & Regulations Card -->
    <div class="rounded-lg border bg-white border-gray-300 p-6 shadow-lg 
                transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
        <h1 class="text-center text-2xl font-extrabold pb-4 text-gray-800 dark:text-white">Rules & Regulations</h1>
        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 flex-1 leading-relaxed text-justify">
            Nulla facilisi. Aenean eget justo vel leo egestas bibendum a et elit. Vivamus eget libero et neque 
            dictum feugiat eget at lacus. 
            Proin rhoncus, velit sit amet vehicula tincidunt, orci velit tincidunt lectus, a tempus turpis erat 
            at libero. Suspendisse quis leo at dolor sollicitudin efficitur.
        </p>
    </div>

</div>
