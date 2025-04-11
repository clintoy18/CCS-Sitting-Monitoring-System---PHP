<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it hasn't been started
}

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['idno'])) {
    // Redirect to login page if not logged in
    header("Location: ../auth/login.php"); // Ensure redirection to auth/login.php
    exit();
}

include "connection.php";

// Fetch current user data for students
$userID = $_SESSION['idno'] ?? null;
if ($userID) {
    $query = "SELECT * FROM studentinfo WHERE idno = '$userID'";
    $result = mysqli_query($conn, $query);
    $userData = mysqli_fetch_assoc($result);
}

// Fetch current user data for admins
$adminID = $_SESSION['admin_id'] ?? null;
if ($adminID) {
    $query = "SELECT * FROM admins WHERE admin_id = '$adminID'";
    $result = mysqli_query($conn, $query);
    $adminData = mysqli_fetch_assoc($result);
}
?>
