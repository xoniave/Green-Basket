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

            $inventory_table_sql = "
                CREATE TABLE IF NOT EXISTS inventory (
                    id VARCHAR(10) NOT NULL PRIMARY KEY,
                    sku VARCHAR(50),
                    category VARCHAR(50),
                    name VARCHAR(100),
                    description TEXT,
                    price INT(11),
                    stock INT(11),
                    image VARCHAR(255)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            $conn->exec($inventory_table_sql);


            $orders_table_sql = "
                CREATE TABLE IF NOT EXISTS orders (
                    id VARCHAR(10) NOT NULL PRIMARY KEY,
                    item_id VARCHAR(50),
                    order_date DATE,
                    status VARCHAR(20),
                    payment_method VARCHAR(20),
                    total INT(11),
                    username VARCHAR(50)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            $conn->exec($orders_table_sql);


            $stmt = $conn->query("SELECT COUNT(*) FROM orders");
            if ($stmt->fetchColumn() == 0) {
                $conn->exec("
                    INSERT INTO orders (id, item_id, order_date, status, payment_method, total, username) VALUES
                    ('ORD-01', 'Item-01', '2025-02-01', 'Complete', 'COD', 100, '@krlasdwwa')
                ");
            }

            $stmt = $conn->query("SELECT COUNT(*) FROM inventory");
            if ($stmt->fetchColumn() == 0) {
                $conn->exec("
                    INSERT INTO inventory (id, sku, category, name, description, price, stock, image) VALUES
                    ('Item-01', 'JAHE', 'bumbu', 'Jahe special', '100% asli jahe special', 100, 1, NULL)
                ");
            }

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