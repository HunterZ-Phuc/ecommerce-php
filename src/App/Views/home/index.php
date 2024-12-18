<!-- sửa ở đây point 4 -->
<div class="container mt-4">
    <div class="row">
        <!-- Sidebar lọc -->
        <div class="col-md-2">
            <form action="/ecommerce-php/search" method="GET" class="mb-4">
                <?php if (!empty($query)): ?>
                    <input type="hidden" name="query" value="<?= htmlspecialchars($query) ?>">
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="h5 mb-0">Bộ lọc tìm kiếm</h3>
                    </div>
                    <div class="card-body">
                        <!-- Lọc theo danh mục -->
                        <div class="mb-3">
                            <h5 class="h6">Danh mục</h5>
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" 
                                        value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>"
                                        <?= ($category === $cat['id']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lọc theo giá -->
                        <div class="mb-3">
                            <h5 class="h6">Khoảng giá</h5>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="minPrice" 
                                        placeholder="Từ" value="<?= htmlspecialchars($minPrice ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="maxPrice" 
                                        placeholder="Đến" value="<?= htmlspecialchars($maxPrice ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-10">
            <!-- Thanh sắp xếp -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">Sắp xếp theo:</span>
                        <div class="btn-group me-2">
                            <button class="btn btn-outline-secondary active">Phổ biến</button>
                            <button class="btn btn-outline-secondary">Mới nhất</button>
                            <button class="btn btn-outline-secondary">Bán chạy</button>
                        </div>
                        <select class="form-select w-auto">
                            <option value="">Giá</option>
                            <option value="asc">Giá thấp → cao</option>
                            <option value="desc">Giá cao → thấp</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid sản phẩm -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                <?php foreach ($products as $product): ?>
                    <?php
                    // Tính tổng số lượng của tất cả các biến thể
                    $totalQuantity = array_reduce($product['variants'], function ($carry, $variant) {
                        return $carry + $variant['quantity'];
                    }, 0);

                    // Tính phần trăm đã bán
                    $soldPercent = $totalQuantity > 0 ? ($product['sold'] / $totalQuantity) * 100 : 0;
                    ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm position-relative">
                            <a href="<?= '/ecommerce-php/product/' . $product['id'] ?>"
                                class="text-decoration-none text-dark">
                                <?php if ($product['salePercent'] > 0): ?>
                                    <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small fw-bold">
                                        -<?= $product['salePercent'] ?>%
                                    </span>
                                <?php endif; ?>

                                <img src="<?= '/ecommerce-php/public' . ($product['mainImage'] ?? '/assets/images/no-image.png') ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($product['productName']) ?>"
                                    style="height: 200px; object-fit: cover;">

                                <div class="card-body p-3">
                                    <h5 class="card-title fw-semibold mb-3"><?= htmlspecialchars($product['productName']) ?>
                                    </h5>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($product['salePercent'] > 0): ?>
                                            <span class="fs-5 fw-bold text-danger">
                                                <?= number_format($product['price'] * (1 - $product['salePercent'] / 100), 0, ',', '.') ?>₫
                                            </span>
                                            <span class="text-muted text-decoration-line-through">
                                                <?= number_format($product['price'], 0, ',', '.') ?>₫
                                            </span>
                                        <?php else: ?>
                                            <span class="fs-5 fw-bold text-danger">
                                                <?= number_format($product['price'], 0, ',', '.') ?>₫
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: <?= $soldPercent ?>%" aria-valuenow="<?= $product['sold'] ?>"
                                            aria-valuemin="0" aria-valuemax="<?= $totalQuantity ?>">
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">Đã bán
                                        <?= number_format($product['sold'], 0, ',', '.') ?> /
                                        <?= number_format($totalQuantity, 0, ',', '.') ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
<?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="/ecommerce-php/?page=<?= $currentPage - 1 ?>">Trước</a>
                </li>
            <?php endif; ?>

            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            if ($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="/ecommerce-php/?page=1">1</a>
                </li>
                <?php if ($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif;
            endif;

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="/ecommerce-php/?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor;

            if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="/ecommerce-php/?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                </li>
            <?php endif;

            if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="/ecommerce-php/?page=<?= $currentPage + 1 ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, .125);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .card img {
        transition: all 0.3s ease;
    }

    .card:hover img {
        transform: scale(1.02);
    }
</style>