<?php
session_start();
include "connection.php"; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = mysqli_real_escape_string($conn, $_POST['announcement']);

    // Insert into database
    $query = "INSERT INTO announcements (content, created_at) VALUES ('$announcement', NOW())";
    
    if (mysqli_query($conn, $query)) {
        header("Location: admindashboard.php"); // Redirect back to the dashboard
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
ob_end_flush();
?>
