<?php 
    include("connection.php");        // utk database 
    include("class/user.php");       // utk class user

    $username = $_POST['username']; 
    $password = $_POST['password']; 
    $firstname=$_POST['firstname']; 
    $lastname =$_POST['lastname']; 
    $email    =$_POST['email']; 
    $role     = "user"; 

    $user = new User($firstname, $lastname, $username, $password, $email, $role); 
    $user->save($conn); 

    // habis dari register echo "sukses";
    echo "sukses"; 
?>