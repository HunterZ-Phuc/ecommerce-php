<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    <h4 class="mb-3">Đặt hàng thành công!</h4>
                    <p class="mb-4">
                        Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#<?= $order['id'] ?></strong>
                    </p>

                    <?php if ($order['paymentMethod'] === 'CASH_ON_DELIVERY'): ?>
                        <div class="alert alert-info">
                            Bạn sẽ thanh toán <strong><?= number_format($order['totalAmount']) ?>đ</strong> 
                            khi nhận hàng
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?php if ($order['paymentStatus'] === 'PENDING'): ?>
                                Vui lòng hoàn tất thanh toán để đơn hàng được xử lý
                            <?php else: ?>
                                Thanh toán của bạn đã được xác nhận
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h6>Thông tin giao hàng:</h6>
                        <p class="mb-1"><strong><?= htmlspecialchars($order['fullName']) ?></strong></p>
                        <p class="mb-1"><?= htmlspecialchars($order['phone']) ?></p>
                        <p class="mb-0"><?= htmlspecialchars($order['address']) ?></p>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="/ecommerce-php/order/detail/<?= $order['id'] ?>" 
                           class="btn btn-primary">
                            Xem chi tiết đơn hàng
                        </a>
                        <a href="/ecommerce-php/order/history" class="btn btn-outline-primary">
                            Xem lịch sử đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>