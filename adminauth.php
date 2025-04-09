<?php
include "connection.php";

//Fetch current admin data
$adminID = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE admin_id = '$adminID'";
$adminresult = mysqli_query($conn, $query);
$adminData = mysqli_fetch_assoc($adminresult);


?>




