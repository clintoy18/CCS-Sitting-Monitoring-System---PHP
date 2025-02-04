<?php 
include 'connection.php'; // Corrected PHP inclusion
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
    <div class="container px-6 py-12 mx-auto">
        <section class="bg-gray-50 dark:bg-gray-900 px-8 py-10 rounded-lg shadow-lg">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                <h1 class="text-2xl text-center font-bold leading-tight tracking-tight text-gray-900 dark:text-white mb-8">
                    Sign Up
                </h1>
                
                <div class="w-full bg-white rounded-lg shadow-md dark:border sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6 space-y-6 sm:p-8">
                        <!-- Logo section with logos and title -->
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
                        <form class="space-y-2" action="submit">
                            <div class="flex space-x-4">
                                <!-- Firstname -->
                                <div class="w-1/2">
                                    <label for="firstname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Firstname</label>
                                    <input type="text" name="firstname" id="firstname" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your first name" required>
                                </div>
                                <!-- Lastname -->
                                <div class="w-1/2">
                                    <label for="lastname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lastname</label>
                                    <input type="text" name="lastname" id="lastname" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your last name" required>
                                </div>
                            </div>
                            <div>
                                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your username" required>
                            </div>
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="flex items-start">
                                    <div class="ml-3 text-sm">
                                        <!-- Login Button -->
                                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Sign Up</button>
                                    </div>
                                </div>
                                <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot password?</a>
                            </div>
                            <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-3 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Sign Up</button>
                            <p class="text-sm font-light text-gray-500 dark:text-gray-400 mt-4 text-center">
                               Already have an account? <a href="login.php" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Log in</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
