<?php
session_start();
require_once 'Database/connection.php';

// Cek Login
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: Index.php");
    exit();
}

// --- AMBIL DATA USER LENGKAP ---
$user_data = [];
if (isset($_SESSION['username'])) {
    try {
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt_user->execute([$_SESSION['username']]);
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            header("Location: Database/logout.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// --- LOGIKA FILTER PRODUK ---
$where_clauses = [];
$params = [];
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $where_clauses[] = "(name LIKE :q OR description LIKE :q)";
    $params[':q'] = "%" . $_GET['q'] . "%";
}
if (isset($_GET['cat']) && !empty($_GET['cat']) && $_GET['cat'] != 'all') {
    $where_clauses[] = "category = :cat";
    $params[':cat'] = $_GET['cat'];
}
$sql = "SELECT * FROM inventory";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt_cat = $conn->query("SELECT DISTINCT category FROM inventory ORDER BY category ASC");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $products = [];
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - Green Basket</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .Navbar_icon {
            width: 50px;
            height: auto;
            margin: 0 10px;
        }

        /* Header Image */
        .headerContainer {
            position: relative;
            width: 100%;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .headerphoto {
            width: 100%;
            height: auto;
            min-height: 250px;
            object-fit: cover;
        }

        .headerElement {
            position: absolute;
            top: 50%;
            left: 8%;
            transform: translateY(-50%);
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .headerButton {
            color: green;
            border-radius: 50px;
            background-color: white;
            font-size: 1.2rem;
            padding: 5px 20px;
            font-weight: bold;
            border: none;
            margin-top: 10px;
        }

        /* Product Card */
        .product-card {
            transition: transform 0.2s;
            height: 100%;
            border: 1px solid #008751;
            border-radius: 10px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-img-wrapper {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            padding: 10px;
        }

        .card-img-top {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        /* Modal Positioning */
        .modal-logout-position {
            margin-right: 20px;
            margin-top: 80px;
            margin-left: auto;
            max-width: 320px;
        }

        .modal.fade .modal-dialog.modal-logout-position {
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog.modal-logout-position {
            transform: translateY(0);
        }

        .user-name-nav {
            font-weight: bold;
            color: #008751;
            margin-right: 5px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .custom-input {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px 15px;
            width: 100%;
            margin-bottom: 10px;
        }

        /* Style Upload Foto (+) */
        .profile-pic-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }

        .btn-upload-pic {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background-color: #008751;
            color: white;
            border-radius: 50%;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .btn-upload-pic:hover {
            background-color: #006b40;
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container-fluid">

            <button class="navbar-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">

                <div class="d-flex align-items-center py-2">
                    <a href="Basket.php"><img src="Gambar/BasketIcon.png" alt="Basket" class="Navbar_icon"></a>
                    <a href="MyOrder.php"><img src="Gambar/BoxIcon.png" alt="Delivery" class="Navbar_icon"></a>
                    <a href="Home.php"><img src="Gambar/HomeIcon.png" alt="Home" class="Navbar_icon"></a>
                </div>

                <form class="d-flex ms-3 my-2 my-lg-0" role="search" style="width: 100%; max-width: 400px;" action="Home.php" method="GET">
                    <input name="q" class="form-control me-2 rounded-pill" type="search" placeholder="Cari sayur, buah..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                    <button class="btn btn-outline-success rounded-pill" type="submit">Search</button>
                </form>

                <div class="d-flex align-items-center ms-lg-auto mt-2 mt-lg-0" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#userMenuModal">
                    <span class="user-name-nav d-none d-md-block">
                        <?= htmlspecialchars($user_data['username']) ?>
                    </span>

                    <?php
                    $nav_pic = 'Gambar/Profile.png'; // Default
                    if (!empty($user_data['profile_pic']) && file_exists($user_data['profile_pic'])) {
                        $nav_pic = $user_data['profile_pic'];
                    }
                    ?>
                    <img src="<?= htmlspecialchars($nav_pic) ?>" alt="Profile" class="Navbar_icon" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                </div>

            </div>
        </div>
    </nav>

    <div class="headerContainer">
        <img src="Gambar/headerPhoto.png" alt="Header Photo" class="headerphoto">
        <div class="headerElement">
            <h1 style="font-weight: bold;">Fresh & Healthy</h1>
            <h3>Organic Food</h3>
            <button class="headerButton">Shop Now</button>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row align-items-center">
            <div class="col-md-3">
                <h2 style="color: #008751; font-weight: bold;">Category</h2>
            </div>
            <div class="col-md-3">
                <form action="Home.php" method="GET">
                    <?php if (isset($_GET['q'])) : ?><input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>"><?php endif; ?>
                    <select name="cat" class="form-select border-success text-success fw-bold" onchange="this.form.submit()">
                        <option value="all">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($_GET['cat']) && $_GET['cat'] == $cat) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($cat)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $item): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card bg-white position-relative">
                            <div class="card-img-wrapper">
                                <?php $imgSrc = !empty($item['image']) ? $item['image'] : 'Gambar/default.jpg'; ?>
                                <img src="<?= htmlspecialchars($imgSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>

                            <div class="p-3">
                                <small class="text-muted text-uppercase"><?= htmlspecialchars($item['category']) ?></small>
                                <h5 class="fw-bold text-success text-truncate"><?= htmlspecialchars($item['name']) ?></h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">Rp. <?= number_format($item['price'], 0, ',', '.') ?></h6>
                                    <?php if ($item['stock'] > 0): ?>
                                        <small class="text-muted" style="font-size: 0.85rem;">Stok: <strong><?= $item['stock'] ?></strong></small>
                                    <?php else: ?>
                                        <small class="text-danger fw-bold" style="font-size: 0.85rem;">Habis</small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid">
                                    <?php if ($item['stock'] > 0): ?>
                                        <button class="btn btn-success rounded-pill fw-bold btn-add-cart"
                                            data-id="<?= $item['id'] ?>"
                                            data-name="<?= htmlspecialchars($item['name']) ?>"
                                            data-price="<?= $item['price'] ?>">
                                            <i class="fas fa-plus me-1"></i> Add
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary rounded-pill fw-bold" disabled>Habis</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">Produk tidak ditemukan :(</h4>
                    <a href="Home.php" class="btn btn-outline-success mt-2">Lihat Semua Produk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="userMenuModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-logout-position">
            <div class="modal-content shadow-lg" style="border-radius: 15px; border: none;">

                <div class="modal-header py-2" style="background-color: #008751; color: white;">
                    <h6 class="modal-title">
                        Halo, <?= htmlspecialchars(($user_data['firstname'] ?? '') . ' ' . ($user_data['lastname'] ?? '')) ?>
                    </h6>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center p-3">
                    <div class="profile-pic-wrapper">
                        <?php
                        $popup_pic = 'Gambar/Profile.png';
                        if (!empty($user_data['profile_pic']) && file_exists($user_data['profile_pic'])) {
                            $popup_pic = $user_data['profile_pic'];
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
                        <a href="Database/logout.php" class="btn btn-danger rounded-pill">
                            <i class="fas fa-sign-out-alt me-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4" style="border-radius: 20px;">

                <div class="text-center mb-4 pb-3 border-bottom">
                    <h5 class="text-muted small mb-1">Profile Kamu</h5>
                    <h3 class="fw-bold text-success m-0">
                        <?= htmlspecialchars($user_data['firstname'] ?? '') ?> <?= htmlspecialchars($user_data['lastname'] ?? '') ?>
                    </h3>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                        <div style="overflow: hidden;"><small class="text-muted d-block">Username</small><span class="fw-bold text-dark"><?= htmlspecialchars($user_data['username'] ?? '-') ?></span></div>
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editUsernameModal">Edit</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                        <div style="overflow: hidden;"><small class="text-muted d-block">Email</small><span class="fw-bold text-dark"><?= htmlspecialchars($user_data['email'] ?? '-') ?></span></div>
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editEmailModal">Edit</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                        <div style="overflow: hidden; max-width: 70%;"><small class="text-muted d-block">Alamat</small><span class="fw-bold text-dark text-truncate d-block"><?= !empty($user_data['address']) ? htmlspecialchars($user_data['address']) : '<i class="text-muted small">Belum diatur</i>' ?></span></div>
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editAddressModal">Edit</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                        <div><small class="text-muted d-block">Password</small><span class="fw-bold text-dark">******</span></div>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPasswordModal">Ubah</button>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="button" class="btn btn-light rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#userMenuModal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUsernameModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-4">
                <h5 class="fw-bold mb-3">Ganti Username</h5>
                <form action="Database/Profile.php" method="POST">
                    <input type="hidden" name="action" value="update_username">
                    <input type="text" name="new_username" class="custom-input mb-3" value="<?= htmlspecialchars($user_data['username'] ?? '') ?>" required>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button>
                        <button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEmailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="fw-bold mb-3">Update Email</h5>
                <form action="Database/Profile.php" method="POST">
                    <input type="hidden" name="action" value="update_email">
                    <input type="email" name="email" class="custom-input mb-3" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button>
                        <button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAddressModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="fw-bold mb-3">Update Alamat</h5>
                <form action="Database/Profile.php" method="POST">
                    <input type="hidden" name="action" value="update_address">
                    <textarea name="address" class="custom-input mb-3" rows="4"><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button>
                        <button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="fw-bold mb-3">Ganti Password</h5>
                <form action="Database/Profile.php" method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <input type="password" name="password" class="custom-input mb-3" placeholder="Password Baru..." required>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill rounded-pill" data-bs-toggle="modal" data-bs-target="#profileModal">Batal</button>
                        <button type="submit" class="btn btn-success flex-fill rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> <span id="toastMessage">Item ditambahkan!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // --- LOGIC AJAX ADD TO CART ---
            $('.btn-add-cart').click(function() {
                var productId = $(this).data('id');
                var productName = $(this).data('name');

                // Ubah tombol jadi loading
                var btn = $(this);
                var originalText = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i>');
                btn.prop('disabled', true);

                // Kirim data ke backend
                $.ajax({
                    url: 'Database/add_to_cart.php',
                    method: 'POST',
                    data: {
                        id: productId,
                        qty: 1
                    },
                    success: function(response) {
                        btn.html(originalText);
                        btn.prop('disabled', false);

                        $('#toastMessage').text(productName + " berhasil masuk keranjang!");
                        var toastEl = document.getElementById('cartToast');
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = "Gagal menambahkan.";
                        if (xhr.responseText) {
                            errorMsg = xhr.responseText;
                        }
                        alert(errorMsg);
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>