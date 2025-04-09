<?php
session_start();
include("connection.php");

$error = "";

if (isset($_POST['submit'])) {
    $idno = mysqli_real_escape_string($conn, $_POST['idno']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    if ($idno == "" || $password == "") {
        $error = "Either ID number or password field is empty.";
    } else {
        $admin_result = mysqli_query($conn, "SELECT * FROM admins WHERE admin_id = '$idno'");
        $admin_row = mysqli_fetch_assoc($admin_result);

        if ($admin_row) {
            if ($admin_row['password'] == $password) {
                $_SESSION['admin_id'] = $admin_row['admin_id'];  
                $_SESSION['name'] = $admin_row['name'];   
                $_SESSION['role'] = 'admin'; 
                header('Location: admindashboard.php');
                exit();
            } else {
                $error = "Invalid ID or password for admin.";
            }
        } else {
            $result = mysqli_query($conn, "SELECT * FROM studentinfo WHERE idno = '$idno'");
            $row = mysqli_fetch_assoc($result);

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['idno'] = $row['idno'];  
                $_SESSION['lname'] = $row['lname'];   
                $_SESSION['fname'] = $row['fname'];   
                $_SESSION['midname'] = $row['midname'];  
                $_SESSION['course'] = $row['course'];  
                $_SESSION['year_level'] = $row['year_level'];  
                $_SESSION['address'] = $row['address'] ?? '';  
                $_SESSION['session'] = $row['session'] ?? '';  
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Invalid ID or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CCS Sit-in Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        setTimeout(() => {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center items-center px-4">
      

        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
            <?php if ($error): ?>
                <div id="error-message" class="bg-red-500 text-white text-sm p-2 rounded text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
            <div class="flex justify-between items-center w-full max-w-2xl mb-6">
            <img src="ccslogo.png" alt="CCS Logo" class="h-20">
            <h1 class="text-xl md:text-2xl font-bold text-center text-gray-800 dark:text-white">
                CCS SIT-IN MONITORING SYSTEM
            </h1>
            <img src="uclogo.jpg" alt="UC Logo" class="h-20">
        </div>
                <div>
                    <label for="idno" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">ID Number</label>
                    <input type="text" name="idno" id="idno" placeholder="Enter your ID number" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                </div>

                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                </div>

                <button type="submit" name="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition">
                    Login
                </button>

                <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                    Don’t have an account? 
                    <a href="registration.php" class="text-blue-600 hover:underline dark:text-blue-400">Sign up</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
