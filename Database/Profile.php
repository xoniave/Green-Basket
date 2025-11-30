<?php
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // TENTUKAN TUJUAN REDIRECT
    $redirect_url = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? '../admin.php' : '../Home.php';

    $username_lama = $_SESSION['username'];
    $action = $_POST['action']; 

    try {
        if ($action == 'update_email') {
            $email = $_POST['email'];
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE username = ?");
            $stmt->execute([$email, $username_lama]);
            $msg = "Email berhasil diperbarui!";
        }
        
        elseif ($action == 'update_address') {
            $address = $_POST['address'];
            $stmt = $conn->prepare("UPDATE users SET address = ? WHERE username = ?");
            $stmt->execute([$address, $username_lama]);
            $msg = "Alamat berhasil diperbarui!";
        }

        elseif ($action == 'update_username') {
            $new_username = $_POST['new_username'];
            
            $cek = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $cek->execute([$new_username]);
            if($cek->fetchColumn() > 0){
                throw new Exception("Username sudah digunakan orang lain!");
            }

            $conn->exec("SET FOREIGN_KEY_CHECKS=0");
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
            $stmt->execute([$new_username, $username_lama]);
            
            $stmt2 = $conn->prepare("UPDATE orders SET username = ? WHERE username = ?");
            $stmt2->execute([$new_username, $username_lama]);
            $conn->exec("SET FOREIGN_KEY_CHECKS=1");

            $_SESSION['username'] = $new_username;
            $msg = "Username berhasil diubah!";
        }

        elseif ($action == 'update_password') {
            $new_pass = $_POST['password'];
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$hashed, $username_lama]);
            $msg = "Password berhasil diubah!";
        }

        // Redirect Dinamis (Admin ke Admin, User ke Home)
        echo "<script>alert('$msg'); window.location.href='$redirect_url';</script>";

    } catch (Exception $e) {
        echo "<script>alert('Gagal update: " . $e->getMessage() . "'); window.location.href='$redirect_url';</script>";
    }
}
?>