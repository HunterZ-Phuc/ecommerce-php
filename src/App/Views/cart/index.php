<?php
$pageTitle = $title ?? 'Giỏ hàng';
?>

<div class="container py-5">
    <h2 class="mb-4">Giỏ hàng của bạn</h2>

    <!-- Modal thông báo -->
    <div id="addressModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Bạn chưa có địa chỉ giao hàng. Vui lòng thêm địa chỉ trước khi tiếp tục.</p>
            <button id="addAddressBtn" class="btn btn-primary">Thêm địa chỉ</button>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Giỏ hàng của bạn đang trống.
            <a href="/ecommerce-php" class="alert-link">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th width="10%">Ảnh</th>
                                <th width="20%">Tên sản phẩm</th>
                                <th width="15%">Biến thể</th>
                                <th width="15%">Giá</th>
                                <th width="15%">Số lượng</th>
                                <th width="15%">Thành tiền</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr class="cart-item" data-variant-id="<?= $item['variantId'] ?>">
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox">
                                    </td>
                                    <td>
                                        <img src="<?= '/ecommerce-php/public' . ($item['imageUrl']) ?? '/ecommerce-php/public/images/default-product.jpg' ?>"
                                            class="img-fluid rounded" style="max-width: 80px;"
                                            alt="<?= htmlspecialchars($item['productName']) ?>">
                                    </td>
                                    <td>
                                        <a href="/ecommerce-php/product/<?= $item['productId'] ?>" class="text-decoration-none">
                                            <h6 class="mb-0 text-dark"><?= htmlspecialchars($item['productName']) ?></h6>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['variantCombinations'])): ?>
                                            <?php foreach ($item['variantCombinations'] as $combination): ?>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($combination['typeName']) ?>:
                                                    <?= htmlspecialchars($combination['value']) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['salePercent'] > 0): ?>
                                            <del class="text-muted d-block">
                                                <?= number_format($item['originalPrice'], 0, ',', '.') ?>đ
                                            </del>
                                            <span class="text-danger fw-bold">
                                                <?= number_format($item['finalPrice'], 0, ',', '.') ?>đ
                                            </span>
                                            <span class="badge bg-danger">
                                                -<?= $item['salePercent'] ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-danger fw-bold">
                                                <?= number_format($item['originalPrice'], 0, ',', '.') ?>đ
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <button class="btn btn-outline-secondary btn-decrease" type="button">-</button>
                                            <input type="number" class="form-control text-center quantity-input"
                                                value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stockQuantity'] ?>">
                                            <button class="btn btn-outline-secondary btn-increase" type="button">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold item-total">
                                            <?= number_format($item['finalPrice'] * $item['quantity'], 0, ',', '.') ?>đ
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-link text-danger btn-remove p-0">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer giỏ hàng -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="form-check-input me-2" id="selectAllBottom">
                        <label class="form-check-label" for="selectAllBottom">Chọn tất cả</label>
                        <button class="btn btn-outline-danger ms-3 btn-delete-selected">
                            Xóa đã chọn
                        </button>
                    </div>
                    <div class="text-end">
                        <div class="mb-2">
                            Tổng thanh toán (<span class="total-quantity-text">0</span> sản phẩm):
                            <span class="h5 text-danger ms-2 cart-total">0đ</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger btn-lg" onclick="processSelectedItems()">
                                <i class="fas fa-shopping-cart"></i> Mua hàng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cập nhật số lượng
        function updateQuantity(variantId, quantity) {
            fetch('/ecommerce-php/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `variantId=${variantId}&quantity=${quantity}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật tổng tiền
                        document.querySelector('.cart-total').textContent =
                            new Intl.NumberFormat('vi-VN').format(data.cartTotal) + 'đ';

                        // Cập nhật thành tiền của sản phẩm
                        const item = document.querySelector(`[data-variant-id="${variantId}"]`);
                        const price = parseFloat(item.querySelector('.text-danger.fw-bold').textContent.replace(/[^\d]/g, ''));
                        const itemTotal = price * quantity;
                        item.querySelector('.item-total').textContent =
                            new Intl.NumberFormat('vi-VN').format(itemTotal) + 'đ';

                        // Cập nhật tổng số lượng sản phẩm
                        updateTotalQuantity();
                    } else {
                        alert(data.message);
                    }
                });
        }

        // Hàm tính và cập nhật tổng số lượng sản phẩm
        function updateTotalQuantity() {
            let totalQuantity = 0;
            document.querySelectorAll('.quantity-input').forEach(input => {
                totalQuantity += parseInt(input.value) || 0;
            });
            // Cập nhật text hiển thị tổng số lượng
            const totalQuantityText = document.querySelector('.total-quantity-text');
            if (totalQuantityText) {
                totalQuantityText.textContent = totalQuantity;
            }
        }

        // Xóa sản phẩm khỏi giỏ hàng
        function removeItem(variantId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

            fetch('/ecommerce-php/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `variantId=${variantId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`[data-variant-id="${variantId}"]`);
                        item.remove();

                        // Cập nhật tổng tiền
                        document.querySelector('.cart-total').textContent =
                            new Intl.NumberFormat('vi-VN').format(data.cartTotal) + 'đ';

                        // Cập nhật tổng số lượng sản phẩm
                        updateTotalQuantity();

                        // Nếu giỏ hàng trống, reload trang
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert(data.message);
                    }
                });
        }

        // Xử lý checkbox
        document.querySelectorAll('#selectAll, #selectAllBottom').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const isChecked = this.checked;
                document.querySelectorAll('.item-checkbox').forEach(item => {
                    item.checked = isChecked;
                });
                // Đồng bộ trạng thái của cả 2 checkbox "Chọn tất cả"
                document.querySelectorAll('#selectAll, #selectAllBottom').forEach(cb => {
                    cb.checked = isChecked;
                });
            });
        });

        // Xử lý nút xóa các mục đã chọn
        document.querySelector('.btn-delete-selected')?.addEventListener('click', function () {
            const selectedItems = document.querySelectorAll('.item-checkbox:checked');
            if (selectedItems.length === 0) {
                alert('Vui lòng chọn sản phẩm cần xóa');
                return;
            }

            if (confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?')) {
                const promises = Array.from(selectedItems).map(checkbox => {
                    const variantId = checkbox.closest('.cart-item').dataset.variantId;
                    return fetch('/ecommerce-php/cart/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `variantId=${variantId}`
                    }).then(response => response.json());
                });

                Promise.all(promises).then(() => {
                    location.reload();
                });
            }
        });

        // Xử lý sự kiện nút tăng/giảm số lượng
        document.querySelectorAll('.btn-decrease, .btn-increase').forEach(button => {
            button.addEventListener('click', function () {
                const input = this.parentElement.querySelector('.quantity-input');
                const variantId = this.closest('.cart-item').dataset.variantId;
                let value = parseInt(input.value);

                if (this.classList.contains('btn-decrease')) {
                    value = Math.max(1, value - 1);
                } else {
                    value = Math.min(parseInt(input.max), value + 1);
                }

                input.value = value;
                updateQuantity(variantId, value);
            });
        });

        // Xử lý sự kiện thay đổi input số lượng
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function () {
                const variantId = this.closest('.cart-item').dataset.variantId;
                let value = parseInt(this.value);

                // Đảm bảo giá trị nằm trong khoảng cho phép
                value = Math.max(1, Math.min(parseInt(this.max), value));
                this.value = value;

                updateQuantity(variantId, value);
            });
        });

        // Xử lý sự kiện nút xóa sản phẩm
        document.querySelectorAll('.btn-remove').forEach(button => {
            button.addEventListener('click', function () {
                const variantId = this.closest('.cart-item').dataset.variantId;
                removeItem(variantId);
            });
        });

        // Hiển thị modal nếu không có địa chỉ
        <?php if (isset($_SESSION['no_address']) && $_SESSION['no_address']): ?>
            var modal = document.getElementById('addressModal');
            var span = document.getElementsByClassName('close')[0];
            var addAddressBtn = document.getElementById('addAddressBtn');

            // Hiển thị modal
            modal.style.display = 'block';

            // Khi người dùng click vào <span> (x), đóng modal
            span.onclick = function () {
                modal.style.display = 'none';
            }

            // Khi người dùng click vào nút "Thêm địa chỉ", điều hướng đến trang thêm địa chỉ
            addAddressBtn.onclick = function () {
                window.location.href = '/ecommerce-php/user/addresses';
            }

            // Khi người dùng click ra ngoài modal, đóng modal
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
            <?php unset($_SESSION['no_address']); endif; ?>
    });

    function processSelectedItems() {
        const selectedItems = document.querySelectorAll('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn sản phẩm để mua');
            return;
        }

        // Lấy danh sách variantId đã chọn
        const selectedVariantIds = Array.from(selectedItems).map(checkbox => {
            return checkbox.closest('.cart-item').dataset.variantId;
        });

        // Tạo form ẩn để submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/ecommerce-php/order/checkout';

        // Thêm input cho mỗi variantId
        selectedVariantIds.forEach(variantId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selectedItems[]';
            input.value = variantId;
            form.appendChild(input);
        });

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }

    // Cập nhật lại validateCart()
    function validateCart() {
        const selectedItems = document.querySelectorAll('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn sản phẩm để mua');
            return false;
        }
        return true;
    }

    // Thêm hàm tính tổng cho các sản phẩm được chọn
    function updateSelectedTotals() {
        let totalQuantity = 0;
        let totalAmount = 0;

        // Lấy tất cả checkbox đã chọn
        document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
            const item = checkbox.closest('.cart-item');
            // Lấy số lượng từ input
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            // Lấy giá từ span có class text-danger fw-bold
            const price = parseFloat(item.querySelector('.text-danger.fw-bold').textContent.replace(/[^\d]/g, ''));

            totalQuantity += quantity;
            totalAmount += price * quantity;
        });

        // Cập nhật UI
        document.querySelector('.total-quantity-text').textContent = totalQuantity;
        document.querySelector('.cart-total').textContent =
            new Intl.NumberFormat('vi-VN').format(totalAmount) + 'đ';
    }

    // Thêm event listener cho các checkbox
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTotals);
    });

    // Cập nhật khi thay đổi số lượng
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
            if (this.closest('.cart-item').querySelector('.item-checkbox').checked) {
                updateSelectedTotals();
            }
        });
    });

    // Cập nhật khi click "Chọn tất cả"
    document.querySelectorAll('#selectAll, #selectAllBottom').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const isChecked = this.checked;
            document.querySelectorAll('.item-checkbox').forEach(item => {
                item.checked = isChecked;
            });
            // Đồng bộ trạng thái của cả 2 checkbox "Chọn tất cả"
            document.querySelectorAll('#selectAll, #selectAllBottom').forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelectedTotals();
        });
    });

    // Khởi tạo tổng ban đầu
    updateSelectedTotals();
</script>

<style>
    /* CSS cho modal */
    .modal {
        display: none;
        /* Ẩn modal mặc định */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        /* Màu nền tối */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>