<?php
error_log('=== START RENDERING SUCCESS VIEW ===');
error_log('Order data: ' . print_r($order ?? 'No order data', true));
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if (isset($order) && !empty($order)): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                            <h4 class="mt-3">Đặt hàng thành công!</h4>
                            <p>Mã đơn hàng: <strong>#<?= $order['id'] ?></strong></p>
                        </div>

                        <!-- Thông tin thanh toán -->
                        <div class="alert <?= $order['paymentMethod'] === 'CASH_ON_DELIVERY' ? 'alert-info' : 'alert-warning' ?>">
                            <h6 class="mb-2">Phương thức thanh toán:</h6>
                            <?php if ($order['paymentMethod'] === 'CASH_ON_DELIVERY'): ?>
                                <p class="mb-0">Thanh toán khi nhận hàng (COD): <strong><?= number_format($order['totalAmount']) ?>đ</strong></p>
                            <?php else: ?>
                                <p class="mb-0">Chuyển khoản qua QR: <strong><?= number_format($order['totalAmount']) ?>đ</strong></p>
                                <?php if ($order['paymentStatus'] === 'PENDING'): ?>
                                    <a href="/ecommerce-php/order/payment/<?= $order['id'] ?>" class="btn btn-primary mt-2">
                                        Thanh toán ngay
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Thông tin giao hàng -->
                        <div class="mb-4">
                            <h6>Địa chỉ giao hàng:</h6>
                            <div class="border rounded p-3">
                                <p class="mb-1"><strong><?= htmlspecialchars($order['fullName']) ?></strong></p>
                                <p class="mb-1">SĐT: <?= htmlspecialchars($order['phone']) ?></p>
                                <p class="mb-0">Địa chỉ: <?= htmlspecialchars($order['address']) ?></p>
                            </div>
                        </div>

                        <!-- Chi tiết đơn hàng -->
                        <?php if (isset($order['items']) && !empty($order['items'])): ?>
                            <div class="mb-4">
                                <h6>Chi tiết sản phẩm:</h6>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="d-flex border-bottom py-3">
                                        <img src="<?= htmlspecialchars($item['imageUrl']) ?>" 
                                             alt="<?= htmlspecialchars($item['productName']) ?>" 
                                             class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['productName']) ?></h6>
                                            <?php if (!empty($item['variantInfo'])): ?>
                                                <p class="text-muted small mb-1"><?= htmlspecialchars($item['variantInfo']) ?></p>
                                            <?php endif; ?>
                                            <p class="mb-0">
                                                <?= number_format($item['price']) ?>đ x <?= $item['quantity'] ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <strong><?= number_format($item['price'] * $item['quantity']) ?>đ</strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Tổng cộng -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tổng tiền hàng:</span>
                                <span><?= number_format($order['totalAmount']) ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-danger"><?= number_format($order['totalAmount']) ?>đ</strong>
                            </div>
                        </div>

                        <!-- Nút điều hướng -->
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <a href="/ecommerce-php/order/detail/<?= $order['id'] ?>" class="btn btn-primary">
                                Xem chi tiết đơn hàng
                            </a>
                            <a href="/ecommerce-php/order/history" class="btn btn-outline-primary">
                                Xem lịch sử đơn hàng
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    Không tìm thấy thông tin đơn hàng
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>