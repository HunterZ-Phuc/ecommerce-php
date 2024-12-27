<div class="container-fluid">
    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quản lý đơn hàng</h5>
                    <div>
                        <a href="/ecommerce-php/employee/export/orders<?= isset($_GET['status']) ? '?status=' . $_GET['status'] : '' ?>" 
                           class="btn btn-success">
                            <i class="bi bi-file-earmark-excel me-2"></i>Xuất Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <p class="mb-0">Không có đơn hàng nào</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Người nhận</th>
                                        <th>Tổng tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái ĐH</th>
                                        <th>Trạng thái TT</th>
                                        <th>Ngày tạo</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($order['customerName']) ?><br>
                                                <small class="text-muted"><?= $order['phone'] ?></small>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($order['receiverName']) ?><br>
                                                <small class="text-muted"><?= $order['receiverPhone'] ?></small><br>
                                                <small class="text-muted"><?= htmlspecialchars($order['address']) ?></small>
                                            </td>
                                            <td class="text-end">
                                                <?= number_format($order['totalAmount'], 0, ',', '.') ?>đ
                                            </td>
                                            <td>
                                                <?php 
                                                    $paymentMethodText = $order['paymentMethod'] === 'CASH_ON_DELIVERY' 
                                                        ? 'Tiền mặt' 
                                                        : 'Chuyển khoản';
                                                ?>
                                                <span class="badge bg-info">
                                                    <?= $paymentMethodText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                    $orderStatusClass = '';
                                                    switch($order['orderStatus']) {
                                                        case 'PENDING':
                                                            $orderStatusClass = 'bg-warning';
                                                            $statusText = 'Chờ xử lý';
                                                            break;
                                                        case 'PROCESSING':
                                                            $orderStatusClass = 'bg-info';
                                                            $statusText = 'Đang xử lý';
                                                            break;
                                                        case 'SHIPPING':
                                                            $orderStatusClass = 'bg-primary';
                                                            $statusText = 'Đang giao';
                                                            break;
                                                        case 'DELIVERED':
                                                            $orderStatusClass = 'bg-success';
                                                            $statusText = 'Đã giao';
                                                            break;
                                                        case 'CANCELLED':
                                                            $orderStatusClass = 'bg-danger';
                                                            $statusText = 'Đã hủy';
                                                            break;
                                                        default:
                                                            $orderStatusClass = 'bg-secondary';
                                                            $statusText = 'Không xác định';
                                                    }
                                                ?>
                                                <span class="badge <?= $orderStatusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                    $paymentStatusClass = '';
                                                    switch($order['paymentStatus']) {
                                                        case 'PENDING':
                                                            $paymentStatusClass = 'bg-warning';
                                                            $paymentText = 'Chờ thanh toán';
                                                            break;
                                                        case 'PAID':
                                                            $paymentStatusClass = 'bg-success';
                                                            $paymentText = 'Đã thanh toán';
                                                            break;
                                                        case 'REFUNDED':
                                                            $paymentStatusClass = 'bg-info';
                                                            $paymentText = 'Đã hoàn tiền';
                                                            break;
                                                        default:
                                                            $paymentStatusClass = 'bg-secondary';
                                                            $paymentText = 'Không xác định';
                                                    }
                                                ?>
                                                <span class="badge <?= $paymentStatusClass ?>">
                                                    <?= $paymentText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?>
                                            </td>
                                            <td>
                                                <a href="/ecommerce-php/employee/order/<?= $order['id'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Chi tiết
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