<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it hasn't been started
}

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['idno'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

include "connection.php";

// Fetch current user data
$userID = $_SESSION['idno'];
$query = "SELECT * FROM studentinfo WHERE idno = '$userID'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

//fetch admin data


?>
