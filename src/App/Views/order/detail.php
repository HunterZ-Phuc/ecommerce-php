<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chi tiết đơn hàng #<?= $order['id'] ?></h5>
                    <span class="badge bg-<?= $order['statusColor'] ?>"><?= $order['statusText'] ?></span>
                </div>
                <div class="card-body">
                    <!-- Thông tin giao hàng -->
                    <div class="mb-4">
                        <h6>Thông tin giao hàng</h6>
                        <p class="mb-1"><strong><?= htmlspecialchars($order['fullName']) ?></strong></p>
                        <p class="mb-1"><?= htmlspecialchars($order['phone']) ?></p>
                        <p class="mb-0"><?= htmlspecialchars($order['address']) ?></p>
                    </div>

                    <hr>

                    <!-- Sản phẩm -->
                    <div class="mb-4">
                        <h6>Sản phẩm</h6>
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="d-flex mb-3">
                                <img src="<?= htmlspecialchars($item['imageUrl']) ?>" 
                                     alt="<?= htmlspecialchars($item['productName']) ?>" 
                                     class="img-thumbnail me-3" style="width: 100px;">
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($item['productName']) ?></h6>
                                    <small class="text-muted">
                                        <?php if (!empty($item['variantCombinations'])): ?>
                                            <?php foreach ($item['variantCombinations'] as $combination): ?>
                                                <?= htmlspecialchars($combination['typeName']) ?>: 
                                                <?= htmlspecialchars($combination['value']) ?><br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </small>
                                    <p class="mb-0">
                                        <?= number_format($item['price']) ?>đ x <?= $item['quantity'] ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Thanh toán -->
                    <div class="mb-4">
                        <h6>Thanh toán</h6>
                        <p class="mb-1">
                            <strong>Phương thức:</strong> 
                            <?= $order['paymentMethod'] === 'COD' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' ?>
                        </p>
                        <p class="mb-1">
                            <strong>Trạng thái:</strong>
                            <span class="badge bg-<?= $order['paymentStatusColor'] ?>">
                                <?= $order['paymentStatusText'] ?>
                            </span>
                        </p>
                        <?php if ($order['paymentMethod'] === 'BANKING' && $order['paymentStatus'] === 'PENDING'): ?>
                            <div class="mt-3">
                                <a href="/ecommerce-php/order/payment/<?= $order['id'] ?>" 
                                   class="btn btn-primary btn-sm">
                                    Thanh toán ngay
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <!-- Tổng cộng -->
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong><?= number_format($order['totalAmount']) ?>đ</strong>
                    </div>

                    <?php if ($order['status'] === 'PENDING' || $order['status'] === 'PROCESSING'): ?>
                        <div class="mt-4">
                            <form action="/ecommerce-php/order/cancel/<?= $order['id'] ?>" 
                                  method="POST" class="d-inline">
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                    Hủy đơn hàng
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Lịch sử đơn hàng -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($order['history'] as $history): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0"><?= $history['statusText'] ?></h6>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($history['createdAt'])) ?>
                                    </small>
                                    <?php if (!empty($history['note'])): ?>
                                        <p class="mb-0 mt-2"><?= htmlspecialchars($history['note']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #007bff;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -23px;
    top: 15px;
    height: 100%;
    width: 2px;
    background: #007bff;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 