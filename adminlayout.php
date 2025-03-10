<?php
include "connection.php";



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
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
  <a href="admindashboard.php" class="flex items-center space-x-3 rtl:space-x-reverse">
  <img src="ccslogo.png" alt="" class="h-14">
      <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-black">CCS SIT-IN Monitoring</span>
  </a>
  <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
    

  <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
    <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-4 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
    <?php ?>
    <li>
    <a href="admindashboard.php" id="navSearch" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
        Search
    </a>
    </li>
    <li>
    <a href="#" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Student List</a>
    </li>
      <li>
        <a href="#" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Announcement</a>
      </li>
      <li>
        <a href="currentsitin.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Current Sit-In Record</a>
      </li>
      <li>
        <a href="login.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Signout</a>
      </li>
    </ul>
  </div>
  </div>
</nav> 


<div>
  
<div id="searchModal" tabindex="-1" aria-hidden="true" 
    class="fixed inset-0 z-50 hidden flex justify-center items-center bg-gray-900 bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
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


