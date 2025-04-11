<?php
include "../includes/connection.php";  // Database connection

if (isset($_POST['search'])) {
    $search = $_POST['search'];

    // Query to search for students by ID or Name
    $query = "SELECT * FROM studentinfo WHERE idno LIKE ? OR fname LIKE ? OR lname LIKE ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $likeSearch = "%$search%";
    $stmt->bind_param("sss", $likeSearch, $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Floating form container
        echo "<div id='searchModal' class='fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center'>
                <div class='bg-white p-6 shadow-lg rounded-lg w-half md:w-1/3 relative'>
                    <button type='button' id='closeSearchModal' class='absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl'>&times;</button>
                    <h2 class='text-xl font-bold mb-4 text-center'>Sit-in Registration</h2>
                    <form id='searchForm' action='process_sitin.php' method='POST'>";

        while ($row = $result->fetch_assoc()) {
            echo "<div class='mb-4'>
                    <label class='block text-gray-700 font-bold mb-2'>Student ID:</label>
                    <input type='text' name='idno[]' value='{$row['idno']}' class='border p-2 rounded w-full' readonly>
                  </div>";

            echo "<div class='mb-4'>
                    <label class='block text-gray-700 font-bold mb-2'>Name:</label>
                    <input type='text' value='{$row['fname']} {$row['lname']}' class='border p-2 rounded w-full' readonly>
                  </div>";

            echo "<div class='mb-4'>
                    <label class='block text-gray-700 font-bold mb-2'>Course:</label>
                    <input type='text' value='{$row['course']}' class='border p-2 rounded w-full' readonly>
                  </div>";

            echo "<div class='mb-4'>
                  <label class='block text-gray-700 font-bold mb-2'>Sit-in Purpose:</label>
                  <select name='sitin_purpose[]' class='border p-2 rounded w-full' required>
                      <option value='' disabled selected>Select Purpose</option>
                      <option value='C Programming'>C Programming</option>
                      <option value='C# Programming'>C# Programming</option>
                      <option value='Java Programming'>Java Programming</option>
                      <option value='Php Programming'>PHP Programming</option>
                      <option value='Database'>Database</option>
                      <option value='Digital Logic & Design'>Digital Logic & Design</option>
                      <option value='Embedded Systems & IoT'>Embedded Systems & IoT</option>
                      <option value='Python Programming'>Python Programming</option>
                      <option value='Systems Integration and Architecture'>Systems Integration and Architecture</option>
                      <option value='Computer Application'>Computer Application</option>
                      <option value='Web Design and Development'>Web Design and Development</option>
                  </select>
              </div>";
              
            echo "<div class='mb-4'>
                  <label class='block text-gray-700 font-bold mb-2'>Laboratory:</label>
                  <select name='lab[]' class='border p-2 rounded w-full'>
                      <option value='524'>524</option>
                      <option value='526'>526</option>
                      <option value='528'>528</option>
                      <option value='530'>530</option>
                      <option value='542'>542</option>
                      <option value='544'>544</option>
                      <option value='517'>517</option>

                  </select>
                </div>";
            echo "<div class='mb-4'>
                  <label class='block text-gray-700 font-bold mb-2'>Session:</label>
                  <input type='text' value='{$row['session']}' class='border p-2 rounded w-full' readonly>
                </div>";


            echo "<hr class='my-4'>";
            
        }
        

        echo "<div class='text-center'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Submit</button>
              </div>";
        echo "</form></div></div>";
    } else {
        echo "<p class='text-red-500 text-center mt-4'>No students found.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Close modal when "×" is clicked
    $(document).on("click", "#closeSearchModal", function () {
        $("#searchModal").fadeOut(300, function () {
            $(this).remove(); // Remove modal from DOM after fading out
        });

        // Reset the form after closing the modal
        $("#searchQuery").val("");
        $("#searchResults").html("");
    });

    // Close modal if clicking outside the modal content
    $(document).on("click", "#searchModal", function (event) {
        if (!$(event.target).closest(".relative").length) {
            $("#searchModal").fadeOut(300, function () {
                $(this).remove(); // Remove modal from DOM
            });

            // Reset the form after closing the modal
            $("#searchQuery").val("");
            $("#searchResults").html("");
        }
    });

    // Handle search button click
    $(document).on("click", "#searchBtn", function () {
        var query = $("#searchQuery").val().trim();

        if (query !== "") {
            $.ajax({
                url: "search_student.php",
                method: "POST",
                data: { search: query },
                success: function (response) {
                    $("#searchResults").html(response);
                }
            });
        } else {
            $("#searchResults").html("<p class='text-red-500'>Please enter a search query.</p>");
        }
    });

    // Fix issue: Ensure the search button can be clicked again
    $(document).on("click", "#navSearch", function (event) {
        event.preventDefault();
        $("#searchModal").remove(); // Ensure old modals are removed before opening a new one
    });
});

</script>
