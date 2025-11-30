<?php
session_start();
require_once 'Database/connection.php';

// --- LOGIKA MENGAMBIL DATA DARI DB BERDASARKAN SESSION ---
$products_in_cart = [];
$subtotal_all = 0;

// Cek apakah keranjang ada isinya
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    // Ambil semua ID produk dari session (contoh: ['item-01', 'item-03'])
    $ids = array_keys($_SESSION['cart']);
    
    // Buat placeholder (?) untuk query SQL sejumlah ID yang ada
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    // Ambil data produk dari database yang ID-nya ada di keranjang
    $sql = "SELECT * FROM inventory WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);
    $products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basket Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { padding-bottom: 80px; background-color: #f8f9fa; }
        .Navbar_icon{ width: 50px; height: auto; margin: 8px; }   
        .productCard {
            background: white;
            border-radius: 20px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .productImg {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }
        .productInfo { flex-grow: 1; }
        .qtyBox { display: flex; align-items: center; gap: 10px; }
        
        .qtyBtn {
            width: 35px; height: 35px; border: none; border-radius: 50%;
            background: #009944; color: white; font-size: 20px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; text-decoration: none;
        }
        .qtyBtn:hover { background: #007a36; color: white; }
        
        .numberDisplay {
            width: 40px; height: 35px; background: #ddd; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .footerBar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #009944; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        .confirmBtn {
            background: white; color: #009944; border: none;
            padding: 8px 30px; border-radius: 20px; font-weight: bold; cursor: pointer;
        }
        .deleteBtn {
            background: #dc3545; border: none; padding: 8px 20px;
            border-radius: 20px; font-weight: bold; cursor: pointer; color: white; text-decoration: none; font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <div class="d-flex">
                    <a href="Basket.php"><img src="Gambar/BasketIcon.png" class="Navbar_icon"></a>
                    <a href="MyOrder.php"><img src="Gambar/BoxIcon.png" class="Navbar_icon"></a>
                    <a href="Home.php"><img src="Gambar/HomeIcon.png" class="Navbar_icon"></a>
                </div>
            </div>
            <a href="#" class="ms-lg-auto"><img src="Gambar/Profile.png" class="Navbar_icon"></a>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?php if (empty($products_in_cart)): ?>
            <div class="text-center py-5">
                <img src="Gambar/BasketIcon.png" style="width: 100px; opacity: 0.3;">
                <h4 class="text-muted mt-3">Keranjangmu masih kosong</h4>
                <a href="Home.php" class="btn btn-success mt-2 rounded-pill">Belanja Sekarang</a>
            </div>
        <?php else: ?>
            
            <div id="productContainer">
                <?php foreach ($products_in_cart as $item): ?>
                    <?php 
                        // Ambil Qty dari Session
                        $qty = $_SESSION['cart'][$item['id']];
                        // Hitung Subtotal per item
                        $subtotal = $item['price'] * $qty;
                        // Tambah ke Grand Total
                        $subtotal_all += $subtotal;
                        
                        // Gambar
                        $imgSrc = !empty($item['image']) ? $item['image'] : 'Gambar/default.jpg';
                    ?>
                    
                    <div class="productCard">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="productImg" alt="Produk">
                        
                        <div class="productInfo">
                            <h5 class="mb-1 fw-bold"><?= htmlspecialchars($item['name']) ?></h5>
                            <small class="text-muted">
                                Rp <?= number_format($item['price'], 0, ',', '.') ?> x <?= $qty ?>
                            </small>
                            <div class="fw-bold text-success mt-1">
                                = Rp <?= number_format($subtotal, 0, ',', '.') ?>
                            </div>
                        </div>

                        <div class="qtyBox">
                            <a href="Database/cart_update.php?action=delete&id=<?= $item['id'] ?>" 
                               class="deleteBtn" onclick="return confirm('Hapus item ini?')">Delete</a>

                            <a href="Database/cart_update.php?action=minus&id=<?= $item['id'] ?>" class="qtyBtn">-</a>
                            
                            <div class="numberDisplay"><?= $qty ?></div>
                            
                            <a href="Database/cart_update.php?action=plus&id=<?= $item['id'] ?>" class="qtyBtn">+</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>

    <?php if (!empty($products_in_cart)): ?>
    <form action="Database/checkout.php" method="POST">
        <div class="footerBar">
            <div class="text-white">
                <small>Total Pembayaran:</small><br>
                <h4 class="fw-bold m-0">Rp <?= number_format($subtotal_all, 0, ',', '.') ?></h4>
            </div>

            <div class="d-flex gap-2">
                <select name="payment_method" id="paymentMethod" class="form-select w-auto" required>
                    <option value="" selected disabled>Pilih Payment</option>
                    <option value="COD">Cash On Delivery</option>
                    <option value="Transfer">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet</option>
                </select>
                <input type="hidden" name="total_price" value="<?= $subtotal_all ?>">
                <button type="submit" class="confirmBtn">CheckOut</button>
            </div>
        </div>
    </form>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>