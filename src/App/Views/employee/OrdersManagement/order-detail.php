<?php
use App\Helpers\OrderHelper;
?>
<!-- sửa 4 -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đơn hàng #<?= $order['id'] ?></h2>
        <div>
            <a href="/ecommerce-php/employee/print-order/<?= $order['id'] ?>" target="_blank" class="btn btn-secondary">
                <i class="bi bi-printer"></i> In hóa đơn
            </a>
            <a href="/ecommerce-php/employee/orders" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> #<?= $order['id'] ?></p>
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?></p>
                            <p><strong>Tổng tiền:</strong> <?= number_format($order['totalAmount']) ?>đ</p>
                            <p>
                                <strong>Trạng thái đơn hàng:</strong>
                                <span class="badge <?= OrderHelper::getOrderStatusClass($order['orderStatus']) ?>">
                                    <?= OrderHelper::getOrderStatusText($order['orderStatus']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Phương thức thanh toán:</strong>
                                <?= $order['paymentMethod'] === 'CASH_ON_DELIVERY' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản' ?>
                            </p>
                            <p>
                                <strong>Trạng thái thanh toán:</strong>
                                <span class="badge <?= OrderHelper::getPaymentStatusClass($order['paymentStatus']) ?>">
                                    <?= OrderHelper::getPaymentStatusText($order['paymentStatus']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin khách hàng và địa chỉ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin khách hàng</h6>
                            <p><strong>Tên khách hàng:</strong> <?= $order['customerName'] ?></p>
                            <p><strong>Số điện thoại:</strong> <?= $order['phone'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Địa chỉ nhận hàng</h6>
                            <p><strong>Người nhận:</strong> <?= $order['receiverName'] ?></p>
                            <p><strong>Số điện thoại:</strong> <?= $order['receiverPhone'] ?></p>
                            <p><strong>Địa chỉ:</strong> <?= $order['address'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>SKU</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?= $item['productName'] ?></td>
                                        <td><?= $item['sku'] ?? 'N/A' ?></td>
                                        <td><?= number_format($item['price']) ?>đ</td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= number_format($item['price'] * $item['quantity']) ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong><?= number_format($order['totalAmount']) ?>đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cập nhật trạng thái -->
        <div class="col-md-4">
            <!-- Cập nhật trạng thái đơn hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cập nhật trạng thái đơn hàng</h5>
                </div>
                <div class="card-body">
                    <form action="/ecommerce-php/employee/order/update-status" method="POST">
                        <input type="hidden" name="orderId" value="<?= $order['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Trạng thái hiện tại</label>
                            <div>
                                <span class="badge <?= OrderHelper::getOrderStatusClass($order['orderStatus']) ?>">
                                    <?= OrderHelper::getOrderStatusText($order['orderStatus']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cập nhật trạng thái</label>
                            <select name="status" class="form-select" required>
                                <option value="">Chọn trạng thái</option>
                                <?php
                                $validNextStatuses = [
                                    'PENDING' => ['PROCESSING'],
                                    'PROCESSING' => ['CONFIRMED'],
                                    'CONFIRMED' => ['SHIPPING'],
                                    'SHIPPING' => [],
                                    'DELIVERED' => [],
                                    'RETURN_REQUEST' => ['RETURN_APPROVED', 'DELIVERED'],
                                    'RETURN_APPROVED' => ['RETURNED'],
                                    'RETURNED' => [],
                                    'CANCELLED' => []
                                ];

                                $currentStatus = $order['orderStatus'];
                                $nextStatuses = $validNextStatuses[$currentStatus] ?? [];

                                foreach ($nextStatuses as $status):
                                    $statusText = OrderHelper::getOrderStatusText($status);
                                    ?>
                                    <option value="<?= $status ?>"><?= $statusText ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                    </form>
                </div>
            </div>

            <!-- Cập nhật trạng thái thanh toán -->
            <?php if ($order['paymentMethod'] === 'QR_TRANSFER' && ($order['paymentStatus'] === 'PENDING' || $order['paymentStatus'] === 'PROCESSING')): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Xác nhận thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <form action="/ecommerce-php/employee/order/confirm-payment" method="POST">
                            <input type="hidden" name="orderId" value="<?= $order['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái thanh toán</label>
                                <select name="status" class="form-select" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="PAID">Đã thanh toán</option>
                                    <option value="FAILED">Thanh toán thất bại</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Xác nhận</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lịch sử đơn hàng -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lịch sử đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($order['history'] as $history): ?>
                            <div class="timeline-item">
                                <!-- Dùng OrderHelper để chuyển chữ của $history['statusText'] sang dạng tiếng việt-->
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0"><?= OrderHelper::getOrderStatusText($history['status']) ?? $history['status'] ?></h6>
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