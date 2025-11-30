<?php
session_start();
require_once 'connection.php';

// Pastikan user login
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../Index.php?error=not_logged_in");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    
    $username = $_SESSION['username'];
    $payment_method = $_POST['payment_method'];
    $total_price = $_POST['total_price'];
    $status = 'Process';
    $order_date = date('Y-m-d');
    
    // Generate ID Order Unik 6 Karakter
    $random_str = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    $order_id = 'ORD-' . $random_str;

    // Ambil item pertama sebagai perwakilan ID (karena struktur tabelmu sederhana)
    $first_item_id = array_key_first($_SESSION['cart']); 

    try {
        // 1. MULAI TRANSAKSI
        $conn->beginTransaction();

        // 2. CEK STOK & KURANGI STOK (Looping semua barang di keranjang)
        foreach ($_SESSION['cart'] as $prod_id => $buy_qty) {
            
            // Ambil stok saat ini dari DB (Gunakan FOR UPDATE untuk mengunci baris agar aman)
            $stmt = $conn->prepare("SELECT name, stock FROM inventory WHERE id = ? FOR UPDATE");
            $stmt->execute([$prod_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Produk dengan ID $prod_id tidak ditemukan.");
            }

            // Validasi: Apakah stok cukup?
            if ($product['stock'] < $buy_qty) {
                throw new Exception("Stok untuk '{$product['name']}' tidak cukup! Sisa: {$product['stock']}, Kamu minta: $buy_qty");
            }

            // Kurangi Stok
            $new_stock = $product['stock'] - $buy_qty;
            $update_stmt = $conn->prepare("UPDATE inventory SET stock = ? WHERE id = ?");
            $update_stmt->execute([$new_stock, $prod_id]);
        }

        // 3. SIMPAN DATA ORDER
        $sql = "INSERT INTO orders (id, item_id, order_date, status, payment_method, total, username) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id, $first_item_id, $order_date, $status, $payment_method, $total_price, $username]);

        // 4. BERHASIL: COMMIT TRANSAKSI & KOSONGKAN KERANJANG
        $conn->commit();
        unset($_SESSION['cart']);
        
        echo "<script>alert('Pesanan berhasil! Stok telah diperbarui.'); window.location.href='../MyOrder.php';</script>";

    } catch (Exception $e) {
        // 5. GAGAL: ROLLBACK (Batalkan semua perubahan stok & order)
        $conn->rollBack();
        
        // Kembalikan ke keranjang dengan pesan error
        echo "<script>alert('Gagal Checkout: " . $e->getMessage() . "'); window.location.href='../Basket.php';</script>";
    }
} else {
    header("Location: ../Basket.php");
}
?>