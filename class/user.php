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

    public static function cekUserExist($conn,$username){
        $data=$conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $data->execute([':username' => $username]);
        $count = $data->fetchColumn();
        return $count > 0;
    }

    public function loginAkun($conn){
        $data=$conn->prepare("SELECT * FROM users WHERE username = ?");
        $data->execute([$this->username]);

        $user_data = $data-> fetch(PDO::FETCH_ASSOC);

        if($user_data){
        $hashed_password = $user_data['password'];
        $role = $user_data['role'];
        
        // 2. Verifikasi password dengan hash
        if(password_verify($this->password, $hashed_password)){
            // Login berhasil, kembalikan role (yang diharapkan oleh login_submit.php)
            return $role; 
        }
    }
    }
}
?>