<?php 
include 'connection.php';

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
    if (empty($idno) || empty($fname) || empty($lname) || empty($midname) || empty($course) || empty($yearlevel) || empty($password)) {
        echo "<script>alert('Error: All fields are required.');</script>";
    } else {
        // Check if ID already exists
        $check_query = "SELECT * FROM studentinfo WHERE idno = '$idno'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Error: ID number already registered. Please use a different ID.');</script>";
        } else {
            // Secure password with hashing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new student record
            $query = "INSERT INTO studentinfo (idno, fname, lname, midname, course, year_level, password) 
                      VALUES ('$idno', '$fname', '$lname', '$midname', '$course', '$yearlevel', '$hashed_password')";

            if (mysqli_query($conn, $query)) {
                header("Location: login.php");
                exit();
            } else {
                echo "<script>alert('Error: Could not execute the query. " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container py-12 mx-auto max-w-lg">
        <section class="bg-white px-8 py-10 rounded-lg shadow-lg">
            <div class="space-y-6">
                <h1 class="text-2xl text-center font-bold text-gray-900 mb-6">Sign Up</h1>
                <div class="flex justify-between items-center mb-6">
                    <img src="ccslogo.png" alt="CCS Logo" class="h-16">
                    <h1 class="text-md font-bold text-gray-900 text-center">CCS SIT-IN MONITORING SYSTEM</h1>
                    <img src="uclogo.jpg" alt="UC Logo" class="h-16">
                </div>  

                <form action="registration.php" method="post" class="space-y-4">
                    <div>
                        <label for="idno" class="block text-sm font-medium text-gray-700">ID Number</label>
                        <input type="text" name="idno" id="idno" placeholder="Enter your ID Number" class="input" required>
                    </div>
                    
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="firstname" id="firstname" placeholder="Enter your First Name" class="input" required>
                    </div>

                    <div>
                        <label for="midname" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" name="midname" id="midname" placeholder="Enter your Middle Name" class="input" required>
                    </div>

                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="lastname" id="lastname" placeholder="Enter your Last Name" class="input" required>
                    </div>

                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                        <select name="course" id="course" class="input" required>
                            <option value="" disabled selected>Select your course</option>
                            <option value="BSIT">BSIT - Information Technology</option>
                            <option value="BSCS">BSCS - Computer Science</option>
                        </select>
                    </div>

                    <div>
                        <label for="yearlevel" class="block text-sm font-medium text-gray-700">Year Level</label>
                        <select name="yearlevel" id="yearlevel" class="input" required>
                            <option value="" disabled selected>Select your year level</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter your Password" class="input" required>
                    </div>

                    <button type="submit" name="submit" class="w-full py-2 px-4 bg-blue-700 hover:bg-blue-800 text-white rounded-lg">
                        Sign Up
                    </button>

                    <p class="text-sm text-center text-gray-500 mt-4">
                        Already have an account? 
                        <a href="login.php" class="text-blue-600 hover:underline">Log in</a>
                    </p>
                </form>
            </div>
        </section>
    </div>

    <style>
        .input {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            color: #111827;
            border-radius: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>
</body>
</html>

