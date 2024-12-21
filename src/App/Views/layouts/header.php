<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nông Sản Tây Bắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .navbar-nav .nav-link {
            color: #0BA215;
            font-weight: bold;
            text-shadow: 
                -1px -1px 0 white,  
                1px -1px 0 white,
                -1px 1px 0 white,
                1px 1px 0 white; /* Tạo viền trắng quanh chữ */
        }

        .navbar-nav .nav-item:hover .nav-link {
            /*  color: #11703;  Đổi màu chữ thành đỏ khi hover vào <li> */
            color: #F11703;
            font-weight: bold;
        }

        .navbar-nav .nav-item:hover {
            border-bottom: 3px solid red;
            /* Thêm thanh màu đỏ khi hover */
        }

        .ux-menu-icon {
            vertical-align: middle;
            /* Đặt icon ngang với chữ */
            margin-right: 5px;
            /* Khoảng cách giữa icon và chữ */
        }
    </style>
</head>

<body>
    <!-- sửa ở đây point 1 -->
    <nav class="navbar navbar-expand-lg" style="background-color: #f8f9fa;">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/ecommerce-php">
                <img src="/ecommerce-php/public/assets/images/logo.png" alt="Logo" height="40"
                    class="d-none d-lg-inline">
                <img src="/ecommerce-php/public/assets/images/logo.png" alt="Logo" height="35" class="d-lg-none">
            </a>

            <!-- Search Bar -->
            <div class="d-flex flex-grow-1 mx-lg-4">
                <form class="d-flex w-100" method="GET" action="/ecommerce-php/search">
                    <input class="form-control me-2" type="search" name="query" placeholder="Tìm kiếm sản phẩm..."
                        value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" required>
                    <button class="btn btn-success" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Cart & Account -->
            <div class="d-flex align-items-center">
                <!-- Giỏ hàng -->
                <a href="<?= $user['isLoggedIn'] ? '/ecommerce-php/cart' : '/ecommerce-php/login' ?>"
                    class="btn btn-outline-success me-2">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="d-none d-md-inline ms-2">Giỏ hàng</span>
                </a>

                <!-- Tài khoản -->
                <?php if ($user['isLoggedIn']): ?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="<?= '/ecommerce-php/public' . $user['avatar'] ?? 'https://openui.fly.dev/openui/24x24.svg?text=👤' ?>"
                                alt="user-avatar" class="custom-avatar"
                                style="width: 24px; height: 24px; border-radius: 50%;">
                            <span class="ms-2"><?= htmlspecialchars($user['name']) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="20" height="20" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M1.5 6.5a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 0 1h-12a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 0 1h-12a.5.5 0 0 1-.5-.5z" />
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="/ecommerce-php/user/profile">Tài Khoản Của Tôi</a></li>
                            <li><a class="dropdown-item" href="/ecommerce-php/order/history">Đơn Mua</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/ecommerce-php/logout">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/ecommerce-php/login" class="btn btn-outline-success">
                        <i class="fa fa-user"></i>
                        <span class="d-none d-md-inline ms-2">Đăng nhập</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Toggle Button -->
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Main Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #5FA533;">
        <div class="container">
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'shop' ? 'active' : '' ?>" href="/ecommerce-php/">
                            <img class="ux-menu-icon" width="20" height="20"
                                src="https://shopnongsansach.com/wp-content/uploads/house_home_13944.png" alt="">
                            CỬA HÀNG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>"
                            href="/ecommerce-php/about">VỀ CHÚNG TÔI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>"
                            href="/ecommerce-php/contact">LIÊN HỆ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>