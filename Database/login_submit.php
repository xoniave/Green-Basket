<?php

    session_start();

    include_once("connection.php");        // utk database 
    include_once("../class/user.php");       // utk class user

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $username = $_POST['username']; //ini ngambil dari name = ...
        $password = $_POST['password'];

        $user = new user('', '', $username, $password, '', '');
        $role = $user->loginAkun($conn);

        if($role){

            $_SESSION['logged'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if($role == "user"){
                header("location: ../Home.php");
                exit();
            }
            else if($role == "admin")
            {
                header("location: ../admin.php");
                exit();
            }

        }
        else{
            header("location: ../Index.php?error=login_failed");
            exit();
        }

    }
    else{
        header("location: ../Index.php");
        exit();
    }
?>