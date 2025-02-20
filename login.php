<?php
session_start();
include("connection.php"); // Include your DB connection
// Define error variable
$error = "";

if (isset($_POST['submit'])) {
  
    $idno = mysqli_real_escape_string($conn, $_POST['idno']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // Check if ID or password is empty
    if ($idno == "" || $password == "") {
        $error = "Either ID number or password field is empty.";
    } else {
        // Password verification 
        $result = mysqli_query($conn, "SELECT * FROM studentinfo WHERE idno = '$idno' AND `password` ='$password'") or die("Could not execute the select query.");
        $row = mysqli_fetch_assoc($result);

        if ($row) { 
            // Store only the user ID in the session, not the password
            $_SESSION['idno'] = $row['idno'];  
            $_SESSION['lname'] = $row['lname'];   
            $_SESSION['fname'] = $row['fname'];   
            $_SESSION['midname'] = $row['midname'];  
            $_SESSION['course'] = $row['course'];  
            $_SESSION['year_level'] = $row['year_level'];  
            $_SESSION['address'] = $row['address'];  
            $_SESSION['session'] = $row['session'];  
                
           
            // Redirect to dashboard after successful login
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid ID or password."; // Set the error message
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
   
   <script>
        setTimeout(function() {
            const errorMessage = document.getElementById('error-message');
            if(errorMessage){
                errorMessage.style.display = 'none';
            }      
        }, 2000);
    </script>
</head>
<body>

<div class="container px-6 py-12 mx-auto">
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    
                    <!-- Error message display -->
                    <?php if ($error): ?>
                        <div id="error-message" class="bg-red-500 text-white p-2 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex">
                        <div class="w-1/2">
                            <img src="ccslogo.png" alt="" class="" style="height: 100px;">
                        </div>
                        <h1 class="text-xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                            CCS SIT-IN MONITORING SYSTEM
                        </h1>
                        <div class="w-1/2">
                            <img src="uclogo.jpg" alt="" class="" style="height: 100px;">
                        </div>
                    </div>  

                    <form class="space-y-4 md:space-y-6" action="" method="POST">
                        <div>
                            <label for="idno" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID no.</label>
                            <input type="text" name="idno" id="idno" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your ID number" required>
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                        </div>

                        <button type="submit" name="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Login
                        </button>
                        
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400 mt-4">
                            Don’t have an account yet? <a href="registration.php" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>
