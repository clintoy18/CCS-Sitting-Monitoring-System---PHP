<?php
session_start();

include "layout.php"; 
include "auth.php";


?>

<div class="px-8 py-8 ps-8 grid grid-cols-3 gap-4">
   
    <div class="box-content bg-blue-900 md:box-border border-4 p-4 text-white justify-items-center "> <h1 class="text-center text-xl">Student Information</h1>
    <img src="ccslogo.png" alt="" class="rounded-full size-32">
    <div class="flex-items-start">
    <p><b>IDNO: </b><?php echo $userID ?></p>
    <p><b>Name: </b><?php echo $ufname . "  ".$ulname  ?></p>
    <p><b>Course:</b>  <?php echo $course ?> </p>
    <p><b>Year: </b> <?php echo $year_level ?></p>
    <p><b>Email:</b> </p>
    <p><b>Address : </b></p>
    <p><b>Session:</b> </p>
    <p></p>
    </div>

    </div>
    <div class="box-content bg-blue-900 md:box-border border-4 p-4 text-white"><h1 class="text-center text-xl">Announcement</h1>
    </div>
    <div class="box-content bg-blue-900 md:box-border border-4 p-4 text-white"><h1 class="text-center text-xl">Rules and Regulation</h1>
    </div>


</div>
    