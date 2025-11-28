<?php

class User{
    public $firstname;
    public $lastname;
    public $username;
    public $password;
    public $email;
    public $role;

    public function __construct($firstname, $lastname, $username, $password, $email, $role){
        $this->firstname    =   $firstname;
        $this->lastname     =   $lastname; 
        $this->username     =   $username; 
        $this->password     =   $password;
        $this->email        =   $email; 
        $this->role         =   $role;
    }
    
    public function save($conn) {
         //----insert data----
        $data=$conn->prepare("insert into users(username, password, email, firstname, lastname, role) values(?,?,?,?,?,?)");
        $data->execute([$this->username,password_hash($this->password, PASSWORD_DEFAULT), $this->email, $this->firstname, $this->lastname, $this->role]);
        return true;
    }

}
?>