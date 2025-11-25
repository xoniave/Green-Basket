<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Stack+Sans+Headline:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<style>
        body {
        background: url('Login & Sign-up Page.png')
                   no-repeat center center fixed;
        background-size: cover;

        }

        #greenbasket{
             font-family: "Stack Sans Headline", sans-serif;
             font-optical-sizing: auto;
             font-weight: 700;
             font-style: normal;
             font-size: 60px;
        }

        #welcome{
            font-size: 30px;
        }

        .btn.btn-light{
            font-family: "Open Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings:
                "wdth" 100;
        }

        .form-control.mb-3{
            max-width: 550px;
        }
        .input{
            margin-left: auto;
            margin-right: auto;
            text-align: left;
    }

</style>
<body>
    <div class="container-fluid d-flex justify-content-center">
        <div class="container text-center">

            <div class="row mt-5 align-items-center">

                <div class="col-12 col-md-5 text-md-end text-center text-white mb-2 mb-md-0">
                    <h5 id="welcome">Welcome to</h5>
                </div>

                <div class="col-12 col-md-7 text-md-start text-center text-white">
                    <h1 id="greenbasket"><b>Green Basket</b></h1>
                </div>

            </div>

            <div class="mt-2">
                <form>
                    <input type="text" class="form-control mb-3 input" id="username" placeholder="Username">
                    <input type="password" class="form-control mb-3 input" id="password" placeholder="Password">
                </form>

                <div class="row">
                    <div class="col-6 text-end">
                        <button class="btn btn-light">Sign-up</button>
                    </div>
                    <div class="col-6 text-start">
                        <button class="btn btn-light">Login</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>