<div class="container mt-4">
    <div class="row">
        <!-- Sidebar lọc -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5">Bộ lọc tìm kiếm</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="h6">Danh mục</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $category['id'] ?>">
                                        <label class="form-check-label">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="submit" class="btn btn-primary mt-3">Lọc</button>
                    </div>
                </div>
            </div>
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
                        $totalQuantity = array_reduce($product['variants'], function($carry, $variant) {
                            return $carry + $variant['quantity'];
                        }, 0);

                        // Tính phần trăm đã bán
                        $soldPercent = $totalQuantity > 0 ? ($product['sold'] / $totalQuantity) * 100 : 0;
                    ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm position-relative">
                            <a href="<?= '/ecommerce-php/product/' . $product['id'] ?>" class="text-decoration-none text-dark">
                                <?php if ($product['salePercent'] > 0): ?>
                                    <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small fw-bold">
                                        -<?= $product['salePercent'] ?>%
                                    </span>
                                <?php endif; ?>
                                
                                <img src="<?= '/ecommerce-php/public' . ($product['mainImage'] ?? '/assets/images/no-image.png') ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['productName']) ?>"
                                     style="height: 200px; object-fit: cover;">
                                     
                                <div class="card-body p-3">
                                    <h5 class="card-title fw-semibold mb-3"><?= htmlspecialchars($product['productName']) ?></h5>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($product['salePercent'] > 0): ?>
                                            <span class="fs-5 fw-bold text-danger">
                                                <?= number_format($product['price'] * (1 - $product['salePercent']/100), 0, ',', '.') ?>₫
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
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: <?= $soldPercent ?>%" 
                                             aria-valuenow="<?= $product['sold'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="<?= $totalQuantity ?>">
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">Đã bán <?= number_format($product['sold'], 0, ',', '.') ?> / <?= number_format($totalQuantity, 0, ',', '.') ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=1" tabindex="-1">Trước</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $totalPages ?>">Sau</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,.125);
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }

    .card img {
        transition: all 0.3s ease;
    }

    .card:hover img {
        transform: scale(1.02);
    }
</style>