<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- CND -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .Navbar_icon{
            width: 50px;
            height: auto;
            margin: 8px;
        }   
        .headerContainer{
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .headerphoto{
            width: 100%;
            height: auto;
        }
        .headerElement{
            position: absolute;
            top: 50%;
            left: 8%;
            transform: translateY(-50%);
            color: white;
            font-weight: bold;
            margin: 2px;
        }
        .headerButton{
            color: green;
            border-radius: 50px;
            background-color: white;
            font-size: 20px;
            padding: 3px 10px;
            font-weight: bold;
            border: none;
        }

        .categoryContainer{
            margin: 20px;
            width: 27%;
            display: flex;
            justify-content: center;
            
        }
        .categoryTitle{
            color: green;
            font-weight: bold;
        }

        .filter-bar .form-group { min-width: 180px; }
        .product-card { padding: 6px; }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">

            <!-- tombol 3 garis -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Icon di sebelah kiri -->
            <div class="collapse navbar-collapse" id="navMenu">
                
                <div class="d-flex">
                    <a href="#Basket.php">
                        <img src="BasketIcon.png" alt="Basket Icon" class="Navbar_icon">
                    </a>
                    <a href="#">
                        <img src="BoxIcon.png" alt="delivery Icon" class="Navbar_icon">
                    </a>
                    <a href="#Home.php">
                        <img src="HomeIcon.png" alt="Home Icon" class="Navbar_icon">
                    </a>
                </div>
            </div>

            <!-- search bar -->
            <form class="d-flex mx-auto my-2 my-lg-0" role="search" style="width: 50%;">
                <input id="search-input" class="form-control me-2 w-75" type="search" placeholder="Search" aria-label="Search"/>
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

            <!-- Profile icon -->
            <a href="#Profile.php" class="ms-lg-auto">
                <img src="Profile.png" alt="Profile Icon" class="Navbar_icon">

            </a>
        </div>
    </nav>

    <!-- Foto Header Home page -->
    <div class="headerContainer">
        <img src="headerPhoto.png" alt="Header Photo" class="headerphoto">
        <!-- Element yang di atas foto -->
        <div class="headerElement"> 
            <p style="font-size: 50px; margin-bottom: 0px;">Fresh & Healty</p>
            <p style="font-size: 30px;">Organic Food</p>
            <button class="headerButton">Shop</button>
        </div>
    </div>

    <!-- Category Section -->
    <div class="categoryContainer">
        <h1 class="categoryTitle">Category</h1>
        <!-- Filter: -->
        <div class="form-group" style="padding: 8px;">
            <!-- SELECT dinamis akan diisi lewat JS -->
            <select id="category-filter" class="form-select" style="color: green; border:solid green; font-weight: bold;">
            <option value="all">All</option>
            </select>
        </div>
    </div>

    <!-- Display produknya disini -->
    <div style="margin: 30px;">
        <div class="row" id="productContainer">
        <!-- Cards akan ditampilkan di sini -->
        </div>
    </div>

    <!-- CND script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!-- Script Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        const apiUrl = 'https://fakestoreapi.com/products';
        let allItems = []; 
        const toRupiah = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(Math.round(v * 15000));

        function renderProducts(items) {
        const container = $('#productContainer');
        container.empty();
        if (!items.length) {
            container.append('<div class="col-12"><p class="text-center text-muted">Produk tidak ditemukan.</p></div>');
            return;
        }

        items.forEach(item => {
            const id = item.id ?? '';
            const desc = item.description ?? '';
            const title = item.title ?? '';

            const img = item.image ?? item.thumbnail ?? 'https://via.placeholder.com/150?text=No+Image';

            const price = item.price ?? 0;
            const shortDesc = desc.length > 45 ? desc.substring(0,45) + '...' : desc;
            const category = item.category ?? '';

            const card = `
            <div class="col-12 col-md-4 col-lg-3 product-card">
                <div class="card h-100 p-0" style="border: solid green;" data-id="${id}" data-title="${(title).toLowerCase()}" data-category="${category}">
                    <div class="card-img-wrapper" style="display: flex; justify-content: center; margin: 10px;">
                        <img src="${img}" class="card-img-top" style="width: 50%; height: auto; object-fit: contain;" alt="${title}">
                    </div>
                    <div class="card-body" style="display: flex; justify-content: space-between">
                        <div>
                            <button style="background-color: green; font-weight: bold; color: white; border: none; border-radius: 50px; padding: 5px 12px; margin-top: 5px;"> Add </button>
                        </div>
                        <div style="margin-left:8px;">
                            <p class="card-text fw-bold mb-1" style="color: green">${escapeHtml(title)}</p>
                            <p class="card-text fw-bold mb-0" style="color: green">${toRupiah(price)}</p>
                        </div>
                    </div>
                </div>
            </div>
            `;
            container.append(card);
        });
        }

        function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }

        // Isi option filter
        function optionFilter(items) {
        const set = new Set();
        items.forEach(i => { if (i.category) set.add(i.category); });
        const cats = Array.from(set).sort((a,b) => a.localeCompare(b));
        const sel = $('#category-filter');
        sel.empty();
        sel.append(`<option value="all">Semua Kategori</option>`);
        cats.forEach(c => {
            sel.append(`<option value="${escapeHtml(c)}">${escapeHtml(c)}</option>`);
        });
        }

        // 
        function applyFilters() {
        const q = $('#search-input').val().trim().toLowerCase();
        const cat = $('#category-filter').val() || 'all';

        $('#productContainer .product-card').each(function() {
            const card = $(this).find('.card').first();
            const title = (card.attr('data-title') || '').toLowerCase();
            const category = (card.attr('data-category') || '');

            const matchesQuery = q === '' || title.includes(q);
            const matchesCategory = (cat === 'all') || (category === cat);

            
            if (matchesQuery && matchesCategory) {
            $(this).show(); 
            } else {
            $(this).hide(); 
            }
        });
        }

        $(document).ready(function() {
        $.ajax({
            url: apiUrl,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
            allItems = data || [];
            renderProducts(allItems);
            optionFilter(allItems);

            $('#search-input').on('input', function() {
                applyFilters();
            });

            $('#category-filter').on('change', function() {
                applyFilters();
            });

            $('#search-input').on('keypress', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); applyFilters(); }
            });
            },
            error: function(xhr, status, error) {
            console.error('Gagal ambil data:', status, error);
            $('#productContainer').html('<div class="col-12"><p class="text-danger text-center">Gagal memuat data. Silakan coba lagi.</p></div>');
            }
        });
        });
    </script>
</body>
</html>
