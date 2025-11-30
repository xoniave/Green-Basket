<?php
session_start();
require_once 'connection.php'; // Kita butuh koneksi DB untuk cek stok real-time

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id = $_POST['id'];
    $qty_to_add = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    // 1. Cek Stok di Database dulu
    $stmt = $conn->prepare("SELECT stock FROM inventory WHERE id = ?");
    $stmt->execute([$id]);
    $db_stock = $stmt->fetchColumn();

    if ($db_stock === false) {
        http_response_code(404);
        echo "Produk tidak ditemukan";
        exit;
    }

    // 2. Hitung jumlah yang akan ada di keranjang nanti
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
    
    $current_qty_in_cart = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0;
    $total_qty_needed = $current_qty_in_cart + $qty_to_add;

    // 3. Validasi: Jangan biarkan masuk kalau melebihi stok
    if ($total_qty_needed > $db_stock) {
        // Kirim kode error khusus (400 Bad Request)
        http_response_code(400); 
        echo "Stok tidak cukup! Sisa stok hanya: $db_stock";
        exit;
    }

    // 4. Kalau aman, baru update session
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty_to_add;
    } else {
        $_SESSION['cart'][$id] = $qty_to_add;
    }

    echo "success";

} else {
    http_response_code(405); 
}
?>