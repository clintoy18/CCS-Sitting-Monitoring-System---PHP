<?php
include "connection.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it hasn't been started
}

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
//Fetch current admin data
$adminID = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE admin_id = '$adminID'";
$adminresult = mysqli_query($conn, $query);
$adminData = mysqli_fetch_assoc($adminresult);


?>




