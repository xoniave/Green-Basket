<?php
// --- FILE: admin.php ---

// 1. HUBUNGKAN KE DATABASE
require_once 'Database/connection.php';

session_start(); 

// Cek Login Admin
if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['role'] !== 'admin'){
    header("Location: Index.php");
    exit();
}

// 2. LOGIKA DATA
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

try {
    // --- A. AMBIL DATA ADMIN LENGKAP (BARU) ---
    // Kita butuh ini untuk menampilkan foto, nama, email di modal profile
    $admin_data = [];
    if(isset($_SESSION['username'])){
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt_user->execute([$_SESSION['username']]);
        $admin_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
    }

    // --- B. LOGIKA DASHBOARD & ORDERS ---
    $stmt_ord = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC");
    $stmt_ord->execute();
    $orders = $stmt_ord->fetchAll(PDO::FETCH_ASSOC);

    $stmt_inc = $conn->query("SELECT SUM(total) as total_income FROM orders WHERE status = 'Complete'");
    $income_data = $stmt_inc->fetch(PDO::FETCH_ASSOC);
    $total_income = $income_data['total_income'] ?? 0;

    $stmt_proc = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Process'");
    $total_process = $stmt_proc->fetchColumn(); 

    $stmt_comp = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Complete'");
    $total_complete = $stmt_comp->fetchColumn();

    // --- C. LOGIKA INVENTORY & KATEGORI ---
    $stmt_cat = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt_cat->execute();
    $categories_list = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    $cat_filter = isset($_GET['cat_filter']) ? $_GET['cat_filter'] : 'all';
    
    if ($cat_filter != 'all') {
        $stmt_inv = $conn->prepare("SELECT * FROM inventory WHERE category = ?");
        $stmt_inv->execute([$cat_filter]);
    } else {
        $stmt_inv = $conn->prepare("SELECT * FROM inventory");
        $stmt_inv->execute();
    }
    $inventory = $stmt_inv->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Helper Function Warna Status Order
function getStatusDot($status) {
    $color = 'secondary';
    $s = strtolower($status);
    if($s == 'complete' || $s == 'completed') $color = 'success';
    if($s == 'shipped') $color = 'primary';
    if($s == 'process' || $s == 'processing') $color = 'warning';
    return "<span class='status-dot bg-$color'></span> $status";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Basket Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root { --primary-green: #008751; --bg-light: #f3f4f6; }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        
        /* SIDEBAR */
        .sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: var(--primary-green); color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 25px; font-size: 1.1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #f8f9fa; color: var(--primary-green); border-left: 5px solid #005f38; }
        .logout-btn { background: white; color: var(--primary-green); font-weight: bold; border-radius: 50px; width: 90%; margin: 20px auto; display: block; }
        
        /* CONTENT */
        .main-content { width: 100%; padding: 0; }
        .top-navbar { background: white; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); background: white; padding: 25px; margin-bottom: 20px; }
        
        /* GENERAL STYLES */
        .stat-card { display: flex; align-items: center; justify-content: space-between; }
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .modal-content { border-radius: 25px; border: none; }
        .custom-input {
            background-color: white; border: none; border-radius: 15px; padding: 12px 20px;
            font-size: 0.95rem; color: #666; width: 100%; margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        
        /* --- STYLE BARU UNTUK PROFILE & MODAL (SAMA SEPERTI HOME) --- */
        .modal-logout-position { 
            margin-right: 20px; margin-top: 70px; margin-left: auto; max-width: 320px; 
        }
        .modal.fade .modal-dialog.modal-logout-position { transform: translateY(-30px); transition: transform 0.3s ease-out; }
        .modal.show .modal-dialog.modal-logout-position { transform: translateY(0); }

        /* Wrapper Foto Profil & Tombol Plus */
        .profile-pic-wrapper { position: relative; display: inline-block; margin-bottom: 15px; }
        .btn-upload-pic {
            position: absolute; bottom: 0; right: 0; width: 32px; height: 32px;
            background-color: #008751; color: white; border-radius: 50%; border: 2px solid white;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            font-weight: bold; font-size: 18px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: transform 0.2s;
        }
        .btn-upload-pic:hover { background-color: #006b40; transform: scale(1.1); }

        /* Inventory Styles */
        .product-img-box {
            background: white; width: 140px; height: 140px; border-radius: 20px; margin: 0 auto 20px auto;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .stock-wrapper { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; margin-bottom: 20px; }
        .stock-btn { background: none; border: none; font-size: 1.5rem; font-weight: bold; cursor: pointer; }
        .stock-display { background: white; width: 60px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666; }
        .btn-update-custom { background-color: #008751; color: white; border-radius: 30px; padding: 10px 30px; font-weight: bold; border: none; }
        .btn-file-upload { background-color: white; color: #aaa; border-radius: 10px; padding: 8px 15px; font-weight: bold; border: none; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column justify-content-between">
        <div>
            <div class="p-4 text-center">
                <i class="fas fa-leaf fa-3x mb-2"></i>
                <h3>Green Basket</h3>
            </div>
            <nav class="nav flex-column mt-2">
                <a class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>" href="?page=dashboard"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                <a class="nav-link <?= $page == 'orders' ? 'active' : '' ?>" href="?page=orders"><i class="fas fa-shopping-bag me-2"></i> Orders</a>
                <a class="nav-link <?= $page == 'inventory' ? 'active' : '' ?>" href="?page=inventory"><i class="fas fa-warehouse me-2"></i> Inventory</a>
                <a class="nav-link <?= $page == 'history' ? 'active' : '' ?>" href="?page=history"><i class="fas fa-clipboard-list me-2"></i> History</a>
            </nav>
        </div>
        <div class="pb-4">
            <a href="Database/logout.php" class="btn logout-btn">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <button class="btn btn-link text-dark p-0"><i class="fas fa-bars fa-lg"></i></button>
            
            <div class="d-flex align-items-center gap-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#userMenuModal">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold"><?= htmlspecialchars($admin_data['firstname'] ?? 'Admin') ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                
                <?php 
                    $nav_pic = 'Gambar/Profile.png'; 
                    if (!empty($admin_data['profile_pic']) && file_exists($admin_data['profile_pic'])) {
                        $nav_pic = $admin_data['profile_pic'];
                    }
                ?>
                <img src="<?= htmlspecialchars($nav_pic) ?>" 
                     width="50" height="50" 
                     class="rounded-circle border border-success" 
                     style="object-fit: cover;">
            </div>
        </div>

        <div class="container-fluid p-4">

            <?php if ($page == 'dashboard'): ?>
                <small class="text-muted fw-bold">Total Income</small>
                <h2 class="fw-bold mb-4">Rp. <?= number_format($total_income, 0, ',', '.') ?></h2>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div><h2 class="fw-bold"><?= count($orders) ?></h2><div class="text-muted">Total Order</div></div>
                            <i class="fas fa-shopping-cart fa-2x text-secondary"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div><h2 class="fw-bold"><?= $total_process ?></h2><div class="text-muted">Processing</div></div>
                            <i class="fas fa-sync fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div><h2 class="fw-bold"><?= $total_complete ?></h2><div class="text-muted">Completed</div></div>
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>

                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold">Recent Orders</h4>
                        <a href="?page=orders" class="btn btn-success rounded-pill btn-sm px-3">See All -></a>
                    </div>
                    <table class="table table-hover">
                        <thead><tr><th>User</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php $c=0; foreach($orders as $o): if($c++ >= 3) break; ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= htmlspecialchars($o['username']) ?></td>
                                <td><?= htmlspecialchars($o['order_date']) ?></td>
                                <td><?= getStatusDot($o['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page == 'orders'): ?>
                <div class="card-custom">
                    <h3 class="fw-bold mb-4">Orders Management</h3>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>ID</th><th>Date</th><th>Status</th><th>Payment</th><th>Total</th><th>User</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach($orders as $o): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?= htmlspecialchars($o['id']) ?></td>
                                    <td><?= htmlspecialchars($o['order_date']) ?></td>
                                    <td><?= getStatusDot($o['status']) ?></td>
                                    <td class="text-secondary fw-bold"><?= htmlspecialchars($o['payment_method']) ?></td>
                                    <td class="text-success fw-bold">Rp. <?= number_format($o['total'],0,',','.') ?></td>
                                    <td class="text-primary fw-bold"><?= htmlspecialchars($o['username']) ?></td>
                                    <td>
                                        <button class="btn btn-link text-secondary p-0 btn-edit-order" 
                                                data-id="<?= htmlspecialchars($o['id']) ?>" 
                                                data-status="<?= htmlspecialchars($o['status']) ?>">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form method="POST" action="Database/Order.php" style="display:inline;" onsubmit="return confirm('Hapus order ini?');">
                                            <input type="hidden" name="delete_order" value="1">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($o['id']) ?>">
                                            <button class="btn btn-link text-danger p-0 ms-2"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'inventory'): ?>
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h3 class="fw-bold m-0">Inventory</h3>
                        <div class="d-flex gap-2">
                            <form action="admin.php" method="GET" class="d-flex align-items-center">
                                <input type="hidden" name="page" value="inventory">
                                <select name="cat_filter" class="form-select rounded-pill me-2 border-success text-success fw-bold" onchange="this.form.submit()" style="min-width: 150px;">
                                    <option value="all">Semua Kategori</option>
                                    <?php foreach($categories_list as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['name']) ?>" <?= ($cat_filter == $cat['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <button class="btn btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="fas fa-tags me-1"></i> + Kategori</button>
                            <button class="btn btn-success rounded-pill px-4" id="btnAddItem"><i class="fas fa-plus me-2"></i> Add Item</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>SKU</th><th>Image</th><th>Kategori</th><th>Nama</th><th>Harga</th><th>Stock</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php if(count($inventory) > 0): ?>
                                    <?php foreach($inventory as $item): ?>
                                    <tr>
                                        <td class="text-muted small"><?= htmlspecialchars($item['sku']) ?></td>
                                        <td><img src="<?= htmlspecialchars(!empty($item['image']) ? $item['image'] : 'Gambar/default.jpg') ?>" width="40" height="40" class="rounded" style="object-fit:cover;"></td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($item['category']) ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                                        <td>Rp. <?= number_format($item['price'],0,',','.') ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($item['stock']) ?></td>
                                        <td>
                                            <button class="btn btn-link text-success p-0 btn-edit" 
                                                    data-sku="<?= htmlspecialchars($item['sku']) ?>" data-name="<?= htmlspecialchars($item['name']) ?>" 
                                                    data-price="<?= htmlspecialchars($item['price']) ?>" data-cat="<?= htmlspecialchars($item['category']) ?>" 
                                                    data-desc="<?= htmlspecialchars($item['description']) ?>" data-stock="<?= htmlspecialchars($item['stock']) ?>"
                                                    data-image="<?= htmlspecialchars($item['image']) ?? 'Gambar/default.jpg'?>">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form method="POST" action="Database/Inventory.php" style="display:inline;" onsubmit="return confirm('Hapus item?');">
                                                <input type="hidden" name="delete_item" value="1"><input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <button class="btn btn-link text-danger p-0 ms-2"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">Barang tidak ditemukan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'history'): ?>
                <div class="card-custom">
                    <h3 class="fw-bold mb-4">History Order (Selesai)</h3>
                    <table class="table table-hover">
                        <thead><tr><th>ID</th><th>Username</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach($orders as $o): if($o['status'] == 'Complete'): ?>
                            <tr>
                                <td><?= htmlspecialchars($o['id']) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($o['username']) ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($o['total'],0,',','.') ?></td>
                                <td><span class="badge bg-success">Complete</span></td>
                            </tr>
                            <?php endif; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="modal fade" id="userMenuModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-logout-position">
        <div class="modal-content shadow-lg" style="border-radius: 15px; border: none;">
            <div class="modal-header py-2" style="background-color: #008751; color: white;">
                <h6 class="modal-title">
                    Halo, <?= htmlspecialchars(($admin_data['firstname'] ?? '') . ' ' . ($admin_data['lastname'] ?? '')) ?>
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <div class="profile-pic-wrapper">
                    <?php 
                        $popup_pic = 'Gambar/Profile.png';
                        if (!empty($admin_data['profile_pic']) && file_exists($admin_data['profile_pic'])) {
                            $popup_pic = $admin_data['profile_pic'];
                        }
                    ?>
                    <img src="<?= htmlspecialchars($popup_pic) ?>" width="80" height="80" class="rounded-circle border border-2 border-success" style="object-fit: cover;">
                    <label for="fileInputProfile" class="btn-upload-pic">+</label>
                    <form action="Database/upload_photo.php" method="POST" enctype="multipart/form-data" id="formUploadPic">
                        <input type="file" name="new_profile_pic" id="fileInputProfile" style="display: none;" accept="image/*" onchange="document.getElementById('formUploadPic').submit()">
                    </form>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-user-edit me-2"></i> Edit Profile
                    </button>
                    <a href="Database/logout.php" class="btn btn-danger rounded-pill"><i class="fas fa-sign-out-alt me-2"></i> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius: 20px;">
            <div class="text-center mb-4 pb-3 border-bottom">
                <h5 class="text-muted small mb-1">Profile Admin</h5>
                <h3 class="fw-bold text-success m-0"><?= htmlspecialchars($admin_data['firstname'] ?? '') ?> <?= htmlspecialchars($admin_data['lastname'] ?? '') ?></h3>
            </div>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <div style="overflow: hidden;"><small class="text-muted d-block">Username</small><span class="fw-bold text-dark"><?= htmlspecialchars($admin_data['username'] ?? '-') ?></span></div>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editUsernameModal">Edit</button>
                </div>
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <div style="overflow: hidden;"><small class="text-muted d-block">Email</small><span class="fw-bold text-dark"><?= htmlspecialchars($admin_data['email'] ?? '-') ?></span></div>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editEmailModal">Edit</button>
                </div>
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <div><small class="text-muted d-block">Password</small><span class="fw-bold text-dark">******</span></div>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPasswordModal">Ubah</button>
                </div>
            </div>
            <div class="mt-4 text-center"><button type="button" class="btn btn-light rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#userMenuModal">Kembali</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUsernameModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content p-4"><h5 class="fw-bold mb-3">Ganti Username</h5><form action="Database/Profile.php" method="POST"><input type="hidden" name="action" value="update_username"><input type="text" name="new_username" class="custom-input mb-3" value="<?= htmlspecialchars($admin_data['username'] ?? '') ?>" required><div class="d-flex gap-2"><button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button><button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="editEmailModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4"><h5 class="fw-bold mb-3">Update Email</h5><form action="Database/Profile.php" method="POST"><input type="hidden" name="action" value="update_email"><input type="email" name="email" class="custom-input mb-3" value="<?= htmlspecialchars($admin_data['email'] ?? '') ?>" required><div class="d-flex gap-2"><button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button><button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="editPasswordModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4"><h5 class="fw-bold mb-3">Ganti Password</h5><form action="Database/Profile.php" method="POST"><input type="hidden" name="action" value="update_password"><input type="password" name="password" class="custom-input mb-3" placeholder="Password Baru..." required><div class="d-flex gap-2"><button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button><button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button></div></form></div></div></div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content p-4 text-center">
            <h5 class="fw-bold mb-3">Kategori Baru</h5>
            <form action="Database/Category.php" method="POST">
                <input type="text" name="category_name" class="custom-input text-center" placeholder="Nama Kategori..." required>
                <button type="submit" name="add_category" class="btn btn-success rounded-pill w-100 mt-2">Simpan</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <h5 id="modalTitle" class="fw-bold mb-3 text-center">Edit Item</h5>
            <div class="product-img-box"><img id="modalImagePreview" src="Gambar/default.jpg" style="opacity: 0.9;"></div>
            <form id="editForm" method="POST" action="Database/Inventory.php" enctype="multipart/form-data">
                <input type="text" id="modalSku" class="custom-input" placeholder="SKU (Kode Barang)" name="sku" required>
                <select id="modalCat" class="custom-input form-select" name="kategori" required>
                    <option value="" disabled selected>Pilih Kategori...</option>
                    <?php foreach($categories_list as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="modalName" class="custom-input" placeholder="Nama Produk" name="nama" required>
                <textarea id="modalDesc" class="custom-input" rows="3" placeholder="Deskripsi.." style="resize: none;" name="description"></textarea>
                <input type="number" id="modalPrice" class="custom-input" placeholder="Harga (Rp)" name="harga" required>
                <input type="file" id="fileUpload" style="display: none;" accept="image/*" name="product_image">
                <div class="stock-wrapper">
                    <span class="fw-bold text-secondary">Stock</span>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="stock-btn" id="btnMinus">–</button>
                        <div class="stock-display" id="displayStock">0</div>
                        <button type="button" class="stock-btn" id="btnPlus">+</button>
                        <input type="hidden" id="modalStock" name="stock" value="0"> 
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn-file-upload shadow-sm" onclick="$('#fileUpload').click()"><i class="fas fa-camera me-1"></i> Upload Foto</button>
                    <button type="submit" name="update_item" class="btn-update-custom shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content p-4 text-center">
            <h5 class="fw-bold mb-3">Update Order</h5>
            <form method="POST" action="Database/Order.php">
                <input type="hidden" id="orderId" name="order_id">
                <div class="mb-3">
                    <label class="form-label text-muted small">Status Pesanan</label>
                    <select id="orderStatus" class="form-select custom-input text-center" name="status">
                        <option value="Process">Process</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Complete">Complete</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_order" class="btn btn-primary rounded-pill w-100">Update Status</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. LOGIC EDIT ITEM
        $('.btn-edit').click(function() {
            document.getElementById("modalTitle").innerText = "Edit Item";
            document.getElementById("editForm").setAttribute("action", "Database/Inventory.php");
            let btn = document.querySelector("#editForm button[type='submit']");
            btn.name = "update_item"; btn.innerText = "Update";
            $('#modalSku').val($(this).data('sku')).prop('readonly', true);
            $('#modalName').val($(this).data('name'));
            $('#modalCat').val($(this).data('cat'));
            $('#modalDesc').val($(this).data('desc'));
            $('#modalPrice').val($(this).data('price'));
            let stock = $(this).data('stock');
            $('#modalStock').val(stock); $('#displayStock').text(stock);
            $('#modalImagePreview').attr('src', $(this).data('image'));
            $('#editModal').modal('show');
        });

        // 2. LOGIC ADD ITEM
        $('#btnAddItem').click(function() {
            document.getElementById("modalTitle").innerText = "Tambah Item Baru";
            document.getElementById("editForm").setAttribute("action", "Database/Inventory.php");
            let btn = document.querySelector("#editForm button[type='submit']");
            btn.name = "add_item"; btn.innerText = "Tambah";
            $('#modalSku').val('').prop('readonly', false);
            $('#modalName').val(''); $('#modalCat').val(''); $('#modalDesc').val(''); $('#modalPrice').val('');
            $('#modalStock').val('0'); $('#displayStock').text('0');
            $('#modalImagePreview').attr('src', 'Gambar/default.jpg');
            $('#editModal').modal('show');
        });

        // 3. LOGIC STOCK
        $('#btnPlus').click(function() { let val = parseInt($('#modalStock').val()) || 0; val++; updateStockUI(val); });
        $('#btnMinus').click(function() { let val = parseInt($('#modalStock').val()) || 0; if(val > 0) val--; updateStockUI(val); });
        function updateStockUI(val) { $('#modalStock').val(val); $('#displayStock').text(val); }

        // 4. PREVIEW IMAGE
        $('#fileUpload').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { $('#modalImagePreview').attr('src', e.target.result); }
                reader.readAsDataURL(file);
            }
        });

        // 5. LOGIC EDIT ORDER
        $('.btn-edit-order').click(function() {
            $('#orderId').val($(this).data('id'));
            $('#orderStatus').val($(this).data('status'));
            $('#orderModal').modal('show');
        });
    });
</script>

</body>
</html>