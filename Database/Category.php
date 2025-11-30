<?php
// File: Database/Category.php
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // LOGIKA TAMBAH KATEGORI
    if (isset($_POST['add_category'])) {
        $new_cat = trim($_POST['category_name']);

        if (!empty($new_cat)) {
            try {
                // Cek dulu biar gak duplikat
                $check = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
                $check->execute([$new_cat]);
                
                if ($check->fetchColumn() == 0) {
                    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([$new_cat]);
                    // Berhasil
                    header("Location: ../admin.php?page=inventory&msg=Kategori Berhasil Ditambah");
                } else {
                    // Duplikat
                    header("Location: ../admin.php?page=inventory&error=Kategori Sudah Ada");
                }
            } catch (PDOException $e) {
                header("Location: ../admin.php?page=inventory&error=" . urlencode($e->getMessage()));
            }
        } else {
            header("Location: ../admin.php?page=inventory&error=Nama Kategori Kosong");
        }
        exit();
    }
}
?>