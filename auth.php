<?php

include "connection.php";

// Check if the user is logged in and the session variable exists
if (isset($_SESSION['idno'])) {
    $userID = $_SESSION['idno'];  // Get the logged-in user ID from the session
    $ufname = $_SESSION['fname'];
    $ulname = $_SESSION['lname'];
   
} else {
    // If the session variable doesn't exist, redirect to the login page or show an error
    echo "User is not logged in.";
}
?>
