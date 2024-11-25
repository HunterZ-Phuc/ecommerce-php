<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nông Sản Tây Bắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg" style="background-color: #f8f9fa;">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/ecommerce-php">
                <img src="/ecommerce-php/public/images/logo2.png" alt="Logo" height="40" class="d-none d-lg-inline">
                <img src="/ecommerce-php/public/images/logo1.png" alt="Logo" height="35" class="d-lg-none">
            </a>

            <!-- Search Bar -->
            <div class="d-flex flex-grow-1 mx-lg-4">
                <form class="d-flex w-100" method="get" action="/ecommerce-php/search">
                    <input class="form-control me-2" type="search" name="query" placeholder="Tìm kiếm sản phẩm..." required>
                    <button class="btn btn-success" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Cart & Account -->
            <div class="d-flex align-items-center">
                <a href="<?= isset($_SESSION['customer_email']) ? '/ecommerce-php/cart' : '/ecommerce-php/login' ?>" 
                   class="btn btn-outline-success me-2">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="d-none d-md-inline ms-2">Giỏ hàng</span>
                </a>
                <a href="<?= isset($_SESSION['customer_email']) ? '/ecommerce-php/customer/account' : '/ecommerce-php/login' ?>" 
                   class="btn btn-outline-success">
                    <i class="fa fa-user"></i>
                    <span class="d-none d-md-inline ms-2">Tài khoản</span>
                </a>
            </div>

            <!-- Toggle Button -->
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Main Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2E7D32;">
        <div class="container">
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="/ecommerce-php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'shop' ? 'active' : '' ?>" href="/ecommerce-php/shop">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="/ecommerce-php/about">Về chúng tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'service' ? 'active' : '' ?>" href="/ecommerce-php/service">Dịch vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="/ecommerce-php/contact">Liên hệ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'blog' ? 'active' : '' ?>" href="/ecommerce-php/blog">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>