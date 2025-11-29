<?php
// Database/Inventory.php

// Panggil file koneksi database
include("connection.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['update_item'])) {
        
        // Ambil data yang dikirimkan (Pastikan nama input di admin.php sama)
        $sku = $_POST['sku']; 
        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $description = $_POST['description'];
        $harga = $_POST['harga'];
        $stock = $_POST['stock']; 
        $image_path = null;
        $upload_dir = '../Gambar/UploadDariDashboardAdmin/';

        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_name = basename($_FILES['product_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid('prod_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            // Pindahkan file ke direktori permanen
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $image_path = 'Gambar/UploadDariDashboardAdmin/' . $new_file_name; // Path yang akan disimpan di DB
            } 
        }

        try {
            $sql_set = "name = :nama, category = :kategori, description = :description, price = :harga, stock = :stock";
            $params = [
                ':nama' => $nama,
                ':kategori' => $kategori,
                ':description' => $description,
                ':harga' => $harga,
                ':stock' => $stock,
                ':sku' => $sku
            ];

            if ($image_path) {
                $sql_set .= ", image = :image_path";
                $params[':image_path'] = $image_path;
            }

            $sql = "UPDATE inventory SET " . $sql_set . " WHERE sku = :sku";
            $stmt = $conn->prepare($sql);
            
            $stmt->execute($params);
            header("Location: ../admin.php?page=inventory&update_status=success");
            exit();

        } catch (PDOException $e) {
            header("Location: ../admin.php?page=inventory&update_status=error&msg=" . urlencode($e->getMessage()));
            exit();
        }
    }



} else {
    // Jika diakses tanpa method POST, kembalikan ke halaman admin
    header("Location: ../admin.php");
    exit();
}
?>