<?php

session_start();
include "layout.php";
include "auth.php";
?>

<div class="px-4 py-8 grid xs:grid-cols-1 ml-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 items-stretch animate-fade-in">

 <!-- Student Information Card -->
 <div class="rounded-lg border bg-white  border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
<h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
Student Information</h1>
          

        <div class="flex justify-center mb-6 mt-4">
            <?php if (!empty($userData['profile_picture'])):  ?>
                <img src="<?php echo $userData['profile_picture']; ?>" 
                    alt="Profile Picture" 
                    class="w-40 h-40 rounded-full border-4 border-blue-500 object-cover transition-all duration-300 hover:scale-110 hover:ring-4 hover:ring-blue-400 aspect-square shadow-md">
            <?php else: ?>
                <div class="w-40 h-40 flex items-center justify-center rounded-full border-4 border-gray-300 bg-gray-100 aspect-square shadow-md">
                    <span class="text-gray-500">No Image</span>
                </div>
            <?php endif; ?>
        </div>


                <div class="space-y-4 text-lg font-semibold text-gray-800 dark:text-gray-300 flex-1 ml-2">
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">ID No:</b></p>
                <p class="ml-2"><?php echo $userData['idno']; ?></p>
            </div>
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">Name:</b></p>
                <p class="ml-2"><?php echo $userData['fname'] . " " . $userData['lname']; ?></p>
            </div>
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">Course:</b></p>
                <p class="ml-2"><?php echo $userData['course']; ?></p>
            </div>
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">Year:</b></p>
                <p class="ml-2"><?php echo $userData['year_level']; ?></p>
            </div>
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">Address:</b></p>
                <p class="ml-2"><?php echo $userData['address']; ?></p>
            </div>
            <div class="flex">
                <p class="w-24"><b class="text-black dark:white">Session:</b></p>
                <p class="ml-2"><?php echo $userData['session']; ?></p>
            </div>
        </div>

            </div>


<!-- Announcement Card -->
<div class="rounded-lg border bg-white border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
<h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
    Announcement
</h1>
<div id="dropdown-content" class="w-full bg-white shadow-lg rounded-lg border border-gray-300 p-8 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white max-h-130 overflow-y-auto transition-all duration-300 ease-in-out">
<?php
    // Fetch announcements from the database
    include "connection.php";
    $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='p-4 bg-gray-100 border-l-4 border-blue-600 '>
                <h3 class='font-bold text-gray-600 p-2 '>📢 CCS-ADMIN</h3>
                <p class='font-semibold pt-2'>" . (!empty($row['content']) ? htmlspecialchars($row['content']) : "No announcement available") . "</p>
                <p class='text-sm text-gray-400 pt-2'>Posted on " . date('F d, Y', strtotime($row['created_at'])) . "</p>
              </div>";
    }
      ?>
    </div>
</div>



<!-- Announcement Card -->
<div class="rounded-lg border bg-white border-gray-300 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full animate-slide-up [animation-delay:0.6s]">
<h1 class="text-center bg-blue-900 text-white text-xl font-extrabold py-6 px-8 rounded-sm shadow-lg">
    Rules and Regulations
</h1>
    <!-- Dropdown Content (Scroll and hide excess content) -->
    <div id="dropdown-content" class="w-full bg-white shadow-lg rounded-lg border border-gray-300 p-8 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white max-h-130 overflow-y-auto transition-all duration-300 ease-in-out">
    
    <h1 class="text-center text-2xl font-extrabold pb-4 text-gray-800 dark:text-white">University Of Cebu</h1>
          <h5 class="text-center text-md font-extrabold pb-4 text-gray-800 dark:text-white">COLLEGE OF INFORMATION & COMPUTER STUDIES</h5>
   
        <p><b>LABORATORY RULES AND REGULATIONS</b></p>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            
            <ul class="list-disc pl-5">
                <li>Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans and other personal pieces of equipment must be switched off.</li>
                <li>Games are not allowed inside the lab. This includes computer-related games, card games, and other games that may disturb the operation of the lab.</li>
                <li>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</li>
                <li>Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                <li>Deleting computer files and changing the set-up of the computer is a major offense.</li>
                <li>Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</li>
                <li>Observe proper decorum while inside the laboratory.</li>
                <li>Do not get inside the lab unless the instructor is present. All bags, knapsacks, and the likes must be deposited at the counter. Follow the seating arrangement of your instructor. At the end of class, all software programs must be closed. Return all chairs to their proper places after using.</li>
                <li>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</li>
                <li>Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                <li>Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                <li>For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                <li>Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</li>
            </ul>

            <p><b>DISCIPLINARY ACTION</b></p>
            <ul class="list-disc pl-5">
                <li>First Offense - The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
                <li>Second and Subsequent Offenses - A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
            </ul>

    </div>
</div>



