<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid my-5">
    <div class="row">
        <div class="col-md-2">
            <!-- Menu nhân viên -->
            <?php require_once __DIR__ . '/menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chi tiết đơn hàng #<?= $order['id'] ?></h5>
                        <span class="badge 
                            <?php
                            switch ($order['orderStatus']) {
                                case 'PENDING':
                                    echo 'bg-warning';
                                    break;
                                case 'PROCESSING':
                                    echo 'bg-info';
                                    break;
                                case 'SHIPPING':
                                    echo 'bg-primary';
                                    break;
                                case 'DELIVERED':
                                    echo 'bg-success';
                                    break;
                                case 'CANCELLED':
                                    echo 'bg-danger';
                                    break;
                            }
                            ?>">
                            <?php
                            switch ($order['orderStatus']) {
                                case 'PENDING':
                                    echo 'Chờ xác nhận';
                                    break;
                                case 'PROCESSING':
                                    echo 'Đang xử lý';
                                    break;
                                case 'SHIPPING':
                                    echo 'Đang giao';
                                    break;
                                case 'DELIVERED':
                                    echo 'Đã giao';
                                    break;
                                case 'CANCELLED':
                                    echo 'Đã hủy';
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thông tin đơn hàng -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6>Thông tin khách hàng</h6>
                            <p class="mb-1">
                                <strong>Tài khoản:</strong> 
                                <?= htmlspecialchars($order['username']) ?>
                            </p>
                            <p class="mb-1">
                                <strong>Người nhận:</strong> 
                                <?= htmlspecialchars($order['fullName']) ?>
                            </p>
                            <p class="mb-1">
                                <strong>Số điện thoại:</strong> 
                                <?= htmlspecialchars($order['phone']) ?>
                            </p>
                            <p class="mb-0">
                                <strong>Địa chỉ:</strong> 
                                <?= htmlspecialchars($order['address']) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6>Thông tin thanh toán</h6>
                            <p class="mb-1">
                                <strong>Phương thức:</strong>
                                <?= $order['paymentMethod'] === 'COD' ? 'Tiền mặt khi nhận hàng' : 'Chuyển khoản ngân hàng' ?>
                            </p>
                            <p class="mb-1">
                                <strong>Trạng thái:</strong>
                                <span class="badge <?= $order['paymentStatus'] === 'PAID' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $order['paymentStatus'] === 'PAID' ? 'Đã thanh toán' : 'Chờ thanh toán' ?>
                                </span>
                            </p>
                            <?php if ($order['paymentMethod'] === 'BANKING'): ?>
                                <?php if (!empty($order['bankingImage'])): ?>
                                    <p class="mb-0">
                                        <strong>Ảnh chuyển khoản:</strong><br>
                                        <img src="<?= htmlspecialchars($order['bankingImage']) ?>" 
                                             alt="Ảnh chuyển khoản" class="img-thumbnail mt-2" 
                                             style="max-width: 200px;">
                                    </p>
                                <?php else: ?>
                                    <p class="mb-0 text-warning">
                                        Khách hàng chưa upload ảnh chuyển khoản
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h6>Thông tin đơn hàng</h6>
                            <p class="mb-1">
                                <strong>Mã đơn:</strong> #<?= $order['id'] ?>
                            </p>
                            <p class="mb-1">
                                <strong>Ngày đặt:</strong>
                                <?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?>
                            </p>
                            <p class="mb-1">
                                <strong>Cập nhật:</strong>
                                <?= date('d/m/Y H:i', strtotime($order['updatedAt'])) ?>
                            </p>
                            <p class="mb-0">
                                <strong>Tổng tiền:</strong>
                                <?= number_format($order['totalAmount']) ?>đ
                            </p>
                        </div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <h6>Sản phẩm</h6>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?= htmlspecialchars($item['imageUrl']) ?>" 
                                             alt="<?= htmlspecialchars($item['productName']) ?>" 
                                             class="img-fluid">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['productName']) ?></h6>
                                        <p class="mb-1 text-muted">
                                            Phân loại: <?= htmlspecialchars($item['variantName'] ?? '') ?>
                                        </p>
                                        <p class="mb-0">
                                            <?= number_format($item['price']) ?>đ x <?= $item['quantity'] ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <p class="mb-0">
                                            <strong>
                                                <?= number_format($item['price'] * $item['quantity']) ?>đ
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Tổng cộng -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span><?= number_format($order['totalAmount']) ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span>0đ</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng cộng:</strong>
                                <strong><?= number_format($order['totalAmount']) ?>đ</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch sử đơn hàng -->
                    <h6 class="mt-4">Lịch sử đơn hàng</h6>
                    <div class="timeline mb-4">
                        <?php foreach ($order['history'] as $history): ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($history['createdAt'])) ?>
                                    </small>
                                    <p class="mb-0">
                                        <?php
                                        switch ($history['status']) {
                                            case 'PENDING':
                                                echo 'Đơn hàng chờ xác nhận';
                                                break;
                                            case 'PROCESSING':
                                                echo 'Đơn hàng đang được xử lý';
                                                break;
                                            case 'SHIPPING':
                                                echo 'Đơn hàng đang được giao';
                                                break;
                                            case 'DELIVERED':
                                                echo 'Đơn hàng đã giao thành công';
                                                break;
                                            case 'CANCELLED':
                                                echo 'Đơn hàng đã bị hủy';
                                                break;
                                        }
                                        ?>
                                    </p>
                                    <?php if (!empty($history['note'])): ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($history['note']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Cập nhật trạng thái -->
                    <?php if ($order['orderStatus'] !== 'CANCELLED' && $order['orderStatus'] !== 'DELIVERED'): ?>
                        <div class="card">
                            <div class="card-body">
                                <h6>Cập nhật trạng thái</h6>
                                
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?= $error ?></div>
                                <?php endif; ?>

                                <!-- Form cập nhật trạng thái đơn hàng -->
                                <form action="/ecommerce-php/employee/update-order-status" method="POST" class="mb-3">
                                    <input type="hidden" name="orderId" value="<?= $order['id'] ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="status" class="form-select" required>
                                                <option value="">Chọn trạng thái</option>
                                                <?php if ($order['orderStatus'] === 'PENDING'): ?>
                                                    <option value="PROCESSING">Xác nhận & Xử lý</option>
                                                    <option value="CANCELLED">Hủy đơn</option>
                                                <?php elseif ($order['orderStatus'] === 'PROCESSING'): ?>
                                                    <option value="SHIPPING">Giao hàng</option>
                                                    <option value="CANCELLED">Hủy đơn</option>
                                                <?php elseif ($order['orderStatus'] === 'SHIPPING'): ?>
                                                    <option value="DELIVERED">Đã giao</option>
                                                    <option value="CANCELLED">Hủy đơn</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="note" class="form-control" 
                                                   placeholder="Ghi chú (không bắt buộc)">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                Cập nhật
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Form xác nhận thanh toán (chỉ hiển thị với đơn hàng chuyển khoản) -->
                                <?php if ($order['paymentMethod'] === 'BANKING' && 
                                          $order['paymentStatus'] !== 'PAID' && 
                                          !empty($order['bankingImage'])): ?>
                                    <form action="/ecommerce-php/employee/confirm-payment" method="POST">
                                        <input type="hidden" name="orderId" value="<?= $order['id'] ?>">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <select name="status" class="form-select" required>
                                                    <option value="">Xác nhận thanh toán</option>
                                                    <option value="PAID">Xác nhận đã thanh toán</option>
                                                    <option value="FAILED">Thanh toán không hợp lệ</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success w-100">
                                                    Xác nhận
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Nút quay lại -->
                    <div class="mt-4">
                        <a href="/ecommerce-php/employee/orders" class="btn btn-outline-primary">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 24px;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:after {
    content: '';
    position: absolute;
    left: -4px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 