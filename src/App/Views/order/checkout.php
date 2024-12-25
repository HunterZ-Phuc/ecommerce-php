<?php
error_log('=== CHECKOUT VIEW DATA ===');
error_log('Session: ' . print_r($_SESSION, true));
error_log('Cart Items: ' . print_r($cartItems, true));
error_log('Selected Variant IDs: ' . print_r($selectedVariantIds, true));
?>

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
                    <form action="/ecommerce-php/order/create" method="POST" id="checkoutForm">
                        <div class="form-group mb-3">
                            <label>Địa chỉ giao hàng</label>
                            <select name="addressId" class="form-control" required>
                                <option value="">Chọn địa chỉ giao hàng</option>
                                <?php foreach ($addresses as $address): ?>
                                    <option value="<?= $address['id'] ?>">
                                        <?= htmlspecialchars($address['fullName']) ?> - 
                                        <?= htmlspecialchars($address['phoneNumber']) ?> - 
                                        <?= htmlspecialchars($address['address']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Phương thức thanh toán</label>
                            <div class="payment-methods">
                                <div class="form-check border rounded p-3 mb-2">
                                    <input class="form-check-input" type="radio" name="paymentMethod" 
                                           value="CASH_ON_DELIVERY" id="cod" required>
                                    <label class="form-check-label w-100" for="cod">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            <div>
                                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                                                <div class="text-muted small">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-check border rounded p-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" 
                                           value="QR_TRANSFER" id="qr" required>
                                    <label class="form-check-label w-100" for="qr">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-qrcode me-2"></i>
                                            <div>
                                                <strong>Chuyển khoản qua QR</strong>
                                                <div class="text-muted small">Quét mã QR để thanh toán</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback">Vui lòng chọn phương thức thanh toán</div>
                        </div>

                        <!-- Danh sách sản phẩm đã chọn -->
                        <?php foreach ($selectedVariantIds as $variantId): ?>
                            <input type="hidden" name="selectedVariantIds[]" value="<?= $variantId ?>">
                        <?php endforeach; ?>

                        <!-- Tổng tiền và nút đặt hàng -->
                        <div class="text-right">
                            <h4>Tổng tiền: <?= number_format($cartTotal) ?>đ</h4>
                            <button type="submit" class="btn btn-primary">Đặt hàng</button>
                        </div>
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
                            <img src="<?= '/ecommerce-php/public/' . htmlspecialchars($item['imageUrl']) ?>" 
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

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    console.log('Form submitting...');
    console.log('Session data:', <?php echo json_encode($_SESSION); ?>);
    console.log('Selected items:', <?php echo json_encode($selectedItems ?? []); ?>);
    
    const addressId = document.querySelector('select[name="addressId"]').value;
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');

    if (!addressId || !paymentMethod) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin');
        return;
    }
});
</script>

<!-- Thêm div để hiển thị lỗi nếu có -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>