<?php
include "connection.php";

// Fetch current user data
$userID = $_SESSION['idno'];
$query = "SELECT * FROM studentinfo WHERE idno = '$userID'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

?>
