<?php
ob_start();
session_start(); // Start the session to get user data

include "layout.php"; 
include "auth.php";
include "connection.php";


if (isset($_POST["submit"])) {


    // Sanitize user inputs
    $fname = mysqli_real_escape_string($conn, $_POST["firstname"]);
    $lname = mysqli_real_escape_string($conn, $_POST["lastname"]);
    $midname = mysqli_real_escape_string($conn, $_POST["midname"]);
    $course = mysqli_real_escape_string($conn, $_POST["course"]);
    $yearlevel = mysqli_real_escape_string($conn, $_POST["yearlevel"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);

    // Check if any field is empty
    if ($userID == "" || $fname == "" || $lname == "" || $midname == "" || $course == "" || $yearlevel == "") {
        echo "<font color='red'>Error: All fields are required.</font>";
    } else {
        // Update the user info      
        $userID = $_SESSION['idno']; 
        
        $result = "UPDATE studentinfo SET fname = '$fname', lname = '$lname', midname = '$midname', course = '$course', year_level = '$yearlevel', address = '$address' WHERE idno = '$userID'";
        $row = mysqli_query($conn, $result);
        if ($row) {
            $_SESSION['Success'] = "Student updated!";
            exit(); // Ensure the script stops here after redirection
        } else {
            echo "<font color='red'>Error: Could not execute the query. " . mysqli_error($conn) . "</font>";
        }
    }
}
?>