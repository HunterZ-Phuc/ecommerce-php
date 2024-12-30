<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thanh toán đơn hàng #<?= $order['id'] ?></h5>
                </div>
                <div class="card-body text-center">
                    <h6 class="mb-4">Vui lòng quét mã QR để thanh toán số tiền:
                        <?= number_format($order['totalAmount']) ?>đ</h6>

                    <div class="mb-4">
                        <img src="<?= $order['qrImage'] ?>" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    </div>

                    <div class="alert alert-info">
                        <strong>Lưu ý:</strong> Sau khi chuyển khoản, vui lòng nhấn nút xác nhận bên dưới.
                    </div>

                    <form action="/ecommerce-php/order/confirmPayment/<?= $order['id'] ?>" method="POST">
                        <button type="submit" class="btn btn-primary">
                            Tôi đã thanh toán
                        </button>
                        <a href="/ecommerce-php/order/detail/<?= $order['id'] ?>" class="btn btn-secondary">
                            Xem chi tiết đơn hàng
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>