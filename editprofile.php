<?php
session_start(); // Start the session to get user data


include "layout.php"; 
include "auth.php";
include "connection.php";


var_dump($_POST);  
if (isset($_POST["submit"])) {
    // Sanitize form input data
    $updatedfname = htmlspecialchars($_POST['fname']);
    $updatedlname = htmlspecialchars($_POST['lname']);
    $updatedmidname = htmlspecialchars($_POST['midname']);
    $updatedyear_level = htmlspecialchars($_POST['year_level']);
    $updatedcourse = htmlspecialchars($_POST['course']);
    $updatedaddress = htmlspecialchars($_POST['address']);
    
    // Check if the session userID is available
    if (!isset($_SESSION['userID'])) {
        echo "User ID not set. Please log in.";
        exit; // Stop if no user ID is found
    }

    // Get the student ID from the session
    $userID = $_SESSION['userID']; 

    // Check if the database connection is valid
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL query to update student info
    $query = "UPDATE studentinfo SET course = ?, fname = ?, lname = ?, midname = ?, year_level = ?, address = ? WHERE idno = ?";

    // Debug: Check the query
    echo "Query: $query\n"; 

    if ($stmt = $conn->prepare($query)) {
        // Bind the parameters (s = string, i = integer for userID)
        $stmt->bind_param("ssssssi", $updatedcourse, $updatedfname, $updatedlname, $updatedmidname, $updatedyear_level, $updatedaddress, $userID);

        if ($stmt->execute()) {
            echo "Profile updated successfully!";
        } else {
            echo "Error executing the query: " . $stmt->error;
        }

        // Close the statement after execution
        $stmt->close(); 
    } else {
        // If the statement couldn't be prepared, show an error
        echo "Error preparing the statement: " . $conn->error;
    }
}
?>



<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Edit Profile</h2>

    <form action="editprofile.php" method="POST" enctype="multipart/form-data">
        <div class="mb-4 grid grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                <input type="text" id="fname" name="fname" value="<?php echo $fname; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your first name">
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input type="text" id="lname" name="lname" value="<?php echo $ulname; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your last name">
            </div>
        </div>

        <!-- Mid Name -->
        <div class="mb-4">
            <label for="midname" class="block text-gray-700 font-medium mb-2">Middle Name</label>
            <input type="text" id="midname" name="midname" value="<?php echo $midname; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your middle name">
        </div>

        <!-- Course -->
        <div class="mb-4">
            <label for="course" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course</label>
            <select name="course" id="course" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="" disabled>Select your course</option>
                <option value="BSIT" <?php echo ($course == 'BSIT') ? 'selected' : ''; ?>>BSIT - Bachelor of Science in Information Technology</option>
                <option value="BSCS" <?php echo ($course == 'BSCS') ? 'selected' : ''; ?>>BSCS - Bachelor of Science in Computer Science</option>
            </select>
        </div>

        <!-- Year Level -->
        <div class="mb-4">
            <label for="year_level" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year Level</label>
            <select name="year_level" id="year_level" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="" disabled>Select Year Level</option>
                <option value="1" <?php echo ($yearlevel == '1') ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo ($yearlevel == '2') ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo ($yearlevel == '3') ? 'selected' : ''; ?>>3</option>
                <option value="4" <?php echo ($yearlevel == '4') ? 'selected' : ''; ?>>4</option>
            </select>
        </div>

        <!-- Profile Picture -->
        <div class="mb-4">
            <label for="profile_picture" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Address -->
        <div class="mb-6">
            <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
            <textarea id="address" name="address" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write something about yourself..."><?php echo $address; ?></textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Save Changes</button>
        </div>
    </form>
</div>
