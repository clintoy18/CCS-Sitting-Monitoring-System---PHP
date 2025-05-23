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

</body>
</html>