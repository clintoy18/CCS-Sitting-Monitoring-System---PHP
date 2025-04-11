<?php
session_start();
include "../includes/connection.php"; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']); // Securely convert ID to integer

    $query = "DELETE FROM announcements WHERE announcement_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/admindashboard.php"); // Redirect after delete
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
