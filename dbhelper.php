
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
            echo'You are connected';
    }


    $formUsername = $_POST['username'];//
    $formPassword = $_POST['password'];//


    // Execute the query
    $stmt->execute();
    $stmt->store_result();

    // Check if a user with the username exists
    if ($stmt->num_rows > 0) {
    // Bind result variables
    $stmt->bind_result($userId, $storedPassword);

    // Fetch the result
    $stmt->fetch();

    // Verify the password using password_verify() function
    if (password_verify($formPassword, $storedPassword)) {
        echo "Login successful!";
        // You can set up session or further logic here
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Username does not exist.";
}

// $sql = "INSERT INTO students (idno,lastname,firstname,
//         middlename,course,yearlevel,username,passw)
//         VALUES('123456','Clint','Alozno','S','BSIT','4','clint123','092222')";

//     if (mysqli_query($conn,$sql)){
//         echo 'Students created successfully!';
//     }else{
//         echo'Failed to add student';
//     }

$stmt->close();
mysqli_close($conn);
           


?>