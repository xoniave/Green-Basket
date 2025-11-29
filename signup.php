<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    }

    function validateForm() {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm_password").value;

        if (password !== confirmPassword) {
            alert("Password dan Konfirmasi Password tidak sama!");
            return false; // Mencegah form dikirim
        }
        return true; // Izinkan form dikirim
    }

    </script>

    <style>
        body {
            background: url('Gambar/Login-Signup-Page-2.png') no-repeat center center fixed;
            background-size: cover;
        }

        #signup {
            font-size: 40px;
            font-weight: 700;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            position: relative; 
        }

        .btn-light { 
            font-weight: 600; 
            color: #007f42; 
        }
    </style>
</head>

<body>

    <!-- menerima eror dari sav submit kalau user ada -->
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 'user_exists') {
        echo '
        <script>
            alert("Pendaftaran gagal. Username yang Anda masukkan sudah digunakan.");
            window.history.replaceState(null, null, "signup.php"); 
        </script>
        ';
    }
    ?>

    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="glass-card text-white">

                    <!-- X BUTTON -->
                    <a href="Index.php" class="btn-close btn-close-white position-absolute" 
                       style="right: 20px; top: 20px;"></a>

                    <h1 id="signup" class="text-center mb-4">Sign Up</h1>

                    <form method="post" action="Database/save_submit.php" onsubmit="return validateForm()">

                        <!-- FIRST NAME & LAST NAME -->
                        <div class="row g-3">
                            <div class="col-6">
                                <input name="firstname" id="firstname" type="text" class="form-control" placeholder="First Name" required>
                            </div>
                            <div class="col-6">
                                <input name="lastname" id="lastname" type="text" class="form-control" placeholder="Last Name" required>
                            </div>
                        </div>

                        <!-- USERNAME -->
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <input name="username" id="username" type="text" class="form-control" placeholder="Username" required>
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <input name="email" id="email" type="email" class="form-control" placeholder="Email" required>
                            </div>
                        </div>

                        <!-- PASSWORD -->
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <div class="input-group">
                                    <input name="password" id="password" type="text" class="form-control" placeholder="Password" required>
                                </div>
                            </div>
                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
                                    <span class="input-group-text" onclick="togglePassword('confirm_password', this)">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <input name="button_create_account" type="submit" class="btn btn-light px-4" value="Create Account">

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
