<?php
ob_start();
session_start(); // Start the session to get user data

include "layout.php";
include "auth.php";
include "connection.php";

// Fetch current user data
$userID = $_SESSION['idno'];
$query = "SELECT * FROM studentinfo WHERE idno = '$userID'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

if (isset($_POST["submit"])) {
    // Sanitize user inputs
    $fname = mysqli_real_escape_string($conn, $_POST["firstname"]);
    $lname = mysqli_real_escape_string($conn, $_POST["lastname"]);
    $midname = mysqli_real_escape_string($conn, $_POST["midname"]);
    $course = mysqli_real_escape_string($conn, $_POST["course"]);
    $yearlevel = mysqli_real_escape_string($conn, $_POST["yearlevel"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);

   // Handle file upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<font color='red'>Error: File is not an image.</font>";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<font color='red'>Error: Sorry, file already exists.</font>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        echo "<font color='red'>Error: Sorry, your file is too large.</font>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "<font color='red'>Error: Sorry, only JPG, JPEG, PNG & GIF files are allowed.</font>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<font color='red'>Error: Sorry, your file was not uploaded.</font>";
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded.";
        } else {
            echo "<font color='red'>Error: Sorry, there was an error uploading your file.</font>";
        }
    }
} else {
    // If no file is uploaded, set $target_file to an empty string (or use the existing image)
    $target_file = $userData['profile_picture'];  
}


    // Check if any field is empty
    if ($userID == "" || $fname == "" || $lname == "" || $midname == "" || $course == "" || $yearlevel == "") {
        echo "<font color='red'>Error: All fields are required.</font>";
    } else {
        // Update the user info
        $result = "UPDATE studentinfo SET fname = '$fname', lname = '$lname', midname = '$midname', course = '$course', year_level = '$yearlevel', address = '$address', profile_picture = '$target_file' WHERE idno = '$userID'";
        $row = mysqli_query($conn, $result);

        if ($row) {
            header("Location: dashboard.php");
            exit(); // Ensure the script stops here after redirection
        } else {
            echo "<font color='red'>Error: Could not execute the query. " . mysqli_error($conn) . "</font>";
        }
    }
}
?>


<div class="max-w-2xl mx-auto bg-white m-6 p-6 rounded-lg shadow-lg ">
    <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Edit Profile</h2>

    <form action="editprofile.php" method="post" enctype="multipart/form-data">
        <!-- Profile Picture -->
        <div class="mb-4 justify-items-center">
            <label for="profile_picture" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
            <?php if (!empty($userData['profile_picture'])): ?>
         <img src="<?php echo !empty($userData['profile_picture']) ? $userData['profile_picture'] : 'uploads/default.jpg'; ?>" 
                alt="Profile Picture" 
                class="w-32 h-32 md:w-64 md:h-64 rounded-full border-4 border-gray-300 object-cover aspect-square mx-auto">

                <?php endif; ?>
            <input type="file" id="profile_picture" name="profile_picture" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
                <label for="fname" class="block text-gray-700 font-medium mb-2">First Name</label>
                <input type="text" id="fname" name="firstname" value="<?php echo $userData['fname']; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your first name">
            </div>

            <!-- Last Name -->
            <div>
                <label for="lname" class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input type="text" id="lname" name="lastname" value="<?php echo $userData['lname']; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your last name">
            </div>
        </div>

        <!-- Mid Name -->
        <div class="mb-4">
            <label for="midname" class="block text-gray-700 font-medium mb-2">Middle Name</label>
            <input type="text" id="midname" name="midname" value="<?php echo $userData['midname']; ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your middle name">
        </div>

        <!-- Course -->
        <div class="mb-4">
            <label for="course" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course</label>
            <select name="course" id="course" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="" disabled>Select your course</option>
                <option value="BSIT" <?php echo ($userData['course'] == 'BSIT') ? 'selected' : ''; ?>>BSIT - Bachelor of Science in Information Technology</option>
                <option value="BSCS" <?php echo ($userData['course'] == 'BSCS') ? 'selected' : ''; ?>>BSCS - Bachelor of Science in Computer Science</option>
            </select>
        </div>

        <!-- Year Level -->
        <div class="mb-4">
            <label for="year_level" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year Level</label>
            <select name="yearlevel" id="year_level" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="" disabled>Select Year Level</option>
                <option value="1" <?php echo ($userData['year_level'] == '1') ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo ($userData['year_level'] == '2') ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo ($userData['year_level'] == '3') ? 'selected' : ''; ?>>3</option>
                <option value="4" <?php echo ($userData['year_level'] == '4') ? 'selected' : ''; ?>>4</option>
            </select>
        </div>


        <!-- Address -->
        <div class="mb-6">
            <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
            <textarea id="address" name="address" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write something about yourself..."><?php echo $userData['address']; ?></textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" name="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Save Changes</button>
        </div>
    </form>
</div>