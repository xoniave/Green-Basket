<?php
session_start();
require_once 'Database/connection.php';

// Cek Login
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: Index.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil data order
$sql = "SELECT o.*, i.name as product_name, i.image as product_image 
        FROM orders o 
        LEFT JOIN inventory i ON o.item_id = i.id 
        WHERE o.username = :user 
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([':user' => $username]);
$all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pisahkan status
$process_orders = [];
$complete_orders = [];

foreach ($all_orders as $order) {
    if (strtolower($order['status']) == 'complete' || strtolower($order['status']) == 'completed') {
        $complete_orders[] = $order;
    } else {
        $process_orders[] = $order; 
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pesanan Saya - Green Basket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
  :root{ --green-dark:#008751; --bg:#F8F9FA; }
  body{ background:var(--bg); font-family: sans-serif; }
  
  /* ICON NAVBAR */
  .Navbar_icon { width: 50px; height: auto; margin: 0 10px; }

  /* TABS STYLE */
  .nav-tabs .nav-link { color: #666; font-weight: bold; border: none; border-bottom: 3px solid transparent; width: 100%; text-align: center; }
  .nav-tabs .nav-link.active { color: var(--green-dark); background: transparent; border-bottom: 3px solid var(--green-dark); }
  .nav-item { flex: 1; }

  /* ORDER CARD STYLE */
  .order-card {
      background: white; border-radius: 15px; padding: 15px; margin-bottom: 15px;
      display: flex; gap: 15px; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      transition: transform 0.2s;
  }
  .order-card:hover { transform: translateY(-3px); }
  
  .order-img {
      width: 80px; height: 80px; border-radius: 10px; object-fit: cover; background: #eee;
      border: 1px solid #eee;
  }
  
  /* BADGE STATUS */
  .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
  .bg-process { background-color: #fff3cd; color: #856404; }
  .bg-complete { background-color: #d4edda; color: #155724; }

  /* MODAL LOGOUT CSS */
  .modal-logout-position { margin-right: 20px; margin-top: 70px; margin-left: auto; max-width: 320px; }
  .modal.fade .modal-dialog.modal-logout-position { transform: translateY(-50px); transition: transform 0.3s ease-out; }
  .modal.show .modal-dialog.modal-logout-position { transform: translateY(0); }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top mb-4">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <div class="d-flex align-items-center py-2">
                <a href="Basket.php"><img src="Gambar/BasketIcon.png" alt="Basket" class="Navbar_icon"></a>
                <a href="MyOrder.php"><img src="Gambar/BoxIcon.png" alt="Delivery" class="Navbar_icon"></a> 
                <a href="Home.php"><img src="Gambar/HomeIcon.png" alt="Home" class="Navbar_icon"></a>
            </div>
        </div>

        <form class="d-flex mx-auto my-2 my-lg-0" role="search" style="width: 50%;" action="Home.php" method="GET">
            <input name="q" class="form-control me-2 rounded-pill" type="search" placeholder="Cari sayur, buah..." >
            <button class="btn btn-outline-success rounded-pill" type="submit">Search</button>
        </form>

        <a href="#" class="ms-lg-auto" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <img src="Gambar/Profile.png" alt="Profile" class="Navbar_icon">
        </a>
    </div>
</nav>

<div class="container pb-5">
    <h3 class="fw-bold text-success mb-4"><i class="fas fa-clipboard-list me-2"></i>Pesanan Saya</h3>

    <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="process-tab" data-bs-toggle="tab" data-bs-target="#process-content" type="button">Di Proses</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="complete-tab" data-bs-toggle="tab" data-bs-target="#complete-content" type="button">Selesai</button>
        </li>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="process-content">
            <?php if(empty($process_orders)): ?>
                <div class="text-center py-5">
                    <img src="Gambar/BoxIcon.png" style="width: 80px; opacity: 0.2; margin-bottom: 15px;">
                    <h5 class="text-muted">Tidak ada pesanan yang sedang diproses.</h5>
                    <a href="Home.php" class="btn btn-success rounded-pill mt-3">Belanja Sekarang</a>
                </div>
            <?php else: ?>
                <?php foreach($process_orders as $o): ?>
                    <div class="order-card">
                        <img src="<?= htmlspecialchars(!empty($o['product_image']) ? $o['product_image'] : 'Gambar/default.jpg') ?>" class="order-img">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold m-0 text-success"><?= htmlspecialchars($o['id']) ?></h6>
                                <span class="badge-status bg-process"><?= htmlspecialchars($o['status']) ?></span>
                            </div>
                            <p class="mb-1 fw-bold text-dark"><?= htmlspecialchars($o['product_name'] ?? 'Item Produk') ?></p>
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <div class="small text-muted mb-1">Via: <?= htmlspecialchars($o['payment_method']) ?></div>
                                    <div class="fw-bold text-dark">Rp <?= number_format($o['total'], 0, ',', '.') ?></div>
                                </div>
                                <small class="text-secondary" style="font-size: 0.7rem;">
                                    <?= date('d M Y', strtotime($o['order_date'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="complete-content">
            <?php if(empty($complete_orders)): ?>
                <div class="text-center py-5">
                    <img src="Gambar/BoxIcon.png" style="width: 80px; opacity: 0.2; margin-bottom: 15px;">
                    <h5 class="text-muted">Belum ada riwayat pesanan selesai.</h5>
                </div>
            <?php else: ?>
                <?php foreach($complete_orders as $o): ?>
                    <div class="order-card" style="opacity: 0.8;">
                        <img src="<?= htmlspecialchars(!empty($o['product_image']) ? $o['product_image'] : 'Gambar/default.jpg') ?>" class="order-img">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold m-0 text-dark"><?= htmlspecialchars($o['id']) ?></h6>
                                <span class="badge-status bg-complete"><?= htmlspecialchars($o['status']) ?></span>
                            </div>
                            <p class="mb-1 fw-bold text-dark"><?= htmlspecialchars($o['product_name'] ?? 'Item Produk') ?></p>
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="fw-bold text-dark">Rp <?= number_format($o['total'], 0, ',', '.') ?></div>
                                <small class="text-secondary" style="font-size: 0.7rem;">
                                    <?= date('d M Y', strtotime($o['order_date'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-logout-position">
        <div class="modal-content shadow-lg" style="border-radius: 15px; border: none;">
            <div class="modal-header py-2" style="background-color: #008751; color: white;">
                <h6 class="modal-title">Konfirmasi</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img src="Gambar/Profile.png" width="60" class="rounded-circle border border-2 border-success mb-2" style="margin: 0 auto;">
                <p class="mb-1 small">Halo, <strong><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?></strong></p>
                <p class="mb-3">Yakin ingin keluar?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <a href="Database/logout.php" class="btn btn-sm btn-danger rounded-pill px-3">Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>