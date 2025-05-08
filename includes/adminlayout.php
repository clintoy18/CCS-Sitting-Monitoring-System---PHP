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
        <i class="fas fa-calendar-check mr-2"></i>Student Reservations
      </a>
      <a href="lab_schedules.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-calendar mr-2"></i>Lab Schedule
      </a>
      <a href="lab_resources.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-book mr-2"></i>Lab Resources
      </a>
      <a href="currentsitin.php" class="flex items-center text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500 whitespace-nowrap">
        <i class="fas fa-desktop mr-2"></i>Current Sit-In Record
      </a>
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

</script>


