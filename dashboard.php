<?php
session_start();
include "layout.php"; 
include "auth.php";
?>

<div class="px-8 py-8 ps-8 grid grid-cols-3 gap-2 ">
<div class="rounded-lg border bg-white border-gray-300 p-6 hover:bg-blue-500 hover:rounded-xl transition-all duration-300">
    <h1 class="text-center text-xl pb-4">Student Information</h1>
    <img src="ccslogo.png" alt="" class="rounded-full mx-auto mb-6">
    
    <div class="flex flex-col space-y-4 ">  
        <p class="flex items-center"><b class="w-16">IDNO</b><?php echo ": " . $userID ?></p>
        <p class="flex items-center"><b class="w-16">Name</b><?php echo ": " . $fname . " " . $lname ?></p>
        <p class="flex items-center"><b class="w-16">Course</b><?php echo ": " . $course ?></p>
        <p class="flex items-center"><b class="w-16">Year</b><?php echo ": " . $yearlevel ?></p>
        <p class="flex items-center"><b class="w-16">Address</b><?php echo ": " . $address ?></p>
        <p class="flex items-center"><b class="w-16">Session</b><?php echo ": " . $session ?></p>
    </div>
</div>

<div class="rounded-lg border bg-white border-gray-300 p-6 hover:bg-blue-500 hover:rounded-xl transition-all duration-300">
<h1 class="text-center text-xl pb-4 ">Announcement</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam sit libero odit eveniet, ad,
         velit necessitatibus temporibus placeat nisi, ducimus praesentium unde nemo est neque delectus quidem! 
         Quisquam, maiores tempore! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque fugiat ut amet ea quod exercitationem at quaerat repellendus nemo odio blanditiis,
         adipisci rerum! Explicabo excepturi natus corrupti optio enim modi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ea quia culpa excepturi laborum nesciunt eaque 
         laboriosam ratione molestias architecto consectetur! Nihil natus non hic quibusdam at cum est similique id? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos maxime hic corporis, quaerat delectus rem autem est quam accusamus, similique nulla recusandae, voluptas quos aliquam dolores modi nihil repellat! Perferendis?
    </p>
    </div>

    <div class="rounded-lg border bg-white border-gray-300 p-6 hover:bg-blue-500 hover:rounded-xl transition-all duration-300">
     <h1 class="text-center text-xl pb-4">Rules and Regulations</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam sit libero odit eveniet, ad,
         velit necessitatibus temporibus placeat nisi, ducimus praesentium unde nemo est neque delectus quidem! 
         Quisquam, maiores tempore! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque fugiat ut amet ea quod exercitationem at quaerat repellendus nemo odio blanditiis,
         adipisci rerum! Explicabo excepturi natus corrupti optio enim modi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ea quia culpa excepturi laborum nesciunt eaque 
         laboriosam ratione molestias architecto consectetur! Nihil natus non hic quibusdam at cum est similique id? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos maxime hic corporis, quaerat delectus rem autem est quam accusamus, similique nulla recusandae, voluptas quos aliquam dolores modi nihil repellat! Perferendis?
    </p>
    </div>


</div>
    