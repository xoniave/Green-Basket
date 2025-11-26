<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Stack+Sans+Headline:wght@700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('Login & Sign-up Page (2).png') no-repeat center center fixed;
            background-size: cover;
        }

        #greenbasket {
            font-family: "Stack Sans Headline", sans-serif;
            font-weight: 700;
            font-size: 60px;
        }

        #welcome {
            font-size: 30px;
        }

        .btn.btn-light {
            font-family: "Open Sans", sans-serif;
            font-weight: 600;
            color: #007f42;
        }

        .form-control.mb-3 {
            max-width: 530px;
        }

        .input {
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        #konten {
            margin-top: 100px;
        }
    </style>
</head>

<body>

    <div class="container-fluid d-flex justify-content-center" id="konten">
        <div class="container text-center">

            <div class="row mt-5 align-items-center">
                <div class="col-12 col-lg-5 text-lg-end text-center text-white">
                    <h5 id="welcome">Welcome to</h5>
                </div>

                <div class="col-12 col-lg-7 text-lg-start text-center text-white">
                    <h1 id="greenbasket"><b>Green Basket</b></h1>
                </div>
            </div>

            <div class="mt-2">
                <form>
                    <input type="text" class="form-control mb-3 input" id="username" placeholder="Username">
                    <input type="password" class="form-control mb-3 input" id="password" placeholder="Password">
                </form>

                <!-- FORGOT PASSWORD (Trigger Offcanvas) -->
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="#" class="text-white"
                           data-bs-toggle="offcanvas" data-bs-target="#forgotCanvas">
                           Forgot Password?
                        </a>
                    </div>
                </div>

                <!-- BUTTON LOGIN + SIGNUP -->
                <div class="row mt-3">
                    <div class="col-6 text-end">
                        <a href="asd" class="btn btn-light px-4">Login</a>
                    </div>

                    <div class="col-6 text-start">
                        <a href="signup.php" class="btn btn-light px-4">Signup</a>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- RIGHT SIDE PANEL - FORGOT PASSWORD -->
    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="forgotCanvas"
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); width: 350px;">

      <div class="offcanvas-header">
        <h5 class="offcanvas-title text-white">Reset Password</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body text-white">
        <p>Enter your email to receive reset link:</p>

        <input type="email" class="form-control mb-3" placeholder="Email">

        <button class="btn btn-light w-100">Send Reset Link</button>
      </div>

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
