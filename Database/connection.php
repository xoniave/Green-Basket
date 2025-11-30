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
    }
    catch(PDOException $e) {
        try {
            // Jika DB belum ada, buat dulu
            $conn_server = new PDO("mysql:host=$servername", $username, $password); 
            $conn_server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $conn_server->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            
            // Koneksi ulang ke DB baru
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); 
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e_server) {
            die("Koneksi gagal: " . $e_server->getMessage());
        }
    }

    // --- TAHAP 2: PEMBUATAN TABEL OTOMATIS (Auto Schema) ---

    try {
        // 1. Tabel Users (UPDATE: Tambah kolom address di create awal)
        $users_table_sql = "
            CREATE TABLE IF NOT EXISTS users (
                username    VARCHAR(50) PRIMARY KEY,
                password    TEXT,
                email       VARCHAR(100),
                address     TEXT, 
                firstname   VARCHAR(50),
                lastname    VARCHAR(50),
                role        VARCHAR(20)
            ) ENGINE=InnoDB;
        ";
        $conn->exec($users_table_sql);

        // --- AUTO MIGRATION: Cek kolom 'address' untuk database lama ---
        // Ini penting! Jika tabel sudah ada tapi belum punya kolom 'address', script ini akan menambahkannya.
        $check_col = $conn->query("SHOW COLUMNS FROM users LIKE 'address'");
        if ($check_col->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN address TEXT AFTER email");
        }

        // --- AUTO MIGRATION: Tambah kolom profile_pic ---
        $check_pic = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
        if ($check_pic->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT 'Gambar/Profile.png'");
        }

        // 2. Tabel Inventory
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

        // 3. Tabel Orders
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

        // 4. Tabel Categories
        $cat_table_sql = "
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE
            ) ENGINE=InnoDB;
        ";
        $conn->exec($cat_table_sql);


        // --- TAHAP 3: SEEDING (Isi Data Awal) ---
        
        // Insert Admin Default
        $stmt = $conn->prepare("SELECT count(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        if($stmt->fetchColumn() == 0){
             $pass = password_hash("admin", PASSWORD_DEFAULT);
             // Perhatikan: Kita tambah field address kosong ('') di query insert
             $conn->exec("INSERT INTO users (username, password, email, address, firstname, lastname, role) 
                          VALUES('admin', '$pass', 'admin@greenbasket.com', '', 'System', 'Admin', 'admin')");
        }

        // Insert Kategori dari Inventory yang sudah ada
        $conn->exec("INSERT IGNORE INTO categories (name) SELECT DISTINCT category FROM inventory WHERE category IS NOT NULL AND category != ''");

    } catch (PDOException $e) {
        die("Gagal setup tabel: " . $e->getMessage());
    }
?>