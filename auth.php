<?php
include "connection.php";

$userID = $_SESSION["idno"];

$result = mysqli_query($conn, "SELECT * FROM studentinfo WHERE idno = '$userID'");
$row = mysqli_fetch_assoc($result);

if($row){
     $userID = $row['idno'];
     $fname = $row['fname'];
     $lname = $row['lname'];
     $midname = $row['midname'];
     $course = $row['course'];
     $yearlevel = $row['year_level'];
     $address = $row['address'];
}

?>
