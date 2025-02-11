<?php 
include 'connection.php'; //


if (isset($_POST["submit"])) {
    // Sanitize user inputs
    $idno = mysqli_real_escape_string($conn, $_POST["idno"]);
    $fname = mysqli_real_escape_string($conn, $_POST["firstname"]);
    $lname = mysqli_real_escape_string($conn, $_POST["lastname"]);
    $midname = mysqli_real_escape_string($conn, $_POST["midname"]);
    $course = mysqli_real_escape_string($conn, $_POST["course"]);
    $yearlevel = mysqli_real_escape_string($conn, $_POST["yearlevel"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    

    // Check if any field is empty
    if ($idno == "" || $fname == "" || $lname == "" || $midname == "" || $course == "" || $yearlevel == "" || $password == "" ) {
        echo "<font color='red'>Error: All fields are required.</font>";
    } else {
        // Hash password for security
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // SQL query for insertion
        $query = "INSERT INTO studentinfo (idno, fname, lname, midname, course, year_level, password, address) 
                  VALUES ('$idno', '$fname', '$lname', '$midname', '$course', '$yearlevel', '$password', '$address')";

        // Execute the query and check for success
        if (mysqli_query($conn, $query)) {
            header("Location: login.php");
            echo "<font color='green'>Data added successfully.</font>";
        } else {
            header("Location: registration.php");
            echo "<font color='red'>Error: Could not execute the query. " . mysqli_error($conn) . "</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    <div class="container py-12 mx-auto">
        
        <section class="bg-gray-50 dark:bg-gray-900 px-8 py-10 rounded-lg shadow-lg">
            <div class="p-6 space-y-6 sm:p-8">
                <!-- Logo section with logos and title -->
                <h1 class="text-2xl text-center font-bold leading-tight tracking-tight text-gray-900 dark:text-white mb-8">
                    Sign Up
                </h1>
                <div class="flex justify-between items-center mb-6">
                    <div class="w-1/3 flex justify-center">
                        <img src="ccslogo.png" alt="CCS Logo" class="h-16">
                    </div>
                    <h1 class="text-lg font-bold text-center text-gray-900 dark:text-white mx-4">
                        CCS SIT-IN MONITORING SYSTEM
                    </h1>
                    <div class="w-1/3 flex justify-center">
                        <img src="uclogo.jpg" alt="UC Logo" class="h-16">
                    </div>
                </div>  

                <!-- Form section -->
                <form class="space-y-2" action="registration.php" method="post">
                    <div>
                        <label for="idno" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Idno</label>
                        <input type="text" name="idno" id="idno" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your idno" required>
                    </div>

                    <!-- Firstname -->
                    <div>
                        <label for="firstname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Firstname</label>
                        <input type="text" name="firstname" id="firstname" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your first name" required>
                    </div>

                    <!-- Midname -->
                    <div>
                        <label for="midname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Middle Name</label>
                        <input type="text" name="midname" id="midname" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your middle name" required>
                    </div>

                    <!-- Lastname -->
                    <div>
                        <label for="lastname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lastname</label>
                        <input type="text" name="lastname" id="lastname" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your last name" required>
                    </div>

                    <!-- Course -->
                    <div>
                        <label for="course" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course</label>
                        <input type="text" name="course" id="course" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your course" required>
                    </div>

                    <!-- Year Level -->
                    <div>
                        <label for="yearlevel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year Level</label>
                        <input type="text" name="yearlevel" id="yearlevel" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your year level" required>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your password" required>
                    </div> <!-- Address -->
                   
                    <!-- Submit Button -->
                    <div class="flex items-center justify-between mt-6">
                        <button type="submit" name="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Sign Up
                        </button>
                    </div>

                    <p class="text-sm font-light text-gray-500 dark:text-gray-400 mt-4 text-center">
                        Already have an account? <a href="login.php" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Log in</a>
                    </p>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
