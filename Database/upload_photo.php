<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['new_profile_pic'])) {
    
    // TENTUKAN TUJUAN REDIRECT (PENTING!)
    // Kalau role admin -> ke admin.php, selain itu -> ke Home.php
    $redirect_url = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? '../admin.php' : '../Home.php';

    $username = $_SESSION['username'];
    $file = $_FILES['new_profile_pic'];
    
    // Validasi Error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Gagal upload gambar.'); window.location.href='$redirect_url';</script>";
        exit;
    }

    // Validasi Tipe File
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        echo "<script>alert('Hanya file JPG/PNG yang diperbolehkan.'); window.location.href='$redirect_url';</script>";
        exit;
    }

    // Proses Simpan
    $upload_dir = '../Gambar/'; 
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = "user_" . $username . "_" . time() . "." . $file_ext;
    $target_path = $upload_dir . $new_filename;
    $db_path = 'Gambar/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE username = ?");
            $stmt->execute([$db_path, $username]);
            
            // Redirect sesuai Role
            echo "<script>window.location.href='$redirect_url';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Database Error'); window.location.href='$redirect_url';</script>";
        }
    } else {
        echo "<script>alert('Gagal memindahkan file.'); window.location.href='$redirect_url';</script>";
    }
}
?>