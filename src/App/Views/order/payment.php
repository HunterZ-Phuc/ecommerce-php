<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thanh toán đơn hàng #<?= $order['id'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h6>Vui lòng chuyển khoản theo thông tin sau:</h6>
                        <div class="border p-3 my-3">
                            <p class="mb-2"><strong>Ngân hàng:</strong> BIDV</p>
                            <p class="mb-2"><strong>Số tài khoản:</strong> 12345678900</p>
                            <p class="mb-2"><strong>Chủ tài khoản:</strong> NGUYEN VAN A</p>
                            <p class="mb-2"><strong>Số tiền:</strong> <?= number_format($order['totalAmount']) ?>đ</p>
                            <p class="mb-0"><strong>Nội dung:</strong> DH<?= $order['id'] ?></p>
                        </div>
                        
                        <!-- QR Code -->
                        <div class="mb-4">
                            <img src="/ecommerce-php/public/images/qr-payment.png" 
                                 alt="QR Code" class="img-fluid" style="max-width: 200px;">
                        </div>

                        <!-- Upload ảnh chuyển khoản -->
                        <form action="/ecommerce-php/order/upload-payment/<?= $order['id'] ?>" 
                              method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Upload ảnh chuyển khoản</label>
                                <input type="file" class="form-control" name="bankingImage" 
                                       accept="image/*" required>
                            </div>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary">
                                Xác nhận đã chuyển khoản
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Chi tiết đơn hàng -->
                    <h6 class="mb-3">Chi tiết đơn hàng:</h6>
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

                    <hr>

                    <!-- Tổng cộng -->
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong><?= number_format($order['totalAmount']) ?>đ</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>