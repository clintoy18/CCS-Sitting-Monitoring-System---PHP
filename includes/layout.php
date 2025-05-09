<?php
include "connection.php";
include "auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>  
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.5.2/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <script src="./assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="./assets/vendor/datatables.net/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.2/dist/flowbite.min.js"></script>
    <!-- Add Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
      .dt-layout-row:has(.dt-search),
      .dt-layout-row:has(.dt-length),
      .dt-layout-row:has(.dt-paging) {
       display: none !important;
      }
    </style>
</head>
<body class="bg-[#D4D9E3]">   
<nav class="bg-white border-gray-200 dark:bg-gray-900">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <a href="dashboard.php" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img src="../assets/uploads/ccslogo.png" alt="" class="h-14">
      <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-black">CCS SIT-IN Monitoring</span>
    </a>
    
    <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
      <button type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
        <span class="sr-only">Open user menu</span>
        <?php 
          // Corrected profile picture path
          $profilePicture = !empty($userData['profile_picture']) ? "../uploads/" . $userData['profile_picture'] : '../uploads/default.jpg'; 
        ?>
        <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="rounded-full w-8 h-8">
      </button>

      <!-- Dropdown menu -->
      <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
        <div class="px-4 py-3">
          <span class="block text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($userData['fname'] . " " . $userData['lname']); ?></span>
          <span class="block text-sm text-gray-500 truncate dark:text-gray-400"></span>
        </div>
        <ul class="py-2" aria-labelledby="user-menu-button">
          <li>
            <a href="editprofile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
              <i class="fas fa-cog mr-2"></i>Settings
            </a>
          </li>
          <li>
            <a href="../auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
              <i class="fas fa-sign-out-alt mr-2"></i>Sign out
            </a>
          </li>
        </ul>
      </div>
      
      <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-user" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>
    </div>

    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
      <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-6 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
        <li>
          <a href="dashboard.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 flex items-center">
            <i class="fas fa-home mr-2"></i>Home
          </a>
        </li>
        <li>
          <a href="reservation.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 flex items-center">
            <i class="fas fa-calendar-check mr-2"></i>Reservations
          </a>
        </li>
        <li>
          <a href="history.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 flex items-center">
            <i class="fas fa-history mr-2"></i>History
          </a>
        </li>
        <li>
          <a href="lab_resources.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 flex items-center">
            <i class="fas fa-book mr-2"></i>Resources
          </a>
        </li>
        <!-- Notification Bell -->
        <li class="relative">
          <button id="notificationBell" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
            <i class="fas fa-bell text-xl"></i>
            <?php
            // Get count of approved reservations
            if (isset($_SESSION["idno"])) {
                $userID = $_SESSION["idno"];
                $approved_query = "SELECT COUNT(*) as count FROM reservations WHERE idno = ? AND status = 'approved' || status = 'disapproved'";
                $stmt = $conn->prepare($approved_query);
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $approved_count = $stmt->get_result()->fetch_assoc()['count'];
                if ($approved_count > 0) {
                    echo '<span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">' . $approved_count . '</span>';
                }
            }
            ?>
          </button>
          
          <!-- Notification Dropdown -->
          <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Reservation Status</h3>
            </div>
            <div id="notificationList" class="max-h-96 overflow-y-auto">
              <?php
              if (isset($_SESSION["idno"])) {
                  // Get approved reservations
                  $recent_query = "SELECT r.*, c.computer_name, rm.room_name 
                                 FROM reservations r 
                                 JOIN computers c ON r.computer_id = c.computer_id 
                                 JOIN rooms rm ON r.room_id = rm.room_id 
                                 WHERE r.idno = ? AND r.status = 'approved' OR r.status = 'disapproved'
                                 ORDER BY r.start_time DESC 
                                 LIMIT 5";
                  $stmt = $conn->prepare($recent_query);
                  $stmt->bind_param("i", $userID);
                  $stmt->execute();
                  $recent_result = $stmt->get_result();
                  
                  if ($recent_result->num_rows > 0) {
                      while ($row = $recent_result->fetch_assoc()) {
                        if($row['status'] == 'approved'){
                          echo '<div class="p-4 hover:bg-gray-50 border-b border-gray-200">
                                  <div class="flex items-start">
                                      <div class="flex-1">
                                          <p class="text-sm text-gray-800">
                                              <span class="font-semibold text-green-600">Reservation Approved!</span><br>
                                              PC ' . htmlspecialchars($row['computer_name']) . ' in Lab ' . htmlspecialchars($row['room_name']) . '
                                          </p>
                                          <p class="text-xs text-gray-500 mt-1">' . date('M d, Y h:i A', strtotime($row['start_time'])) . '</p>
                                      </div>
                                  </div>
                                </div>';
                        }else{
                        echo '<div class="p-4 hover:bg-gray-50 border-b border-gray-200">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">
                                    <span class="font-semibold text-red-600">Reservation Disapproved!</span><br>
                                    PC ' . htmlspecialchars($row['computer_name']) . ' in Lab ' . htmlspecialchars($row['room_name']) . '
                                </p>
                                <p class="text-xs text-gray-500 mt-1">' . date('M d, Y h:i A', strtotime($row['start_time'])) . '</p>
                            </div>
                        </div>
                      </div>';
                        }
                      }
                  } else {
                      echo '<div class="p-4 text-center text-gray-500">No approved reservations</div>';
                  }
              }
              ?>
            </div>
            <div class="p-4 border-t border-gray-200">
              <a href="reservation.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Reservations</a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>   

<!-- Page-specific content will be inserted here -->
<div>
    <?php
    // Include the page content from the individual pages here
    if (isset($content)) {
        echo $content;
    }
    ?>
</div>

<script>
// Toggle notification dropdown
document.getElementById('notificationBell').addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.getElementById('notificationBell');
    
    if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Refresh notifications every 30 seconds
setInterval(function() {
    fetch('get_student_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            const notificationCount = document.querySelector('#notificationBell span');
            
            if (data.reservations.length > 0) {
                if (notificationCount) {
                    notificationCount.textContent = data.reservations.length;
                } else {
                    const countSpan = document.createElement('span');
                    countSpan.className = 'absolute -top-2 -right-2 bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center';
                    countSpan.textContent = data.reservations.length;
                    document.getElementById('notificationBell').appendChild(countSpan);
                }
                
                notificationList.innerHTML = data.reservations.map(reservation => `
                    <div class="p-4 hover:bg-gray-50 border-b border-gray-200">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">
                                    <span class="font-semibold text-green-600">Reservation Approved!</span><br>
                                    PC ${reservation.computer_name} in Lab ${reservation.room_name}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">${reservation.start_time}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                if (notificationCount) {
                    notificationCount.remove();
                }
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">No approved reservations</div>';
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}, 30000);
</script>

</body>
</html>