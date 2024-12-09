<?php
// File này phải tồn tại trong thư mục layouts
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Trang người dùng' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Header/Navbar có thể thêm ở đây -->
    <?php require_once 'header.php'; ?>

    <div class="container py-4">
        <div class="bg-white rounded shadow">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-md-3 border-end">
                    <div class="p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="<?= isset($user['avatar']) ? '/ecommerce-php/public' . $user['avatar'] : 'https://placehold.co/100x100?text=Profile' ?>" 
                                 alt="User Profile" 
                                 class="rounded-circle me-3"
                                 style="width: 60px; height: 60px; object-fit: cover;" />
                            <div>
                                <div class="fs-5 fw-semibold"><?= htmlspecialchars($user['username'] ?? 'Người dùng') ?></div>
                                <small class="text-muted">
                                    <i class="bi bi-pencil-square"></i> 
                                    <a href="/ecommerce-php/user/profile" class="text-decoration-none text-muted">Sửa hồ sơ</a>
                                </small>
                            </div>
                        </div>

                        <div class="nav flex-column nav-pills">
                            <div class="nav-item mb-2">
                                <span class="d-block text-secondary fw-bold mb-2">Tài khoản của tôi</span>
                                <a href="/ecommerce-php/user/profile" class="nav-link <?= $active_page == 'profile' ? 'active' : '' ?>">
                                    <i class="bi bi-person me-2"></i>Hồ Sơ
                                </a>
                                <a href="/ecommerce-php/user/addresses" class="nav-link <?= $active_page == 'addresses' ? 'active' : '' ?>">
                                    <i class="bi bi-geo-alt me-2"></i>Địa Chỉ
                                </a>
                                <a href="/ecommerce-php/user/change-password" class="nav-link <?= $active_page == 'change-password' ? 'active' : '' ?>">
                                    <i class="bi bi-key me-2"></i>Đổi Mật Khẩu
                                </a>
                            </div>

                            <div class="nav-item mb-2">
                                <span class="d-block text-secondary fw-bold mb-2">Đơn hàng</span>
                                <a href="/ecommerce-php/user/orders" class="nav-link <?= $active_page == 'orders' ? 'active' : '' ?>">
                                    <i class="bi bi-bag me-2"></i>Đơn Mua
                                </a>
                            </div>

                            <div class="nav-item">
                                <span class="d-block text-secondary fw-bold mb-2">Khuyến mãi</span>
                                <a href="/ecommerce-php/user/vouchers" class="nav-link <?= $active_page == 'vouchers' ? 'active' : '' ?>">
                                    <i class="bi bi-ticket-perforated me-2"></i>Kho Voucher
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="p-4">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer có thể thêm ở đây -->
    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
