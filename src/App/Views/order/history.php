<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag mb-3" style="font-size: 48px; color: #ccc;"></i>
                            <h5>Bạn chưa có đơn hàng nào</h5>
                            <a href="/ecommerce-php/product" class="btn btn-primary mt-3">
                                Mua sắm ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thanh toán</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?></td>
                                            <td><?= number_format($order['totalAmount']) ?>đ</td>
                                            <td>
                                                <span class="badge bg-<?= $order['statusColor'] ?>">
                                                    <?= $order['statusText'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $order['paymentStatusColor'] ?>">
                                                    <?= $order['paymentStatusText'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/ecommerce-php/order/detail/<?= $order['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        Chi tiết
                                                    </a>
                                                    <?php if ($order['paymentMethod'] === 'QR_TRANSFER' && 
                                                              $order['paymentStatus'] === 'PENDING'): ?>
                                                        <a href="/ecommerce-php/order/payment/<?= $order['id'] ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            Thanh toán
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="/ecommerce-php/order/history?page=<?= $currentPage - 1 ?>">
                                                Trước
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" 
                                               href="/ecommerce-php/order/history?page=<?= $i ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="/ecommerce-php/order/history?page=<?= $currentPage + 1 ?>">
                                                Sau
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>