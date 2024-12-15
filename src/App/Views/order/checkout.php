<div class="container my-5">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <form action="/ecommerce-php/order/create" method="POST">
                        <!-- Địa chỉ giao hàng -->
                        <div class="mb-4">
                            <h6>Địa chỉ giao hàng</h6>
                            <?php foreach ($addresses as $address): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" 
                                           name="addressId" value="<?= $address['id'] ?>" 
                                           <?= $address['isDefault'] ? 'checked' : '' ?> required>
                                    <label class="form-check-label">
                                        <strong><?= htmlspecialchars($address['fullName']) ?></strong><br>
                                        <?= htmlspecialchars($address['phoneNumber']) ?><br>
                                        <?= htmlspecialchars($address['address']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mb-4">
                            <h6>Phương thức thanh toán</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" 
                                       name="paymentMethod" value="COD" checked required>
                                <label class="form-check-label">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       name="paymentMethod" value="BANKING" required>
                                <label class="form-check-label">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-4">
                            <h6>Ghi chú</h6>
                            <textarea name="note" class="form-control" rows="3" 
                                      placeholder="Ghi chú về đơn hàng"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tổng quan đơn hàng -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tổng quan đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex mb-3">
                            <img src="<?= htmlspecialchars($item['imageUrl']) ?>" 
                                 alt="<?= htmlspecialchars($item['productName']) ?>" 
                                 class="img-fluid rounded" style="width: 80px;">
                            <div class="ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars($item['productName']) ?></h6>
                                <p class="mb-0 text-muted">
                                    <?= number_format($item['finalPrice']) ?>đ x <?= $item['quantity'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?= number_format($cartTotal) ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong><?= number_format($cartTotal) ?>đ</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 