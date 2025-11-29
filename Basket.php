<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basket Page</title>
    <!-- CND -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        .Navbar_icon{
            width: 50px;
            height: auto;
            margin: 8px;
        }   
        .productCard {
            background: #f3f3f3;
            border-radius: 20px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .productImg {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 20px;
        }

        .productInfo {
            flex-grow: 1;
        }

        .qtyBox {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qtyBtn {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 50%;
            background: #009944;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .numberDisplay {
            width: 40px;
            height: 35px;
            background: #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .footerBar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #009944;
            padding: 15px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .confirmBtn {
            background: white;
            color: #009944;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .deleteBtn{
            background: red;
            border: none;
            border-color: solid;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }
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
                    <a href="Basket.php">
                        <img src="BasketIcon.png" alt="Basket Icon" class="Navbar_icon">
                    </a>
                    <a href="">
                        <img src="BoxIcon.png" alt="delivery Icon" class="Navbar_icon">
                    </a>
                    <a href="Home.php">
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

    <!-- Basket display: -->
    <div style="margin: 30px;">
        <div class="productCard" id="productContainer">
        <!-- Cards akan ditampilkan di sini -->
        <!-- INI BAKAL DIMASUKIN DI SCRIPT -->
            <img src="BasketIcon.png" class="productImg">
            <div class="productInfo">
                <strong>0.00 Rp x 12 = 0.00 Rp</strong>
            </div>
            <div class="qtyBox">
                <button class="deleteBtn">Delete</button>
                <button class="qtyBtn">-</button>
                <div class="numberDisplay">12</div>
                <button class="qtyBtn">+</button>
            </div>
        </div>
    </div>

    <!-- Footer bar confirmation -->
     <div class="footerBar">
        <button class="confirmBtn">Confirm</button>
    </div>

    <!-- CND script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!-- Script Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        
    </script>
</body>
</html>