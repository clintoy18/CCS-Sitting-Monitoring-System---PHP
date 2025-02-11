<?php
session_start();

include "layout.php"; 
include "auth.php";


?>

<div class="px-8 py-8 ps-8 grid grid-cols-3 gap-4">
   
    <div class="box-content bg-blue-900 md:box-border border-4 p-8 text-white "> <h1 class="text-center text-xl pb-4">Student Information</h1>
    <img src="ccslogo.png" alt="" class="rounded-full mx-auto ">
    <div class="flex-items-start p-8">
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
    <div class="box-content bg-blue-900 md:box-border border-4 p-8 text-white"><h1 class="text-center text-xl pb-4 ">Announcement</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam sit libero odit eveniet, ad,
         velit necessitatibus temporibus placeat nisi, ducimus praesentium unde nemo est neque delectus quidem! 
         Quisquam, maiores tempore! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque fugiat ut amet ea quod exercitationem at quaerat repellendus nemo odio blanditiis,
         adipisci rerum! Explicabo excepturi natus corrupti optio enim modi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ea quia culpa excepturi laborum nesciunt eaque 
         laboriosam ratione molestias architecto consectetur! Nihil natus non hic quibusdam at cum est similique id? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos maxime hic corporis, quaerat delectus rem autem est quam accusamus, similique nulla recusandae, voluptas quos aliquam dolores modi nihil repellat! Perferendis?
    </p>
    </div>

    <div class="box-content bg-blue-900 md:box-border border-4 p-8 text-white"><h1 class="text-center text-xl pb-4">Rules and Regulations</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam sit libero odit eveniet, ad,
         velit necessitatibus temporibus placeat nisi, ducimus praesentium unde nemo est neque delectus quidem! 
         Quisquam, maiores tempore! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque fugiat ut amet ea quod exercitationem at quaerat repellendus nemo odio blanditiis,
         adipisci rerum! Explicabo excepturi natus corrupti optio enim modi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ea quia culpa excepturi laborum nesciunt eaque 
         laboriosam ratione molestias architecto consectetur! Nihil natus non hic quibusdam at cum est similique id? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos maxime hic corporis, quaerat delectus rem autem est quam accusamus, similique nulla recusandae, voluptas quos aliquam dolores modi nihil repellat! Perferendis?
    </p>
    </div>


</div>
    