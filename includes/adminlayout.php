<?php
include "connection.php";
include "adminauth.php"; // Ensure authentication is checked first

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
      .dt-layout-row:has(.dt-search),
      .dt-layout-row:has(.dt-length),
      .dt-layout-row:has(.dt-paging) {
       display: none !important;
}

    </style>
</head>
<body class ="bg-[#D4D9E3] ">   
<nav class="bg-white border-gray-200 dark:bg-gray-900">
  <div class="max-w-screen-xl mx-auto p-4 flex flex-wrap justify-between items-center">
    <!-- Logo Section -->
    <a href="admindashboard.php" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img src="/assets/images/ccslogo.png" alt="CCS Logo" class="h-14">
      <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-black">CCS SIT-IN Monitoring</span>
    </a>

    <!-- Desktop Nav Links -->
    <div class="hidden md:flex flex-wrap items-center gap-x-4 gap-y-2 font-medium text-sm md:text-base" id="navbar-user">
      <a href="admindashboard.php" id="navSearch" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-search mr-2"></i>Search
      </a>
      <a href="student_list.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-users mr-2"></i>Student List
      </a>
      <a href="student_reservations.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-calendar-check mr-2"></i>Reservations
      </a>
      <a href="lab_schedules.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-calendar mr-2"></i>Lab Schedule
      </a>
      <a href="lab_resources.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-book mr-2"></i>Resources
      </a>
      <a href="pc_management.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-calendar-check mr-2"></i>PC Management
      </a>
      <a href="currentsitin.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-desktop mr-2"></i>Manage Sit-in
      </a>
      <!-- Notification Bell -->
      <div class="relative">
        <button id="notificationBell" class="text-gray-600 hover:text-blue-600 focus:outline-none">
          <i class="fas fa-bell text-xl"></i>
          <?php
          // Get count of pending reservations
          $pending_query = "SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'";
          $pending_result = $conn->query($pending_query);
          $pending_count = $pending_result->fetch_assoc()['count'];
          if ($pending_count > 0) {
              echo '<span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">' . $pending_count . '</span>';
          }
          ?>
        </button>
        
        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Reservations</h3>
          </div>
          <div id="notificationList" class="max-h-96 overflow-y-auto">
            <?php
            // Get recent reservations
            $recent_query = "SELECT r.*, s.fname, s.lname, c.computer_name, rm.room_name 
                           FROM reservations r 
                           JOIN studentinfo s ON r.idno = s.idno 
                           JOIN computers c ON r.computer_id = c.computer_id 
                           JOIN rooms rm ON r.room_id = rm.room_id 
                           WHERE r.status = 'pending' 
                           ORDER BY r.start_time DESC 
                           LIMIT 5";
            $recent_result = $conn->query($recent_query);
            
            if ($recent_result->num_rows > 0) {
                while ($row = $recent_result->fetch_assoc()) {
                    echo '<a href="student_reservations.php" class="block p-4 hover:bg-gray-50 border-b border-gray-200">
                            <div class="flex items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800">
                                        <span class="font-semibold">' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</span>
                                        requested PC ' . htmlspecialchars($row['computer_name']) . ' in Lab ' . htmlspecialchars($row['room_name']) . '
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">' . date('M d, Y h:i A', strtotime($row['start_time'])) . '</p>
                                </div>
                            </div>
                          </a>';
                }
            } else {
                echo '<div class="p-4 text-center text-gray-500">No new reservations</div>';
            }
            ?>
          </div>
          <div class="p-4 border-t border-gray-200">
            <a href="student_reservations.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Reservations</a>
          </div>
        </div>
      </div>
      <a href="../auth/logout.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-sign-out-alt mr-2"></i>Signout
      </a>
    </div>

    <!-- Mobile Menu Button -->
    <div class="md:hidden ml-auto">
      <button type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-user" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
</nav>



<div>
  
<div id='searchModal' class='fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex justify-center items-center'>
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative m-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-semibold">Search Student</h3>
            <button type="button" id="closeSearchModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4">
            <input type="text" id="searchQuery" class="border p-2 w-full rounded" placeholder="Enter Student Name or ID">
            <button id="searchBtn" class="w-full mt-2 px-4 py-2 bg-blue-500 text-white rounded">Search</button>
            <div id="searchResults" class="mt-4"></div>
        </div>
    </div>
</div>





    <?php
    // Include the page content from the individual pages here

    if (isset($content)) {
        echo $content;
        
  
    }
    ?>
</div>



</body>
</html>
<script>
$(document).ready(function() {
    // Show modal when "Search" is clicked
    $("#navSearch").click(function(event) {
        event.preventDefault();
        $("#searchModal").fadeIn();
        return false; // Prevent default anchor behavior
    });

    // Close modal when "×" is clicked
    $("#closeSearchModal").click(function() {
        $("#searchModal").fadeOut();
        $("#searchQuery").val(""); // Clear input field
        $("#searchResults").html(""); // Clear search results (optional)
    });

    // Close modal if clicking outside the modal content
    $("#searchModal").click(function(event) {
        if (!$(event.target).closest(".relative").length) {
            $("#searchModal").fadeOut();
            $("#searchQuery").val(""); // Clear input field
            $("#searchResults").html(""); // Clear search results (optional)
        }
    });

    // Handle search button click
    $("#searchBtn").click(function() {
        var query = $("#searchQuery").val().trim();

        if (query !== "") {
            $.ajax({
                url: "search_student.php", 
                method: "POST",
                data: { search: query },
                success: function(response) {
                    $("#searchResults").html(response);
                }
            });
        } else {
            $("#searchResults").html("<p class='text-red-500'>Please enter a search query.</p>");
        }
    });
});

// Function to load notifications
function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            const notificationCount = document.getElementById('notificationCount');
            
            if (data.notifications.length > 0) {
                notificationCount.textContent = data.notifications.length;
                notificationCount.classList.remove('hidden');
                
                notificationList.innerHTML = data.notifications.map(notification => `
                    <a href="student_reservations.php?id=${notification.reservation_id}" 
                       class="block p-4 hover:bg-gray-50 border-b border-gray-200 ${notification.is_read ? 'bg-gray-50' : 'bg-blue-50'}">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">${notification.message}</p>
                                <p class="text-xs text-gray-500 mt-1">${notification.created_at}</p>
                            </div>
                            ${!notification.is_read ? '<span class="ml-2 h-2 w-2 bg-blue-500 rounded-full"></span>' : ''}
                        </div>
                    </a>
                `).join('');
            } else {
                notificationCount.classList.add('hidden');
                notificationList.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        No new notifications
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Toggle notification dropdown
document.getElementById('notificationBell').addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
    
    if (!dropdown.classList.contains('hidden')) {
        loadNotifications();
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.getElementById('notificationBell');
    
    if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Load notifications every 30 seconds
setInterval(loadNotifications, 30000);

// Initial load
loadNotifications();
</script>


