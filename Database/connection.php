<?php
    $servername="localhost";
    $username="root";
    $password="";
    $dbname="GreenBasket";

    $conn = null;

    try {
        // --- TAHAP 1: Coba Koneksi ke DATABASE ---
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully"; 

    }
    catch(PDOException $e) {
        try {
            $conn_server = new PDO("mysql:host=$servername", $username, $password); // ini koneksi gk pake db
            $conn_server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $conn_server->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // konekin lgi skrg + db
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 3. Buat Tabel 'users'
            $users_table_sql = "
                CREATE TABLE IF NOT EXISTS users (
                    username    VARCHAR(50) PRIMARY KEY,
                    password    TEXT,
                    email       VARCHAR(100),
                    firstname   VARCHAR(50),
                    lastname    VARCHAR(50),
                    role        VARCHAR(20)
                ) ENGINE=InnoDB;
            ";
            $conn->exec($users_table_sql);

            // insert akun admin biar langsung ada
            if (file_exists('../class/user.php')) {
                include_once('../class/user.php');


                $admin_username = "admin";
                $admin_password = "admin"; 
                $admin_firstname = "System";
                $admin_lastname = "Admin";
                $admin_email = "admin@greenbasket.com";
                $admin_role = "admin";
                
                
                $admin_user = new User($admin_firstname, $admin_lastname, $admin_username, $admin_password, $admin_email, $admin_role);
                $admin_user->save($conn);
                
            }

        } catch (PDOException $e_server) {
            die("Koneksi gagal dan pembuatan database juga gagal: " . $e_server->getMessage());
        }
    }
?>