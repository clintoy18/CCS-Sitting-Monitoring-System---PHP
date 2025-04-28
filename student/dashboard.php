<?php
include "../includes/auth.php";  // Ensure the user is authenticated
include "../includes/layout.php"; // Include the layout for consistent UI
include "../includes/connection.php"; // Include the database connection
?>

<div class="px-4 py-6 grid xs:grid-cols-1 ml-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 items-stretch animate-fade-in">

    <!-- Student Information Card -->
    <div class="rounded-lg border bg-white border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
        <h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
            Student Information
        </h1>

        <div class="flex justify-center my-6">
            <?php if (!empty($userData['profile_picture'])): ?>
                <img src="<?php echo htmlspecialchars($userData['profile_picture']); ?>" 
                     alt="Profile Picture" 
                     class="w-40 h-40 rounded-full border-4 border-blue-500 object-cover transition-all duration-300 hover:scale-110 hover:ring-4 hover:ring-blue-400 aspect-square shadow-md">
            <?php else: ?>
                <div class="w-40 h-40 flex items-center justify-center rounded-full border-4 border-gray-300 bg-gray-100 aspect-square shadow-md">
                    <span class="text-gray-500 font-medium">No Image</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6 text-lg font-semibold text-gray-800 dark:text-gray-300 flex-1 px-8 pb-6">
            <div class="flex border-b border-gray-200 pb-2">
                <p class="w-28 font-bold text-black dark:text-white">ID No:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['idno']); ?></p>
            </div>
            <div class="flex border-b border-gray-200 pb-2">
                <p class="w-28 font-bold text-black dark:text-white">Name:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['fname'] . " " . $userData['lname']); ?></p>
            </div>
            <div class="flex border-b border-gray-200 pb-2">
                <p class="w-28 font-bold text-black dark:text-white">Course:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['course']); ?></p>
            </div>
            <div class="flex border-b border-gray-200 pb-2">
                <p class="w-28 font-bold text-black dark:text-white">Year:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['year_level']); ?></p>
            </div>
            <div class="flex border-b border-gray-200 pb-2">
                <p class="w-28 font-bold text-black dark:text-white">Address:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['address']); ?></p>
            </div>
            <div class="flex">
                <p class="w-28 font-bold text-black dark:text-white">Session:</p>
                <p class="ml-4"><?php echo htmlspecialchars($userData['session']); ?></p>
            </div>
        </div>
    </div>

    <!-- Announcement Card -->
    <div class="rounded-lg border bg-white border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
        <h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
            Announcement
        </h1>
        <div id="dropdown-content" class="w-full bg-white shadow-lg rounded-lg border border-gray-300 p-8 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white max-h-[500px] overflow-y-auto transition-all duration-300 ease-in-out">
            <?php
            // Fetch announcements from the database
            $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='p-5 mb-5 bg-gray-100 rounded-lg border-l-4 border-blue-600'>
                        <h3 class='font-bold text-gray-600 pb-3 text-lg'>📢 CCS-ADMIN</h3>
                        <p class='font-medium text-gray-800 pb-3 leading-relaxed'>" . (!empty($row['content']) ? htmlspecialchars($row['content']) : "No announcement available") . "</p>
                        <p class='text-sm text-gray-500 pt-2'>Posted on " . date('F d, Y', strtotime($row['created_at'])) . "</p>
                      </div>";
            }
            ?>
        </div>
    </div>

    <!-- Rules and Regulations Card -->
    <div class="rounded-lg border bg-white border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
        <h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
            Rules and Regulations
        </h1>
        <div id="dropdown-content" class="w-full bg-white shadow-lg rounded-lg border border-gray-300 p-8 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white max-h-[500px] overflow-y-auto transition-all duration-300 ease-in-out">
            <h1 class="text-center text-xl font-extrabold pb-3 text-gray-800 dark:text-white">University Of Cebu</h1>
            <h5 class="text-center text-md font-extrabold pb-5 text-gray-800 dark:text-white">COLLEGE OF INFORMATION & COMPUTER STUDIES</h5>
            <p class="font-bold text-lg mb-2 text-gray-800">LABORATORY RULES AND REGULATIONS</p>
            <p class="mb-4 text-base leading-relaxed">To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <ul class="list-disc pl-6 space-y-3 text-base">
                <li>Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                <li>Games are not allowed inside the lab.</li>
                <li>Surfing the Internet is allowed only with the permission of the instructor.</li>
                <li>Deleting computer files and changing the set-up of the computer is a major offense.</li>
                <li>Observe computer time usage carefully.</li>
                <li>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .animate-slide-up {
        animation: slideUp 0.5s ease-in-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
