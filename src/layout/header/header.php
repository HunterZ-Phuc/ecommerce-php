<?php
session_start();
include("C:/xampp/htdocs/ecommerce-php/src/utils/db_connect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nông Sản Tây Bắc</title>

    <link href="./assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg" style="background-color: #f8f9fa;">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="images/logo2.png" alt="Logo" height="40" class="d-none d-lg-inline">
                <img src="images/logo1.png" alt="Logo" height="35" class="d-lg-none">
            </a>

            <!-- Thanh tìm kiếm -->
            <div class="d-flex flex-grow-1 mx-lg-4">
                <form class="d-flex w-100" method="get" action="result.php">
                    <input class="form-control me-2" type="search" name="user_query" placeholder="Tìm kiếm sản phẩm..." required>
                    <button class="btn btn-success" type="submit" name="search">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Giỏ hàng và tài khoản -->
            <div class="d-flex align-items-center">
                <a href="<?= isset($_SESSION['customer_email']) ? 'cart.php' : 'checkout.php' ?>" class="btn btn-outline-success me-2">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="d-none d-md-inline ms-2">Giỏ hàng</span>
                </a>
                <a href="<?= isset($_SESSION['customer_email']) ? 'customer/my_account.php?my_order' : '#' ?>" class="btn btn-outline-success">
                    <i class="fa fa-user"></i>
                    <span class="d-none d-md-inline ms-2">Tài khoản</span>
                </a>
            </div>

            <!-- Nút toggle menu -->
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Menu chính -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2E7D32;">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Về chúng tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="service.php">Dịch vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Liên hệ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="./assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>