
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ccsmonitoringsystem";

    //Create connection
    try{
        $conn = mysqli_connect($servername,$username,$password,$dbname);

    }catch(mysqli_sql_exception){
        echo'Connection failed.';    
    }
    if($conn)
        {
            echo'';
    }



?>