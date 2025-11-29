<?php
// --- FILE: admin.php ---

// 1. HUBUNGKAN KE FILE KONEKSI KAMU
require_once 'Database/connection.php';

// 2. LOGIKA HALAMAN
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// 3. AMBIL DATA DARI DATABASE (MENGGUNAKAN PDO)

try {
    // 1. Ambil data Inventory
    $stmt_inv = $conn->prepare("SELECT * FROM inventory");
    $stmt_inv->execute();
    $inventory = $stmt_inv->fetchAll(PDO::FETCH_ASSOC);

    // 2. Ambil data Orders (Semua)
    $stmt_ord = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC"); // Ditambah ORDER BY biar yang terbaru diatas
    $stmt_ord->execute();
    $orders = $stmt_ord->fetchAll(PDO::FETCH_ASSOC);

    // 3. Hitung Total Income (Hanya yang statusnya 'Complete')
    $stmt_inc = $conn->prepare("SELECT SUM(total) as total_income FROM orders WHERE status = 'Complete'");
    $stmt_inc->execute();
    $income_data = $stmt_inc->fetch(PDO::FETCH_ASSOC);
    $total_income = $income_data['total_income'] ? $income_data['total_income'] : 0;

    // --- BARU: HITUNG JUMLAH STATUS ---
    
    // 4. Hitung Jumlah 'Process'
    $stmt_proc = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = 'Process'");
    $stmt_proc->execute();
    $total_process = $stmt_proc->fetchColumn(); // Mengambil hasil angka langsung

    // 5. Hitung Jumlah 'Complete'
    $stmt_comp = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = 'Complete'");
    $stmt_comp->execute();
    $total_complete = $stmt_comp->fetchColumn(); // Mengambil hasil angka langsung

} catch(PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    // Set default jika error agar tidak warning di HTML
    $inventory = []; $orders = []; 
    $total_income = 0; $total_process = 0; $total_complete = 0;
}
// Helper Function Warna Status
function getStatusDot($status) {
    $color = 'secondary';
    if(strtolower($status) == 'complete' || strtolower($status) == 'completed') $color = 'success';
    if(strtolower($status) == 'shipped') $color = 'primary';
    if(strtolower($status) == 'process') $color = 'danger';
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
        :root {
            --primary-green: #008751; 
            --bg-light: #f3f4f6;
        }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        
        /* --- SIDEBAR & LAYOUT --- */
        .sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: var(--primary-green); color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 25px; font-size: 1.1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #f8f9fa; color: var(--primary-green); border-left: 5px solid #005f38; }
        .logout-btn { background: white; color: var(--primary-green); font-weight: bold; border-radius: 50px; width: 90%; margin: 20px auto; display: block; }
        .main-content { width: 100%; padding: 0; }
        .top-navbar { background: white; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        /* --- GENERAL ELEMENTS --- */
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); background: white; padding: 25px; margin-bottom: 20px; }
        .stat-card { display: flex; align-items: center; justify-content: space-between; }
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        
        /* --- MODAL EDIT STYLING --- */
        .modal-content {
            border-radius: 25px; 
            background-color: #efefef;
            border: none; overflow: hidden;
        }
        .custom-input {
            background-color: white; border: none;
            border-radius: 15px; padding: 12px 20px;
            font-size: 0.95rem; color: #666; width: 100%;
            margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .custom-input:focus { outline: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        
        .product-img-box {
            background: white; width: 140px; height: 140px;
            border-radius: 20px; margin: 0 auto 20px auto;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .stock-wrapper { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; margin-bottom: 20px; }
        .stock-controls { display: flex; align-items: center; gap: 15px; }
        .stock-btn { background: none; border: none; font-size: 1.5rem; font-weight: bold; cursor: pointer; }
        .stock-display {
            background: white; width: 60px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: #666;
        }

        .btn-file-upload { background-color: white; color: #aaa; border-radius: 10px; padding: 8px 15px; font-weight: bold; border: none; font-size: 0.9rem; }
        .btn-update-custom {
            background-color: #008751; color: white;
            border-radius: 30px; padding: 10px 30px;
            font-weight: bold; border: none;
        }
        .btn-update-custom:hover { background-color: #006b40; color: white; }
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
                <a class="nav-link <?= $page == 'account' ? 'active' : '' ?>" href="?page=account"><i class="fas fa-user me-2"></i> Account</a>
            </nav>
        </div>
        <div class="pb-4">
            <button class="btn logout-btn">Log Out</button>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0"><i class="fas fa-bars fa-lg"></i></button>
                <div class="bg-light rounded-pill px-3 py-2" style="width:300px">
                    <input type="text" placeholder="Search..." class="border-0 bg-transparent w-100" style="outline:none">
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold">Selyn</div>
                    <small class="text-muted">Admin</small>
                </div>
                <div class="rounded-circle border border-success p-1">
                    <i class="fas fa-user-circle fa-2x text-success"></i>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">

            <?php if ($page == 'dashboard'): ?>
                <small class="text-muted fw-bold">income</small>
                <h2 class="fw-bold mb-4">Rp. <?= number_format($total_income, 0, ',', '.') ?></h2>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div>
                                <h2 class="fw-bold"><?= count($orders) ?></h2>
                                <div class="text-muted">Order</div>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div>
                                <h2 class="fw-bold"><?= $total_process ?></h2> 
                                <div class="text-muted">Processing</div>
                            </div>
                            <i class="fas fa-sync fa-2x text-muted"></i>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-custom stat-card">
                            <div>
                                <h2 class="fw-bold"><?= $total_complete ?></h2>
                                <div class="text-muted">Complete</div>
                            </div>
                            <i class="fas fa-star fa-2x text-success"></i>
                        </div>
                    </div>
                </div>

                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold">Recent Orders</h4>
                        <a href="?page=orders" class="btn btn-success rounded-pill btn-sm px-3">See All -></a>
                    </div>
                    <table class="table table-hover">
                        <thead><tr><th>Username</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php 
                            $count = 0;
                            foreach($orders as $o): 
                                if($count >= 3) break;
                            ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= htmlspecialchars($o['username']) ?></td>
                                <td><?= htmlspecialchars($o['order_date']) ?></td>
                                <td><?= getStatusDot($o['status']) ?></td>
                            </tr>
                            <?php 
                                $count++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>

           <?php elseif ($page == 'orders'): ?>
                <div class="card-custom">
                    <h3 class="fw-bold mb-4">Orders</h3>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item ID</th> <th>TGL</th>
                                    <th>Status</th>
                                    <th>PM</th>
                                    <th>Total</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $o): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?= htmlspecialchars($o['id']) ?></td>
                                    
                                    <td class="fw-bold text-dark">
                                        <?= isset($o['item_id']) ? htmlspecialchars($o['item_id']) : '-' ?>
                                    </td>
                                    
                                    <td><?= htmlspecialchars($o['order_date']) ?></td>
                                    <td class="fw-bold"><?= getStatusDot($o['status']) ?></td>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($o['payment_method']) ?></td>
                                    <td class="fw-bold text-secondary">Rp. <?= number_format($o['total'],0,',','.') ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($o['username']) ?></td>
                                    <td>
                                        <button class="btn btn-link text-secondary p-0 me-2"><i class="fas fa-trash"></i></button>
                                        <button class="btn btn-link text-secondary p-0"><i class="fas fa-pen"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'inventory'): ?>
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold">Inventory</h3>
                        <select class="form-select w-auto rounded-pill bg-light border-0"><option>Kategori</option></select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>ID</th><th>SKU</th><th>Kategori</th><th>Nama</th><th>Harga</th><th>Stock</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach($inventory as $item): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?= htmlspecialchars($item['id']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($item['sku']) ?></td>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($item['category']) ?></td>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="fw-bold text-secondary">Rp. <?= number_format($item['price'],0,',','.') ?></td>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($item['stock']) ?></td>
                                    <td>
                                        <button class="btn btn-link text-secondary p-0 me-2"><i class="fas fa-trash"></i></button>
                                        <button class="btn btn-link text-secondary p-0 btn-edit" 
                                                data-sku="<?= htmlspecialchars($item['sku']) ?>" 
                                                data-name="<?= htmlspecialchars($item['name']) ?>" 
                                                data-price="<?= htmlspecialchars($item['price']) ?>"
                                                data-cat="<?= htmlspecialchars($item['category']) ?>"
                                                data-desc="<?= htmlspecialchars($item['description']) ?>"
                                                data-stock="<?= htmlspecialchars($item['stock']) ?>"
                                                data-image="<?= htmlspecialchars($item['image']) ?? 'Gambar/default.jpg'?>">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'history'): ?>
                <div class="card-custom" style="min-height: 80vh; display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fw-bold">History</h3>
                            <select class="form-select w-auto rounded-pill bg-light border-0"><option>Kategori</option></select>
                        </div>
                        <table class="table table-hover">
                            <thead><tr><th>ID</th><th>Username</th><th>Status</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach($orders as $o): 
                                    if($o['status'] == 'Complete'): // Hanya tampilkan yang complete
                                ?>
                                <tr>
                                    <td class="text-muted"><?= htmlspecialchars($o['id']) ?></td>
                                    <td class="text-primary fw-bold"><?= htmlspecialchars($o['username']) ?></td>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($o['status']) ?></td>
                                    <td class="fw-bold text-secondary"><?= number_format($o['total'],0,',','.') ?></td>
                                </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="border-top pt-3 d-flex justify-content-end gap-5 fw-bold text-secondary">
                        <span>Total Income :</span><span>Rp. <?= number_format($total_income, 0, ',', '.') ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-custom text-center">
                    <h3>Account Page</h3>
                    <p>Halaman pengaturan akun admin.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            
            <div class="product-img-box">
                <img id="modalImagePreview" src="" width="100" style="opacity: 0.8;">
            </div>

            <!--  ini nyambung ke php database inventory karena ini modal untuk Inventory -->
            <form id="editForm" method="POST" action="Database/Inventory.php" enctype="multipart/form-data"> 
                <input type="text" id="modalSku" class="custom-input" placeholder="SKU" name = "sku">
                <input type="text" id="modalCat" class="custom-input" placeholder="Kategori" name = "kategori">
                <input type="text" id="modalName" class="custom-input" placeholder="Nama" name = "nama">
                <textarea id="modalDesc" class="custom-input" rows="3" placeholder="Deskripsi.." style="border-radius: 15px; resize: none;" name = "description"></textarea>
                <input type="text" id="modalPrice" class="custom-input" placeholder="Harga" name = "harga">
                <input type="file" id="fileUpload" style="display: none;" accept="image/*" name="product_image">

                <div class="stock-wrapper">
                    <span class="fw-bold text-secondary" style="font-size: 1.1rem;">Stock</span>
                    <div class="stock-controls">
                        <button type="button" class="stock-btn" id="btnMinus">–</button>
                        <div class="stock-display" id="displayStock">0</div>
                        <button type="button" class="stock-btn" id="btnPlus">+</button>
                        <input type="hidden" id="modalStock" name="stock"> 
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="button" class="btn-file-upload shadow-sm">
                        File Upload <i class="fas fa-caret-down ms-1"></i>
                    </button>
                    <button type="submit" name="update_item" class="btn-update-custom shadow">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Logic Buka Modal
        $('.btn-edit').click(function() {
            const sku = $(this).data('sku');
            const name = $(this).data('name');
            const cat = $(this).data('cat');
            const desc = $(this).data('desc');
            const price = $(this).data('price');
            const stock = $(this).data('stock');
            let imagePath = $(this).data('image');

            $('#modalSku').val(sku);
            $('#modalName').val(name);
            $('#modalCat').val(cat);
            $('#modalDesc').val(desc);
            $('#modalPrice').val(price);
            
            $('#modalStock').val(stock);
            $('#displayStock').text(stock);
            $('#modalImagePreview').attr('src', imagePath);
            
            $('#editModal').modal('show');
        });

        // Logic Plus Minus Stock
        $('#btnPlus').click(function() {
            let val = parseInt($('#modalStock').val()) || 0;
            val++;
            updateStockUI(val);
        });

        $('#btnMinus').click(function() {
            let val = parseInt($('#modalStock').val()) || 0;
            if(val > 0) val--;
            updateStockUI(val);
        });

        function updateStockUI(val) {
            $('#modalStock').val(val);
            $('#displayStock').text(val);
        }

        $('.btn-file-upload').click(function(e) {
            e.preventDefault(); 
            $('#fileUpload').click(); 
        });
        

        $('#fileUpload').change(function() {
        const file = this.files[0]; // Mengambil file pertama yang dipilih
        
        if (file) {
            // Objek FileReader memungkinkan aplikasi web membaca konten file secara asinkron
            const reader = new FileReader(); 
            
            // Ketika FileReader selesai membaca file
            reader.onload = function(e) {
                // Mengubah atribut 'src' dari tag <img> di modal menjadi URL data file yang baru dipilih
                $('#modalImagePreview').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(file); 
        }
    });
    });
</script>

</body>
</html>