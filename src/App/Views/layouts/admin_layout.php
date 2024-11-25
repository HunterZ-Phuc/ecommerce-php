<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-item .nav-link {
            padding: 0.8rem 1rem;
            transition: all 0.3s;
        }

        .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-item .nav-link i {
            margin-right: 10px;
        }

        #mainContent {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark min-vh-100 p-0">
                <div class="p-3 text-white">
                    <h4>Admin Panel</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/ecommerce-php/admin">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/ecommerce-php/admin/admin-management">
                            <i class="bi bi-people"></i> Quản lý admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/ecommerce-php/admin/employee-management">
                            <i class="bi bi-people"></i> Quản lý nhân viên
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/ecommerce-php/admin/user-management">
                            <i class="bi bi-people"></i> Quản lý khách hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/ecommerce-php/logout">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-10 p-0">
                <div id="mainContent">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 