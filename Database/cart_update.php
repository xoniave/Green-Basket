<?php
session_start();
require_once 'connection.php'; // Butuh koneksi DB

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if (isset($_SESSION['cart'][$id])) {
        
        if ($action == 'plus') {
            // CEK STOK DULU SEBELUM NAMBAH
            $stmt = $conn->prepare("SELECT stock FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            $stock = $stmt->fetchColumn();

            $current_qty = $_SESSION['cart'][$id];

            if ($current_qty < $stock) {
                $_SESSION['cart'][$id]++;
            } else {
                // Opsional: Bisa kasih alert lewat session flash message kalau mau
                // Tapi dibiarkan diam juga tidak apa-apa (tombol tidak nambah)
            }
        } 
        elseif ($action == 'minus') {
            if ($_SESSION['cart'][$id] > 1) {
                $_SESSION['cart'][$id]--;
            }
        } 
        elseif ($action == 'delete') {
            unset($_SESSION['cart'][$id]);
        }
    }
}

header("Location: ../Basket.php");
exit();
?>