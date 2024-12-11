<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid my-5">
    <div class="row">
        <div class="col-md-2">
            <!-- Menu nhân viên -->
            <?php require_once __DIR__ . '/menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quản lý đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box text-muted mb-3" style="font-size: 48px;"></i>
                            <p class="mb-0">Không có đơn hàng nào cần xử lý</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Thanh toán</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td>
                                                <p class="mb-1">
                                                    <?= htmlspecialchars($order['username']) ?>
                                                </p>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($order['fullName']) ?>
                                                </small>
                                            </td>
                                            <td><?= number_format($order['totalAmount']) ?>đ</td>
                                            <td>
                                                <?= $order['paymentMethod'] === 'COD' ? 'COD' : 'Chuyển khoản' ?>
                                                <?php if ($order['paymentMethod'] === 'BANKING'): ?>
                                                    <span class="badge <?= $order['paymentStatus'] === 'PAID' ? 'bg-success' : 'bg-warning' ?>">
                                                        <?= $order['paymentStatus'] === 'PAID' ? 'Đã thanh toán' : 'Chờ thanh toán' ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?>
                                            </td>
                                            <td>
                                                <a href="/ecommerce-php/employee/order-detail/<?= $order['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 